<?php

namespace App\Modules\Article\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Category\Models\Category;
use App\Modules\Attach\Models\Attach;

class Article extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'articles';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'admin_id',
		'category_id',
		'title',
		'ascii_title',
		'slug',
		'content',
		'preview',
		'thumbnail',
		'thumbnail_ratio',
		'public',
		'home',
		'order'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'ascii_title',
		'user_id',
		'admin_id',
		'content',
	];

	public static $fieldDescriptions = [
		'title' => 'str,required|max:250',
		'slug' => 'str,max:250',
		'category_id' => 'int',
		'public' => 'bool',
		'home' => 'bool',
		'content' => 'str',
		'order' => 'int',
	];

	public static $searchFields = ['title', 'ascii_title', 'preview'];

	public function category(){
		return $this->belongsTo('App\Modules\Category\Models\Category', 'category_id');
	}

	public function owner(){
		if($this->admin_id){
			return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
		}
		return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
	}

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = '-order';
			$orderBy = Tools::parseOrderBy($orderBy);
			$category = Category::find(isset($params['category_id'])?$params['category_id']:0);
			if(!$category){
				return ResTools::err(
					trans('messages.category_not_exist'),
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}

			$listItem = Article::where($params);
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
				'category' => $category,
				'allow_add' => ($category->single && count($category->articles) >0)?false:true
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
			$category = $result->category;

			// $category = $result->category;
			/* Make some field visible */
			$result->makeVisible('content');
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
			$category = Category::find($input['category_id']);
			if(!$category){
				return ResTools::err(
					trans('messages.category_not_exist'),
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}
			if($category->single && count($category->articles) >0){
				return ResTools::err(
					trans('messages.can_not_create_more_item'),
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}
			$input["ascii_title"] = Tools::niceUrl($input["title"], false);
			$input["slug"] = Tools::niceUrl($input["title"]);
			$item = self::create($input);

			$category = $item->category;
			$item->category_uid = $category->uid;
			$item->category_type = $category->type;
			$item->save();

			$item->allow_add = true;
			if($category->single && count($category->articles) >0){
				$item->allow_add = false;
			}
			return ResTools::obj($item, trans('messages.add_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function editItem($id, $input, $file){
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
			$thumbnail = Tools::uploadHandler($file, $item->thumbnail, 'thumbnail', 'thumbnail', 0, false, true);
			if(!$thumbnail['success'] && !$thumbnail['blank']){
				return ResTools::err(
					$thumbnail['message'],
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}

			# var_dump($thumbnail);die;
			$input["category_id"] = $item->category_id;
			$input["ascii_title"] = Tools::niceUrl($input["title"], false);
			$input["preview"] = Tools::getPreview($input["content"]);
			$input["thumbnail"] = $thumbnail["path"];
			$input["thumbnail_ratio"] = \Config::get('upload.golden_ratio');
			if(!$input['thumbnail']){
	        	unset($input['thumbnail']);
	        }
			$item->update($input);
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
						$result = [
							'id' => $successList,
							'allow_add' => $removeResult['data']['allow_add']
						];
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
				$result = [
					'id' => $successList,
					'allow_add' => $removeResult['data']['allow_add']
				];
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
			$result = [
				"id" => $id,
				"allow_add" => true,
			];
			if($item->category->single && count($item->category->articles) > 1){
				$result["allow_add"] = false;
			}
			Attach::removeByParent($item->id, 'articles', $force);
			if($force){
				$item->forceDelete();
			}else{
				$item->delete();
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
		}
		if(in_array('order', $colums)){
			if(!$this->order){
				$largestOrderItem = self::orderBy('order', 'desc')->first();
				if($largestOrderItem){
					$this->order = $largestOrderItem->order + 1;
				}else{
					$this->order = 1;
				}
			}
		}
		// before save code
		parent::save();
		// after save code
	}
}
