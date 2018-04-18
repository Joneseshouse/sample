<?php

namespace App\Modules\Category\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Dropdown\Models\Dropdown;
use App\Modules\Banner\Models\Banner;
use App\Modules\Article\Models\Article;

class Category extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'categories';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'uid',
		'title',
		'ascii_title',
		'single',
		'type',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'title' => 'str,required|max:150',
		'single' => 'bool,required',
		'type' => 'str,required|max:50',
	];

	public static $searchFields = ['uid', 'title', 'ascii_title'];

	public function dropdowns(){
		return $this->hasMany('App\Modules\Dropdown\Models\Dropdown', 'category_id');
	}

	public function articles(){
		return $this->hasMany('App\Modules\Article\Models\Article', 'category_id');
	}

	public function banners(){
		return $this->hasMany('App\Modules\Banner\Models\Banner', 'category_id');
	}

	public function user_accounts(){
		return $this->hasMany('App\Modules\UserAccount\Models\UserAccount', 'category_id');
	}

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = Category::where($params);
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
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}

			if($input["type"] !== 'article'){
				$input["single"] = false;
			}
			$input["uid"] = Tools::niceUrl($input["title"]);
			$input["ascii_title"] = Tools::niceUrl($input["title"], false);
			$checkDuplicate = self::where(["uid" => $input["uid"]])->first();
			if($checkDuplicate){
				return ResTools::err(
					trans('messages.duplicate_item'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			# var_dump($input);die;
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
			if($input["type"] !== 'article'){
				$input["single"] = false;
			}
			$input["uid"] = Tools::niceUrl($input["title"]);
			$input["ascii_title"] = Tools::niceUrl($input["title"], false);
			$checkDuplicate = self::where(["uid" => $input["uid"]])->first();
			if($checkDuplicate && $checkDuplicate->id !== $id){
				return ResTools::err(
					trans('messages.duplicate_item'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			$item->update($input);

			# Update related items
			$dropdowns = $item->dropdowns;
			$articles = $item->articles;
			$banners = $item->banners;
			foreach ($dropdowns as $subItem) {
				$subItem->category_uid = $item->uid;
				$subItem->category_type = $item->type;
				$subItem->save();
			}

			foreach ($articles as $subItem) {
				$subItem->category_uid = $item->uid;
				$subItem->category_type = $item->type;
				$subItem->save();
			}
			foreach ($banners as $subItem) {
				$subItem->category_uid = $item->uid;
				$subItem->category_type = $item->type;
				$subItem->save();
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

	public static function removeSingle($id, $force){
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

	public function delete(){
		# Remove related columns
		if(config('app.app_env') !== 'testing'){
			switch($this->type){
				case 'dropdown':
					$dropdowns = $this->dropdowns;
					foreach($dropdowns as $dropdown) Dropdown::removeItem($dropdown->id);
				break;
				case 'banner':
					$banners = $this->banners;
					foreach($banners as $banner) Banner::removeItem($banner->id);
				break;
				case 'article':
					$articles = $this->articles;
					foreach($articles as $article) Article::removeItem($article->id);
				break;
			}
		}

		return parent::delete();
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
