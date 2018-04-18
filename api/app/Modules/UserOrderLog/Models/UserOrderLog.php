<?php

namespace App\Modules\UserOrderLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Atoken\Models\Atoken;

class UserOrderLog extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'user_order_logs';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'uid',
		'user_id',
		'user_full_name',
		'admin_id',
		'admin_full_name',
		'order_id',
		'purchase_id',
		'is_normal',
		'target',
		'type',
		'content'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
	];

	public static $searchFields = ['uid'];

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = UserOrderLog::where($params);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			$listItem = $listItem->
				orderBy($orderBy[0], $orderBy[1])->
				paginate(config('app.page_size'));
			return ResTools::lst($listItem);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function obj($params=null, $original=false){
		try{
			$result = null;
			if($params === null){
				return $result;
			}
			if(gettype($params) === "integer"){
				$result = self::find($params);
			}else if(gettype($params) === "string"){
				$result = null;
			}else{
				if(count($params) === 1 && array_key_exists("id", $params)){
					$result = self::find(intval($params['id']));
				}else{
					$result = self::where($params)->first();
				}
			}
			if(!$result){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['NOT_FOUND']
				);
			}
			return ResTools::obj($result, $original);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function addItem($input){
		$handleError = function($e){
	        return ResTools::criticalErr(Tools::errMessage($e));
	    };
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}
			$item = self::create($input);
			return ResTools::obj($item, trans('messages.add_success'));
		}
		catch(\Exception $e){return $handleError($e);}
		catch(\Error $e){return $handleError($e);}
	}

	public static function addOrder($order){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $order->user_id,
			'user_full_name' => ($order->user_id && $order->user)?$order->user->full_name:null,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $order->id,
			'purchase_id' => null,
			'is_normal' => true,
			'target' => 'order',
			'type' => 'add',
			'content' => 'Tạo mới đơn hàng order: '.$order->uid
		];

		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		self::addItem($data);
	}

	public static function editOrder($order, $oldOrder, $executor){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $order->user_id,
			'user_full_name' => ($order->user_id && $order->user)?$order->user->full_name:null,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $order->id,
			'purchase_id' => null,
			'is_normal' => true,
			'target' => 'order',
			'type' => 'edit',
			'content' => '<div><strong>Sửa đơn hàng order: '.$order->uid.'</strong></div>'
		];
		$needToLog = false;
		if($executor->role->role_type_uid === 'user'){
			$data['content'] .= '<ul>';
			if($oldOrder->address_id !== $order->address_id){
				# Đổi địa chỉ
				$content = "
					<li>
						Địa chỉ: {$oldOrder->address->uid} &rarr;  {$order->address->uid}
					</li>
				";
				$data['content'] .= $content;
				$needToLog = true;
			}
			$data['content'] .= '</ul>';
		}else{
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
			$data['content'] .= '<ul>';
			if($oldOrder->address_id !== $order->address_id){
				# Đổi địa chỉ
				$content = "
					<li>
						Địa chỉ: {$oldOrder->address->uid} &rarr;  {$order->address->uid}
					</li>
				";
				$data['content'] .= $content;
				$needToLog = true;
			}
			if($oldOrder->admin_id !== $order->admin_id && $oldOrder->admin){
				# Đổi nhân viên mua hàng
				$oldAdmin = !$oldOrder->admin_id?'Chưa gán':$oldOrder->admin->full_name;
				$admin = !$order->admin_id?'Chưa gán':$order->admin->full_name;
				$content = "
					<li>
						N.V mua hàng: {$oldAdmin} &rarr;  {$admin}
					</li>
				";
				$data['content'] .= $content;
				$needToLog = true;

			}
			if($oldOrder->status !== $order->status){
				# Đổi trạng thái
				$statusRef = config('app.list_order_status_ref');
				$oldStatus = $statusRef[$oldOrder->status];
				$status = $statusRef[$order->status];
				$content = "
					<li>
						Trạng thái: {$oldStatus} &rarr;  {$status}
					</li>
				";
				$data['content'] .= $content;
				$needToLog = true;
			}
			$data['content'] .= '</ul>';
		}
		if($needToLog){
			self::addItem($data);
		}
	}

	public static function confirmOrder($order, $executor){
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $order->user_id,
			'user_full_name' => $order->user->full_name,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $order->id,
			'purchase_id' => null,
			'is_normal' => true,
			'target' => 'order',
			'type' => 'confirm',
			'content' => 'Duyệt order: '.$order->uid
		];
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}
		self::addItem($data);
	}

	public static function removeOrder($order, $executor){
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $order->user_id,
			'user_full_name' => $order->user->full_name,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $order->id,
			'purchase_id' => null,
			'is_normal' => true,
			'target' => 'order',
			'type' => 'remove',
			'content' => 'Xoá order: '.$order->uid
		];
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}
		self::addItem($data);
	}

	public static function addPurchase($purchase){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $purchase->user_id,
			'user_full_name' => $purchase->user->full_name,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $purchase->order_id,
			'purchase_id' => $purchase->id,
			'is_normal' => true,
			'target' => 'purchase',
			'type' => 'add',
			'content' => 'Tạo shop order: '.$purchase->order->uid.' &rarr; '.$purchase->shop->title
		];
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		self::addItem($data);
	}

	public static function editPurchase($purchase, $oldPurchase, $executor){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $purchase->user_id,
			'user_full_name' => $purchase->user->full_name,
			'admin_id' => $executor->id,
			'admin_full_name' => $executor->full_name,
			'order_id' => $purchase->order_id,
			'purchase_id' => $purchase->id,
			'is_normal' => true,
			'target' => 'purchase',
			'type' => 'edit',
			'content' => '<div><strong>Sửa shop order: '.$purchase->order->uid.' &rarr; '.$purchase->shop->title.'</strong></div>'
		];
		$needToLog = false;
		$data['content'] .= '<ul>';

		if($oldPurchase->delivery_fee_unit !== $purchase->delivery_fee_unit){
			# Đơn giá vận chuyển
			$content = "
				<li>
					Đơn giá vận chuyển: {$oldPurchase->delivery_fee_unit} &rarr;  {$purchase->delivery_fee_unit}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}
		if($oldPurchase->inland_delivery_fee !== $purchase->inland_delivery_fee){
			# Đơn giá vận chuyển
			$content = "
				<li>
					Vận chuyển nội địa: {$oldPurchase->inland_delivery_fee} &rarr;  {$purchase->inland_delivery_fee}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}
		if($oldPurchase->real_amount !== $purchase->real_amount){
			# Đơn giá vận chuyển
			$content = "
				<li>
					Thanh toán thực: {$oldPurchase->real_amount} &rarr;  {$purchase->real_amount}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}
		if($oldPurchase->code !== $purchase->code){
			# Đơn giá vận chuyển
			$content = "
				<li>
					Mã giao dịch: {$oldPurchase->code} &rarr;  {$purchase->code}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		$data['content'] .= '</ul>';

		if($needToLog){
			self::addItem($data);
		}
	}

	public static function removePurchase($purchase, $executor){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $purchase->user_id,
			'user_full_name' => $purchase->user->full_name,
			'admin_id' => $executor->id,
			'admin_full_name' => $executor->full_name,
			'order_id' => $purchase->order_id,
			'purchase_id' => $purchase->id,
			'is_normal' => true,
			'target' => 'purchase',
			'type' => 'remove',
			'content' => 'Xoá shop order: '.$purchase->order->uid.' &rarr; '.$purchase->shop->title
		];
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		self::addItem($data);
	}

	public static function addOrderItem($orderItem){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $orderItem->order->user_id,
			'user_full_name' => $orderItem->order->user->full_name,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $orderItem->order_id,
			'purchase_id' => $orderItem->purchase_id,
			'is_normal' => true,
			'target' => 'orderItem',
			'type' => 'add',
			'content' => 'Tạo mới mặt hàng: '.$orderItem->order->uid.' &rarr; '.$orderItem->shop->title.' &rarr; '.$orderItem->title
		];
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		self::addItem($data);
	}

	public static function editOrderItem($orderItem, $oldOrderItem, $executor){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $orderItem->order->user_id,
			'user_full_name' => $orderItem->order->user->full_name,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $orderItem->order_id,
			'purchase_id' => $orderItem->purchase_id,
			'is_normal' => true,
			'target' => 'orderItem',
			'type' => 'edit',
			'content' => '<div><strong>Sửa mặt hàng: '.$orderItem->order->uid.' &rarr; '.$orderItem->shop->title.' &rarr; '.$orderItem->title.'</strong></div>'
		];
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}

		$needToLog = false;
		$data['content'] .= '<ul>';

		if($oldOrderItem->quantity !== $orderItem->quantity){
			# Số lượng
			$content = "
				<li>
					Số lượng: {$oldOrderItem->quantity} &rarr;  {$orderItem->quantity}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}
		if($oldOrderItem->properties !== $orderItem->properties){
			# Mô tả
			$content = "
				<li>
					Mô tả: {$oldOrderItem->properties} &rarr;  {$orderItem->properties}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}
		if($oldOrderItem->message !== $orderItem->message){
			# Ghi chú
			$content = "
				<li>
					Ghi chú: {$oldOrderItem->message} &rarr;  {$orderItem->message}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		$data['content'] .= '</ul>';
		if($needToLog){
			self::addItem($data);
		}
	}

	public static function removeOrderItem($orderItem, $executor){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $orderItem->order->user_id,
			'user_full_name' => $orderItem->order->user->full_name,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $orderItem->order_id,
			'purchase_id' => $orderItem->purchase_id,
			'is_normal' => true,
			'target' => 'orderItem',
			'type' => 'remove',
			'content' => 'Xoá mặt hàng: '.$orderItem->order->uid.' &rarr; '.$orderItem->shop->title.' &rarr; '.$orderItem->title
		];
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}
		self::addItem($data);
	}

	public static function addBillOfLanding($billOfLanding, $executor){
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $billOfLanding->user_id,
			'user_full_name' => $billOfLanding->user?$billOfLanding->user->full_name:null,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $billOfLanding->order_id,
			'purchase_id' => $billOfLanding->purchase_id,
			'is_normal' => $billOfLanding->order?true:false,
			'target' => 'billOfLanding',
			'type' => 'add',
		];
		if($billOfLanding->order){
			$data['content'] = 'Tạo mới vận đơn: '.$billOfLanding->order->uid.' &rarr; '.$billOfLanding->purchase->code.' &rarr; '.$billOfLanding->code;
		}else{
			$data['content'] = 'Tạo mới vận đơn: '.$billOfLanding->code;
		}
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}
		self::addItem($data);
	}

	public static function editBillOfLanding($billOfLanding, $oldBillOfLanding, $executor){
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $billOfLanding->user_id,
			'user_full_name' => $billOfLanding->user?$billOfLanding->user->full_name:null,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $billOfLanding->order_id,
			'purchase_id' => $billOfLanding->purchase_id,
			'is_normal' => $billOfLanding->order?true:false,
			'target' => 'billOfLanding',
			'type' => 'add',
		];
		if($billOfLanding->order){
			$data['content'] = 'Tạo mới vận đơn: '.$billOfLanding->order->uid.' &rarr; '.$billOfLanding->purchase->code.' &rarr; '.$billOfLanding->code;
		}else{
			$data['content'] = 'Tạo mới vận đơn: '.$billOfLanding->code;
		}
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}

		$needToLog = false;
		$data['content'] .= '<ul>';

		if($oldBillOfLanding->code !== $billOfLanding->code){
			# Mã vận đơn
			$content = "
				<li>
					Mã vận đơn: {$oldBillOfLanding->code} &rarr;  {$billOfLanding->code}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->address_id !== $billOfLanding->address_id){
			# Địa chỉ
			$content = "
				<li>
					Địa chỉ: {$oldBillOfLanding->address->uid} &rarr;  {$billOfLanding->address->uid}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->packages !== $billOfLanding->packages){
			# Số kiện
			$content = "
				<li>
					Số kiện: {$oldBillOfLanding->packages} &rarr;  {$billOfLanding->packages}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->mass !== $billOfLanding->mass){
			# Khối lượng
			$content = "
				<li>
					Khối lượng: {$oldBillOfLanding->mass} &rarr;  {$billOfLanding->mass}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->length !== $billOfLanding->length){
			# Dài
			$content = "
				<li>
					Dài: {$oldBillOfLanding->length} &rarr;  {$billOfLanding->length}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->width !== $billOfLanding->width){
			# Rộng
			$content = "
				<li>
					Rộng: {$oldBillOfLanding->width} &rarr;  {$billOfLanding->width}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->height !== $billOfLanding->height){
			# Cao
			$content = "
				<li>
					Cao: {$oldBillOfLanding->height} &rarr;  {$billOfLanding->height}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->insurance_register !== $billOfLanding->insurance_register){
			# Đăng ký bảo hiểm
			$oldInsuranceRegister = $oldBillOfLanding->insurance_register?'Có':'Không';
			$insuranceRegister = $billOfLanding->insurance_register?'Có':'Không';
			$content = "
				<li>
					Đăng ký bảo hiểm: {$oldInsuranceRegister} &rarr;  {$insuranceRegister}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		if($oldBillOfLanding->insurance_value !== $billOfLanding->insurance_value){
			# Giá trị bảo hiểm
			$content = "
				<li>
					Giá trị bảo hiểm: {$oldBillOfLanding->insurance_value} &rarr;  {$billOfLanding->insurance_value}
				</li>
			";
			$data['content'] .= $content;
			$needToLog = true;
		}

		$data['content'] .= '</ul>';
		if($needToLog){
			self::addItem($data);
		}
	}

	public static function removeBillOfLanding($billOfLanding, $executor){
		# User only
		$data = [
			'uid' => str_random(8).Tools::nowDateTime()->timestamp,
			'user_id' => $billOfLanding->user_id,
			'user_full_name' => $billOfLanding->user?$billOfLanding->user->full_name:null,
			'admin_id' => null,
			'admin_full_name' => null,
			'order_id' => $billOfLanding->order_id,
			'purchase_id' => $billOfLanding->purchase_id,
			'is_normal' => $billOfLanding->order?true:false,
			'target' => 'billOfLanding',
			'type' => 'remove',
		];
		if($billOfLanding->order && $billOfLanding->purchase){
			$data['content'] = 'Xoá vận đơn: '.$billOfLanding->order->uid.' &rarr; '.$billOfLanding->purchase->code.' &rarr; '.$billOfLanding->code;
		}else{
			$data['content'] = 'Xoá vận đơn: '.$billOfLanding->code;
		}
		$data['content'] = "<div><strong>{$data['content']}</strong></div>";
		if($executor->role->role_type_uid !== 'user'){
			$data['admin_id'] = $executor->id;
			$data['admin_full_name'] = $executor->full_name;
		}
		self::addItem($data);
	}

	public static function editItem($id, $input){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}

			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			$item->update($input);
			return ResTools::obj($item, trans('messages.edit_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeItem($id, $force=false){
		try{
			$result = null;
			$listId = explode(',', $id);
			foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

			$listItem = self::whereIn('id', $listId)->get();
			if($listItem->count()){
				foreach ($listItem as $item) {
					if($force){
						$item->forceDelete();
					}else{
						$item->delete();
					}
				}
				$result = ['id' => count($listId)>1?$listId:$listId[0]];
			}else{
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			return ResTools::obj($result, trans('messages.remove_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public function save(array $options = array()){
		# Auto increase order (if order exist)
		$item = $this;
        \DB::transaction(function() use($item){
	        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($item->getTable());
			if(in_array('updated_at', $colums)){
				$item->updated_at = Tools::nowDateTime();
			}
			if(!$item->exists){
				if(in_array('created_at', $colums)){
					$item->created_at = Tools::nowDateTime();
				}
				if(self::withTrashed()->count() > 0){
					$largestIdItem = self::withTrashed()->orderBy('id', 'desc')->first();
					$item->id = $largestIdItem->id + 1;
				}else{
					$item->id = 1;
				}
				if(in_array('order', $colums)){
					if($item->order === 0){
						$largestOrderItem = self::orderBy('order', 'desc')->first();
						if($largestOrderItem){
							$item->order = $largestOrderItem->order + 1;
						}else{
							$item->order = 1;
						}
					}
				}
			}
			// before save code
			parent::save();
			// after save code
        }, 20);
	}
}
