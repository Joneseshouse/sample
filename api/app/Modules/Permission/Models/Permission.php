<?php

namespace App\Modules\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Atoken\Models\Atoken;
use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;

class Permission extends Model{
	 use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'permissions';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'title',
		'ascii_title',
		'action',
		'route',
		'module',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'title' => 'str|max:250',
		'action' => 'str,required|max:250',
	];

	public static $searchFields = ['title', 'ascii_title', 'action'];

	public static function syncList(){
		try{
			# Preserve when sync
			# Remove all then insert again.
			# REMOVE ALL
			$oldPermissions = [];
			$listPermissions = self::all();
			foreach($listPermissions as $item){
				$route = [
					'route' => $item->route,
					'module' => $item->module,
					'title' => $item->title,
					'ascii_title' => $item->ascii_title,
					'action' => $item->action
				];
				$oldPermissions[$route['route']] = $route;
				$item->delete();
			}
			$listRoute = Tools::getListRoute();
			foreach ($listRoute as $route) {
				# code...
				$input = [
					'route' => $route['route'],
					'module' => $route['module'],
					'title' => $route['module'],
					'ascii_title' => $route['module'],
					'action' => $route['label']
				];
				if(array_key_exists($input['route'], $oldPermissions)){
					$oldPermission = $oldPermissions[$input['route']];
					$input['title'] = $oldPermission['title'];
					$input['ascii_title'] = $oldPermission['ascii_title'];
					$input['action'] = $oldPermission['action'];
				}
				self::create($input);
			}
			$listItem = self::orderBy(array_key_exists('order', self::$fieldDescriptions)?'order':'ascii_title','asc')->get();
			return ResTools::lst($listItem);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function list($params=[], $keyword=null){
		try{
			$listItem = Permission::select('id', 'action', 'title', 'route', 'module')->where($params);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			$listItem = $listItem->
				orderBy(array_key_exists('order', self::$fieldDescriptions)?'order':'ascii_title','asc')->
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
			$oldTitle = $item->title;
			$newTitle = $input['title'];
			$input['ascii_title'] = Tools::niceUrl($input['title'], false);
			$item->update($input);

			if($oldTitle !== $newTitle){
				$relatedItems = self::where('module', $item->module)->get();
				foreach ($relatedItems as $relatedItem) {
					$relatedItem->title = $item->title;
					$relatedItem->ascii_title = $item->ascii_title;
					$relatedItem->save();
				}
			}
			return ResTools::obj($item, trans('messages.edit_success'));
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
