<?php

namespace App\Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Permission\Models\Permission;
use App\Modules\RoleType\Models\RoleType;

class Role extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'roles';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'uid',
		'role_type_id',
		'role_type_uid',
		'title',
		'detail',
		'default_role'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'role_type_id' => 'int,required',
		'title' => 'str,required|max:250',
		'detail' => 'str,required',
		'default_role' => 'bool',
	];

	private static $parents = ['admins', 'users'];

	public static $searchFields = ['uid', 'title'];

	public function admins(){
		return $this->hasMany('App\Modules\Admin\Models\Admin', 'role_id');
	}

	public function users(){
		return $this->hasMany('App\Modules\User\Models\User', 'role_id');
	}

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$roleType = RoleType::find(isset($params['role_type_id'])?$params['role_type_id']:0);
			if(!$roleType){
				return ResTools::err(
					trans('messages.role_type_not_exist'),
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}
			$listItem = Role::where($params);
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
			$extra = [
				'list_route' => Permission::orderBy('ascii_title', 'asc')->get(),
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

			$input['uid'] = Tools::niceUrl($input['title']);
			$checkDuplicate = self::where(["uid" => $input["uid"]])->first();
			if($checkDuplicate){
				return ResTools::err(
					trans('messages.duplicate_item'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			$input['role_type_uid'] = RoleType::find($input['role_type_id'])->uid;
			$listItem = self::where('role_type_uid', $input['role_type_uid'])->get();
			if(array_key_exists('default_role', $input) && $input['default_role']){
				foreach ($listItem as $role) {
					$role->default_role = false;
					$role->save();
				}
			}
			if(count($listItem) === 0){
				$input['default_role'] = true;
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
			$input['uid'] = Tools::niceUrl($input['title']);
			$checkDuplicate = self::where(["uid" => $input["uid"]])->first();
			if($checkDuplicate && $checkDuplicate->id !== $id){
				return ResTools::err(
					trans('messages.duplicate_item'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			$listItem = self::where('role_type_id', $item->role_type_id)->get();
			if($listItem->count() > 1){
				if(!$item->default_role & array_key_exists('default_role', $input) && $input['default_role']){
					foreach ($listItem as $role) {
						$role->default_role = false;
						$role->save();
					}
					$input['default_role'] = true;
				}else{
					unset($input['default_role']);
				}
			}else{
				$input['default_role'] = true;
			}


			$item->update($input);

			/* Update permissions */
			foreach (self::$parents as $parentStr) {
				$listParent = $item->$parentStr;
				foreach ($listParent as $parent) {
					$parent->permissions = $item->detail;
					$parent->save();
				}
			}
			return ResTools::obj($item, trans('messages.edit_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeItem($id, $force=false){
		try{
			$listId = explode(',', $id);
			$total = count($listId);
			if($total > 1){
				# Remove all
				$successList = [];
				foreach ($listId as $id) {
					# code...
					$id = ValidateTools::toInt($id);
					$removeResult = self::removeSingle($id, $force);
					if($removeResult['success']){
						array_push($successList, $id);
					}else{
						$result = ['id' => $successList];
						return ResTools::obj(
							$result,
							trans('messages.remove_all_message', [
									'count' => count($successList),
									'total' => $total
								]
							)
						);
					}
				}
				$result = ['id' => $successList];
				return ResTools::obj($result, trans('messages.remove_success'));
			}

			# Remove single object
			$id = ValidateTools::toInt($id);
			return self::removeSingle($id, $force);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeSingle($id, $force=false){
		try{
			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			if($force){
				$item->forceDelete();
			}else{
				$item->delete();
			}
			$result = ["id" => $id];
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
			/*
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
			*/
		}
		// before save code
		parent::save();
		// after save code
	}
}
