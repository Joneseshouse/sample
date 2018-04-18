<?php

namespace App\Modules\RateLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Order\Models\Order;

class RateLog extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'rate_logs';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'rate',
		'buy_rate',
		'sell_rate',
		'order_rate'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'rate' => 'int,required',
		'buy_rate' => 'int',
		'sell_rate' => 'int',
		'order_rate' => 'int'
	];

	public static $searchFields = ['rate'];

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = RateLog::where($params);
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

	public static function latestRate(){
		$rate = self::orderBy('created_at', 'desc')->first();
		if($rate){
			return $rate->rate;
		}
		return \ConfigDb::get('cny-vnd', 3400);
	}

	public static function latestOrderRate(){
		$rate = self::orderBy('created_at', 'desc')->first();
		if($rate){
			return $rate->order_rate;
		}
		return \ConfigDb::get('cny-vnd', 3400);
	}

	public static function addItem($input){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}
			$input['buy_rate'] = ValidateTools::toInt($input['rate']) + ValidateTools::toInt(\ConfigDb::get('lech-ty-gia-chuyen-khoan'));
			$input['sell_rate'] = ValidateTools::toInt($input['rate']) + ValidateTools::toInt(\ConfigDb::get('lech-ty-gia-nho-thanh-toan'));
			# $input['order_rate'] = ValidateTools::toInt($input['rate']) + ValidateTools::toInt(\ConfigDb::get('lech-ty-gia-order'));
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
			$oldRate = $item->rate;
			$item->update($input);

			if($oldRate !== $input['rate']){
				// Chage every orders in corresponding date
				$listOrder = Order::whereDate('created_at', '=', $item->created_at)->get();
				foreach ($listOrder as $order) {
					$order->real_rate = $item->rate;
					$order->save();
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
