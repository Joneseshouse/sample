<?php

namespace App\Modules\ChatLost\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Atoken\Models\Atoken;

class ChatLost extends Model{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'chat_lost';
	protected $appends = [
		'admin_full_name',
		'user_full_name'
	];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'admin_id',
		'user_id',
		'lost_id',
		'message'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'user',
		'admin'
	];

	public static $fieldDescriptions = [
		'message' => 'str,required|max:500',
		'lost_id' => 'int, required',
		'admin_id' => 'int',
		'user_id' => 'int'
	];

	public static $searchFields = ['admin_id', 'lost_id', 'user_id'];
	
	public function lost(){
		return $this->belongsTo('App\Modules\Lost\Models\Lost', 'lost_id');
	}
	
	public function admin(){
		return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
	}

	public function user(){
		return $this->belongsTo('App\Modules\Admin\Models\Admin', 'user_id');
	}

	public function getAdminFullNameAttribute($value){
		if($this->admin_id && $this->admin){
			return $this->admin->full_name;
		}
 	}

 	public function getUserFullNameAttribute($value){
 		if($this->user_id && $this->user){
 			return $this->user->full_name;
 		}
 	}

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = ChatLost::where($params);
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

	public static function get($key, $default=''){
		try{
			$result = self::where('uid', $key)->first();
			if($result){
				return $result->value;
			}
			return $default;
		}
		catch(\Exception $e){return null;}
		catch(\Error $e){return null;}
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

	public static function addItem($input, $token){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}
		
			$user = Atoken::where('token', $token)->get()->first();
			
			if($user && $user['role_type'] === 'admin'){
				$input['admin_id'] = $user['admin_id'];
			}
			if($user && $user['role_type'] === 'user'){
				$input['user_id'] = $user['user_id'];
			}
			$item = self::create($input);
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

			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			$checkDuplicate = self::where(["uid" => $input["uid"]])->first();
			if($checkDuplicate && $checkDuplicate->id !== $id){
				return ResTools::err(
					trans('messages.duplicate_item'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			$item->update($input);
			return ResTools::obj($item, trans('messages.edit_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeItem($id){
		try{
			$result = null;
			$listId = explode(',', $id);
			foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

			$listItem = self::whereIn('id', $listId)->get();
			if($listItem->count()){
				foreach ($listItem as $item) {
					$item->delete();
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
			if(self::count() > 0){
				$largestIdItem = self::orderBy('id', 'desc')->first();
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
