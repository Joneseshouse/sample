<?php

namespace App\Modules\Receipt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;

class Receipt extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'receipts';
	public $timestamps = false;
	protected $appends = [
		'admin_fullname',
		'user_fullname',
		'user_address'
	];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'uid',
		'user_id',
		'admin_id',
		'amount',
		'note'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'user_id' => 'int,required',
		'amount' => 'int,required',
		'note' => 'str,required'
	];

	public static $searchFields = ['uid', 'note'];

	public function admin(){
		return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
	}

	public function user(){
		return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
	}

	public function userTransactions(){
		return $this->hasMany('App\Modules\UserTransaction\Models\UserTransaction', 'receipt_id');
	}

	public function getAdminFullNameAttribute($value){
    	if($this->admin_id && $this->admin){
	        return $this->admin->first_name . ' ' . $this->admin->last_name;
    	}
    	return null;
    }

    public function getUserFullNameAttribute($value){
    	if($this->user_id && $this->user){
	        return $this->user->first_name . ' ' . $this->user->last_name;
    	}
    	return null;
    }

    public function getUserAddressAttribute($value){
    	if($this->user_id && $this->user){
	        return $this->user->address;
    	}
    	return null;
    }

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = Receipt::where([]);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}

			if(array_key_exists('admin_id', $params) && $params['admin_id']){
				$listItem->where('admin_id', $params['admin_id']);
			}

			if(array_key_exists('user_id', $params) && $params['user_id']){
				$listItem->where('user_id', $params['user_id']);
			}

			if(array_key_exists('uid', $params) && $params['uid']){
				$listItem->where('uid', $params['uid']);
			}

			if(array_key_exists('have_transaction', $params) && $params['have_transaction']){
				if($params['have_transaction'] === 'yes'){
					$listItem->has('userTransactions');
				}else{
					$listItem->has('userTransactions', '<', 1);
				}
			}

			if(array_key_exists('money_type', $params) && $params['money_type']){
				$listItem->whereHas('userTransactions', function($q) use($params){
					$q->where('money_type', $params['money_type']);
				});
			}

			if(array_key_exists('from_amount', $params) & array_key_exists('to_amount', $params)){
				$fromAmount = $params['from_amount'];
				$toAmount = $params['to_amount'];
				if($fromAmount && $toAmount){
					$listItem->whereBetween('amount', [$fromAmount, $toAmount]);
				}
			}

			if(array_key_exists('date_range', $params) && $params['date_range']){
				$dateRange = explode(',', $params['date_range']);
				$fromDate = ValidateTools::toDate($dateRange[0]);
				$toDate = ValidateTools::toDate($dateRange[1]);
				$listItem->
					whereDate('created_at', '>=', $fromDate)->
					whereDate('created_at', '<=', $toDate);
			}

			if(array_key_exists('note', $params) && $params['note']){
				$keyword = $params['note'];
				if($keyword && strlen($keyword) >=3){
					$listItem->where('note', 'ilike', '%' . $keyword . '%');
				}
			}

			$total = [
				'amount' => $listItem->sum('amount')
			];
			$listItem = $listItem->
				orderBy($orderBy[0], $orderBy[1])->
				paginate(config('app.page_size'));

			$extra = [
				'total' => $total,
				'list_admin' => Admin::select('id', 'first_name', 'last_name')->orderBy('last_name', 'asc')->get(),
				'list_user' => User::select('id', 'uid', 'first_name', 'last_name')->orderBy('last_name', 'asc')->get(),
			];
			$extra['list_admin'] = Admin::select('id', 'first_name', 'last_name')->orderBy('last_name', 'asc')->get();
			$extra['list_user'] = User::select('id', 'uid', 'first_name', 'last_name', 'email', 'phone')->orderBy('id', 'asc')->get();
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
			$extra = [
				'company' => \ConfigDb::get('default-title'),
				'address' => \ConfigDb::get('contact-address')
			];
			$result->contact = [
				'company' => \ConfigDb::get('contact-cong-ty'),
				'address' => \ConfigDb::get('contact-dia-chi'),
				'website' => \ConfigDb::get('contact-website'),
				'phone' => \ConfigDb::get('contact-phone'),
				'email' => \ConfigDb::get('contact-email'),
			];
			$result->address = \ConfigDb::get('contact-address');
			return ResTools::obj($result, $original, $extra);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function addItem($input, $executor){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}
			if(!User::find($input['user_id'])){
				return ResTools::err(
					'Khách hàng này không tồn tại, bạn vui lòng chọn lại.',
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			$today = Tools::nowDate();
			$uidPrefix = $today->year.($today->month>9?$today->month:'0'.$today->month).($today->day>9?$today->day:'0'.$today->day).'-'.$executor->id.'-';
			$input['admin_id'] = $executor->id;
			$input['uid'] = str_random(48);
			$item = self::create($input);
			$item->uid = $uidPrefix.$item->id;
			$item->save();
			return ResTools::obj($item, trans('messages.add_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function editItem($id, $input, $executor){
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
			if($input['user_id'] !== $item->user_id){
				return ResTools::err(
					'Bạn không thể đổi tên khách hàng trong phiếu thu.',
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			$input['admin_id'] = $executor->id;
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
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
		if(in_array('updated_at', $colums)){
			$this->updated_at = Tools::nowDateTime();
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
