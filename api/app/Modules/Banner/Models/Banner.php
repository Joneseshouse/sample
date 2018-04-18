<?php

namespace App\Modules\Banner\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Category\Models\Category;

class Banner extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'banners';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'category_id',
		'title',
		'subtitle',
		'url',
		'image',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'category_id' => 'int',
		'title' => 'str,required|max:150',
		'subtitle' => 'str,required|max:150',
		'url' => 'str,max:255',
		'image' => 'str',
	];

	public static $searchFields = ['title', 'subtitle'];

	public function category(){
        return $this->belongsTo('App\Modules\Category\Models\Category', 'category_id');
    }

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$category = Category::find(isset($params['category_id'])?$params['category_id']:0);
			if(!$category){
				return ResTools::err(
					trans('messages.category_not_exist'),
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}

			$listItem = Banner::where($params);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			$extra = [
				'allow_add' => true,
				'category_id' => $category->id,
				'category_title' => $category->title,
				'category_type' => $category->type,
				'category_single' => $category->single,
			];
			$listItem = $listItem->
				orderBy($orderBy[0], $orderBy[1])->
				paginate(config('app.page_size'));
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

	public static function addItem($input, $file){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}
			$image = Tools::uploadHandler($file, null, 'image', 'banner');
			if(!$image['success'] && !$image['blank']){
				return ResTools::err(
					$image['message'],
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}
	        $input['image'] = $image['path'];
	        if(!$input['image']){
	        	return ResTools::err(
					trans('messages.missing_image'),
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
	        }
			# var_dump($input);
			$item = self::create($input);
			$category = $item->category;
			$item->category_uid = $category->uid;
			$item->category_type = $category->type;
			$item->save();
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
			$item = self::find(ValidateTools::toInt($id));
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
	        $image = Tools::uploadHandler($file, $item->image, 'image', 'banner');
			if(!$image['success'] && !$image['blank']){
				return ResTools::err(
					$image['message'],
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}
	        $input['image'] = $image['path'];
	        if(!$input['image']){
	        	unset($input['image']);
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
			$image = $item->image;
			if($force){
				$item->forceDelete();
				$existFile = \Config::get('app.media_root').$image;
	            if($image && file_exists($existFile)){
	                unlink($existFile);
	            }
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
