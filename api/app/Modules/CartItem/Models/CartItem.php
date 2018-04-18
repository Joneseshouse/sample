<?php

namespace App\Modules\CartItem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\RateLog\Models\RateLog;

class CartItem extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'cart_items';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		"user_id",
		"title",
		"properties",
		"quantity",
		"shop_name",
		"shop_uid",
		"avatar",
		"unit_price",
		"message",
		"url",
		"vendor",
		"rate"
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'quantity' => 'float',
		'properties' => 'str',
		'message' => 'str'
	];

	public static $searchFields = ['title', 'message'];

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = CartItem::where('user_id', $params['user_id']);

			if(array_key_exists('date_range', $params) && $params['date_range']){
				$dateRange = explode(',', $params['date_range']);
				$fromDate = ValidateTools::toDate($dateRange[0]);
				$toDate = ValidateTools::toDate($dateRange[1]);
				$listItem->
					whereDate('created_at', '>=', $fromDate)->
					whereDate('created_at', '<=', $toDate);
			}

			if($params['link']){
				$listItem->where('url', $params['link']);
			}

			if($params['shop']){
				$listItem->where('shop_name', $params['shop']);
			}

			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			$listItem = $listItem->get();
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

	public static function addItem($listInput, $executor){
		try{
			$rate = $executor->rate;
			if(!$rate){
				$rateLog = RateLog::orderBy('id', 'DESC')->first();
				if($rateLog){
					$rate = $rateLog->order_rate;
				}else{
					$rate = intVal(\ConfigDb::get('cny-vnd'));
				}
			}
			foreach ($listInput as $input) {
				$input['user_id'] = $executor->id;
				$input['order_id'] = 0;
				$input['rate'] = $rate;
				if(!array_key_exists('properties', $input)){
					$input['properties'] = null;
				}
				if(!array_key_exists('title', $input)){
					$input['title'] = $input['url'];
				}
				$item = self::where([
						'user_id' => $input['user_id'],
						'title' => $input['title'],
						'url' => $input['url'],
						'unit_price' => ValidateTools::toFloat($input['unit_price']),
						'properties' => $input['properties']
					])->first();
				if(!$item){
					$input['unit_price'] = ValidateTools::toFloat($input['unit_price']);
					$input['quantity'] = ValidateTools::toInt($input['quantity']);
					if(!array_key_exists('vendor', $input) || !$input['vendor']){
						$input['vendor'] = Tools::getVendorFromUrl($input['url']);
					}
					self::create($input);
				}else{
					$item->quantity += intval($input['quantity']);
					$item->save();
				}
			}
			$listItem = self::where('user_id', $executor->id)->orderBy('id', 'asc')->get();
			return ResTools::lst($listItem);
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
