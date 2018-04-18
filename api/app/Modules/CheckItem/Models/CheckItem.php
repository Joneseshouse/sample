<?php

namespace App\Modules\CheckItem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\BillOfLanding\models\BillOfLanding;


class CheckItem extends Model{
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'check_items';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'bol_id',
		'order_item_id',
		'quantity',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'bol'
	];

	public static $fieldDescriptions = [
	];

	public function bol(){
		return $this->belongsTo('App\Modules\BillOfLanding\Models\BillOfLanding', 'bol_id');
	}

	public function orderItem(){
		return $this->belongsTo('App\Modules\OrderItem\Models\OrderItem', 'order_item_id');
	}

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = CheckItem::where($params);
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
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['NOT_FOUND']
				);
			}
			if(gettype($params) === "integer"){
				$result = self::find($params);
			}else if(gettype($params) === "string"){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['NOT_FOUND']
				);
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

	private static function hasId($id, $listItem, $key){
		foreach ($listItem as $item) {
			if($item && $item->$key === $id){
				return true;
			}
		}
		return false;
	}

	public static function sync($bol, $listOrderItem){
		\DB::transaction(function() use($bol, $listOrderItem){
			# List check item.
			$listCheckedItem = self::where('bol_id', $bol->id)->get();

			foreach ($listOrderItem as $orderItem) {
				if(!self::hasId($orderItem->id, $listCheckedItem, 'order_item_id')){
					# Add new
					self::create([
						'bol_id' => $bol->id,
						'order_item_id' => $orderItem->id,
						'quantity' => 0,
					]);
				}
			}
			foreach ($listCheckedItem as $checkedItem) {
				if(!self::hasId($checkedItem->order_item_id, $listOrderItem, 'id')){
					# Remove
					$checkedItem->delete();
				}
			}
			BillOfLanding::recalculate($bol);
		}, 10);
	}

	public function save(array $options = array()){
		# Auto increase order (if order exist)
		$item = $this;
        \DB::transaction(function() use($item){
	        $colums = $item->getConnection()->getSchemaBuilder()->getColumnListing($item->getTable());
			if(in_array('updated_at', $colums)){
				$item->updated_at = Tools::nowDateTime();
			}
			if(!$item->exists){
				if(in_array('created_at', $colums)){
					$item->created_at = Tools::nowDateTime();
				}
				if(self::count() > 0){
					$largestIdItem = self::orderBy('id', 'desc')->first();
					$item->id = $largestIdItem->id + 1;
				}else{
					$item->id = 1;
				}
			}
			// before save code
			parent::save();
			// after save code
        }, 20);
	}
}
