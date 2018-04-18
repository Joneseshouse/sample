<?php

namespace App\Modules\CollectBol\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\Order\Models\Order;
use App\Modules\Admin\Models\Admin;

class CollectBol extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'collect_bols';
	protected $appends = [
		'admin_full_name'
	];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'admin_id',
		'purchase_code',
		'bill_of_landing_code',
		'real_amount',
		'note',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'admin'
	];

	public static $fieldDescriptions = [
		'purchase_code' => 'str,required|max:50',
		'bill_of_landing_code' => 'str,max:50',
		'real_amount' => 'float',
		'note' => 'str,max:250',
	];

	public static $searchFields = ['purchase_code', 'bill_of_landing_code'];

	public function admin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
    }

	public function getAdminFullNameAttribute($value){
    	if($this->admin_id){
	        return $this->admin->first_name . ' ' . $this->admin->last_name;
    	}
    	return null;
    }

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$startDate = null;
			$endDate = null;
			if(array_key_exists('start_date', $params)){
				$startDate = $params['start_date'];
				unset($params['start_date']);
			}
			if(array_key_exists('end_date', $params)){
				$endDate = $params['end_date'];
				unset($params['end_date']);
			}

			if(array_key_exists('admin_id', $params)){
				$params['admin_id'] = ValidateTools::toInt($params['admin_id']);
			}
			if(array_key_exists('admin_id', $params) && !$params['admin_id']){
				unset($params['admin_id']);
			}
			$startDate = ValidateTools::toDate($startDate);
			$endDate = ValidateTools::toDate($endDate);

			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = CollectBol::where($params);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			if($startDate && $endDate){
				$listItem->
					whereDate('created_at', '>=', $startDate)->
					whereDate('created_at', '<=', $endDate);
			}
			$listItem = $listItem->
				orderBy($orderBy[0], $orderBy[1])->
				paginate(config('app.page_size'));
			$extra = [
				'list_admin' => Admin::
					select('id', 'first_name', 'last_name')->
					orderBy('last_name', SORT_NATURAL|SORT_FLAG_CASE)->
					get()
			];
			return ResTools::lst($listItem, $extra);
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
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}

			$fromClient = true;
			if(array_key_exists('from_client', $input)){
				$fromClient = $input['from_client'];
				unset($input['from_client']);
			}

			$existItem = self::
				where(["purchase_code" => $input["purchase_code"]])->
				where(["bill_of_landing_code" => strtoupper($input["bill_of_landing_code"])])->
				first();
			if($existItem){
				if($fromClient){
					return ResTools::err(
						trans('messages.duplicate_item'),
						ResTools::$ERROR_CODES['BAD_REQUEST']
					);
				}
				$item = $existItem->update($input);
			}else{
				$item = self::create($input);
			}

			$purchase = Purchase::where('code', $item->purchase_code)->first();
			$billOfLanding = BillOfLanding::where('code', $item->bill_of_landing_code)->first();
			if($purchase){
				$purchase->real_amount = $item->real_amount;
				$purchase->save();
				Order::recalculate($purchase->order_id);
				if($billOfLanding){
					$billOfLanding->user_id = $purchase->user_id;
					$billOfLanding->purchase_id = $purchase->id;
					$billOfLanding->order_id = $purchase->order_id;
					$billOfLanding->address_id = $purchase->order->address_id;
					$billOfLanding->rate = $purchase->order->rate;
					$billOfLanding->save();
					BillOfLanding::recalculate($billOfLanding);

					$item->match = true;
					$item->save();
				}
			}

			return ResTools::obj($item, trans('messages.add_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function editItem($id, $input){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}

			$fromClient = true;
			if(array_key_exists('from_client', $input)){
				$fromClient = $input['from_client'];
				unset($input['from_client']);
			}

			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			if($item->match){
				return ResTools::err(
					'Mã giao dịch và mã vận đơn đã khớp. Bạn không thể sửa.',
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			$existItem = self::
				where(["purchase_code" => $input["purchase_code"]])->
				where(["bill_of_landing_code" => strtoupper($input["bill_of_landing_code"])])->
				first();
			if($fromClient && $existItem && $existItem->id !== $id){
				if($fromClient){
					return ResTools::err(
						trans('messages.duplicate_item'),
						ResTools::$ERROR_CODES['BAD_REQUEST']
					);
				}
			}
			if($existItem && $existItem->id !== $id){
				$existItem->delete();
			}
			$item->update($input);

			if(!$item->match){
				$purchase = Purchase::where('code', $item->purchase_code)->first();
				$billOfLanding = BillOfLanding::where('code', $item->bill_of_landing_code)->first();
				if($purchase){
					$purchase->real_amount = $item->real_amount;
					$purchase->save();
					Order::recalculate($purchase->order_id);
					if($billOfLanding){
						$billOfLanding->user_id = $purchase->user_id;
						$billOfLanding->purchase_id = $purchase->id;
						$billOfLanding->order_id = $purchase->order_id;
						$billOfLanding->address_id = $purchase->order->address_id;
						$billOfLanding->rate = $purchase->order->rate;
						$billOfLanding->save();

						$item->match = true;
						$item->save();
					}
				}
			}
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
					if($item->match){
						$purchase = Purchase::where('code', $item->purchase_code)->first();
						$bol = BillOfLanding::where('code', $item->bill_of_landing_code)->first();
						if($purchase && $bol){
							return ResTools::err(
								'Mã giao dịch và mã vận đơn đã khớp. Bạn không thể xoá.',
								ResTools::$ERROR_CODES['BAD_REQUEST']
							);
						}
					}
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
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
		if(in_array('updated_at', $colums)){
			$this->updated_at = Tools::nowDateTime();
		}
		if($this->bill_of_landing_code){
			$this->bill_of_landing_code = strtoupper($this->bill_of_landing_code);
		}
		if(!$this->exists){
			if(in_array('created_at', $colums)){
				$this->created_at = Tools::nowDateTime();
			}
			if(self::withTrashed()->count() > 0){
				$largestIdItem = self::withTrashed()->orderBy('id', 'desc')->first();
				$this->id = $largestIdItem->id + 1;
			}else{
				$this->id = 1;
			}
			if(in_array('order', $colums)){
				if($this->order === 0){
					$largestOrderItem = self::orderBy('order', 'desc')->first();
					if($largestOrderItem){
						$this->order = $largestOrderItem->order + 1;
					}else{
						$this->order = 1;
					}
				}
			}
		}
		// before save code
		parent::save();
		// after save code
	}
}
