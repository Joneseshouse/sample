<?php

namespace App\Modules\Dropdown\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Atoken\Models\Atoken;

class Dropdown extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'dropdowns';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'title',
		'ascii_title',
		'category_id',
		'value',
		'order',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'ascii_title',
		'category_id',
	];

	public static $fieldDescriptions = [
		'title' => 'str,required|max:150',
		'value' => 'str,required|max:150',
		'category_id' => 'int,required',
		'order' => 'int,required',
	];

	public static $searchFields = ['title', 'ascii_title', 'value'];

	public function category(){
        return $this->belongsTo('App\Modules\Category\Models\Category', 'category_id');
    }

	public static function list($params=[], $keyword=null){
		try{
			$listItem = Dropdown::where($params);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			$listItem = $listItem->
				orderBy(array_key_exists('order', self::$fieldDescriptions)?'order':'id','desc')->
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
					ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
				);
			}
			return ResTools::obj($result, $original);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function addItem($input){
		try{
			$input["ascii_title"] = Tools::niceUrl($input["title"], false);
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

	public static function editItem($id, $input){
		try{
			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			$input["ascii_title"] = Tools::niceUrl($input["title"], false);
			$item->update($input);
			return ResTools::obj($item, trans('messages.edit_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeItem($id){
		try{
			$listId = explode(',', $id);
			$total = count($listId);
			if($total > 1){
				# Remove all
				$successList = [];
				foreach ($listId as $id) {
					# code...
					$id = ValidateTools::toInt($id);
					$removeResult = self::removeSingle($id);
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
			return self::removeSingle($id);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeSingle($id){
		try{
			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			$item->delete();
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
