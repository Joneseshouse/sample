<?php

namespace App\Modules\BolDaily\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\BillOfLanding\Models\BillOfLanding;

class BolDaily extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'bols_daily';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'report_date',
		'number_of_bols',
		'order_bols',
		'deposit_bols',
		'missing_bols',
		'mass',
		'total',
		'last_updated'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
	];

	public static $searchFields = ['report_date'];

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = BolDaily::where([]);
			/*
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			*/
			if(array_key_exists('date_range', $params) && $params['date_range']){
				$dateRange = explode(',', $params['date_range']);
				$fromDate = ValidateTools::toDate($dateRange[0]);
				$toDate = ValidateTools::toDate($dateRange[1]);
				$listItem->
					whereDate('created_at', '>=', $fromDate)->
					whereDate('created_at', '<=', $toDate);
			}

			if(array_key_exists('last_updated', $params) && $params['last_updated']){
				$listItem->whereDate('last_updated', '=', $params['last_updated']);
			}

			if(array_key_exists('bol', $params) && $params['bol']){
				$bol = BillOfLanding::where('code', strtoupper($params['bol']))->first();
				if($bol){
					$listItem->whereDate('report_date', '=', $bol->created_at);
				}else{
					$listItem->where('id', 0);
				}
			}

			$listItem = $listItem->
				orderBy('report_date', 'desc')->
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

	public static function updateByDate($date=null){
		if(!$date){
			$date = Tools::nowDate();
		}
		if(gettype($date) === 'string'){
			$date = ValidateTools::toDate($date);
		}

		$listItemQuery = BillOfLanding::whereDate('created_at', '=', $date);

        $numberOfBols = $listItemQuery->count();
        $mass = $listItemQuery->sum('mass');
        $total = $listItemQuery->sum('total');

       	$orderBolsQuery = clone $listItemQuery;
       	$orderBols = $orderBolsQuery->whereNotNull('order_id')->count();

       	$depositBolsQuery = clone $listItemQuery;
       	$depositBols = $depositBolsQuery->whereNull('order_id')->whereNotNull('address_id')->count();

       	$missingBolsQuery = clone $listItemQuery;
       	$missingBols = $depositBolsQuery->whereNull('order_id')->whereNull('address_id')->count();

       	$lastUpdated = null;
       	$lastUpdated = $listItemQuery->orderBy('updated_at', 'desc')->first();
       	if($lastUpdated){
       		$lastUpdated = $lastUpdated->updated_at;
       	}

        $data = [
            'report_date' => $date,
            'number_of_bols' => $numberOfBols,
            'order_bols' => $orderBols,
            'deposit_bols' => $depositBols,
            'missing_bols' => $missingBols,
            'mass' => $mass,
            'total' => $total,
            'last_updated' => $lastUpdated
        ];

		$existItem = self::whereDate('report_date', '=', $date)->first();
        if(!$existItem){
            self::create($data);
        }else{
            $existItem->update($data);
        }
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
