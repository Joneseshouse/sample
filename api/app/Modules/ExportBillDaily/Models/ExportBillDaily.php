<?php

namespace App\Modules\ExportBillDaily\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\ExportBill\Models\ExportBill;

class ExportBillDaily extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'export_bill_daily';
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'export_date',
		'addresses',
		'number_of_export',
		'number_of_bol',
		'mass',
		'packages',
		'sub_fee',
		'amount',
		'total',
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

	public static $searchFields = ['export_date'];

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = ExportBillDaily::where($params);
			if($keyword && strlen($keyword) >=3){
				$listItem = $listItem->where(function($query) use($keyword){
					foreach(self::$searchFields as $key => $field){
						$query->orWhere($field, 'ilike', '%' . $keyword . '%');
					}
				});
			}
			$listItem = $listItem->
				orderBy('export_date', 'desc')->
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

		$listItemQuery = ExportBill::whereDate('created_at', '=', $date);

        $numberOfExport = $listItemQuery->count();
        $subFee = $listItemQuery->sum('sub_fee');
        $amount = $listItemQuery->sum('amount');
        $total = $listItemQuery->sum('total');
        $listItem = $listItemQuery->get();

        $numberOfBol = 0;
        $addresses = [];
        $mass = 0;
        $packages = 0;
        foreach ($listItem as $item) {
            $numberOfBol += $item->bols()->count();
            foreach ($item->bols as $bol) {
            	if(!in_array($bol->address->uid, $addresses)){
		            $addresses[] = $bol->address->uid;
            	}
	            $mass += $bol->mass;
	            $packages += $bol->packages;
            }
        }

        $data = [
            'export_date' => $date,
            'number_of_export' => $numberOfExport,
            'number_of_bol' => $numberOfBol,
            'addresses' => implode(', ', $addresses),
            'mass' => $mass,
            'packages' => $packages,
            'sub_fee' => $subFee,
            'amount' => $amount,
            'total' => $total
        ];

		$existItem = self::whereDate('export_date', '=', $date)->first();
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
