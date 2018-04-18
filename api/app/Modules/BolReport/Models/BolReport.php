<?php

namespace App\Modules\BolReport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;
use App\Modules\VnBillOfLanding\Models\VnBillOfLanding;

class BolReport extends Model{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'bol_reports';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'report_date',

		'number_of_bills',
		'number_of_packages',
		'total_mass',

		'cn_normal',
		'cn_deposit',
		'cn_missing_info',

		'vn_normal',
		'vn_deposit',
		'vn_missing_info',

		'export_normal',
		'export_deposit',
		'export_missing_info',

		'inventory'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static $fieldDescriptions = [
		'selected_year' => 'int',
		'selected_month' => 'int'
	];

	public static $searchFields = [];

	public static function list($params=[]){
		try{
			$selected_year = Tools::nowDate()->year;
			$selected_month = Tools::nowDate()->month;
			if(array_key_exists('selected_year', $params)){
				$selected_year = ValidateTools::toInt($params['selected_year']);
				unset($params['selected_year']);
			};
			if(array_key_exists('selected_month', $params)){
				$selected_month = ValidateTools::toInt($params['selected_month']);
				unset($params['selected_month']);
			};
			$startDate = Carbon::createFromDate($selected_year, $selected_month, 1, 'Asia/Saigon');
			$endDate = Carbon::createFromDate($selected_year, $selected_month, 1, 'Asia/Saigon')->endOfMonth();

			self::report(false);
			$listItem = BolReport::
				whereDate('report_date', '>=', $startDate)->
				whereDate('report_date', '<=', $endDate)->
				orderBy('report_date', 'desc')->
				get();
			return ResTools::lst($listItem);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function report($yesterday=true){
		try{
			if($yesterday){
				$reportDate = Tools::yesterday();
			}else{
				$reportDate = Tools::nowDate();
			}

			$listCnBol = CnBillOfLanding::whereDate('created_at', '=', $reportDate);
			$listCnNormal = clone $listCnBol;
			$listCnDeposit = clone $listCnBol;
			$listCnMissingInfo = clone $listCnBol;

			$listVnBol = VnBillOfLanding::whereDate('created_at', '=', $reportDate);
			$listVnNormal = clone $listVnBol;
			$listVnDeposit = clone $listVnBol;
			$listVnMissingInfo = clone $listVnBol;

			$listBol = BillOfLanding::whereDate('export_store_date', '=', $reportDate);
			$listExportNormal = clone $listBol;
			$listExportDeposit = clone $listBol;
			$listExportMissingInfo = clone $listBol;

			$record = [
				'report_date' => $reportDate,

				'number_of_bills' => $listCnBol->count(),
				'number_of_packages' => $listCnBol->sum('packages'),
				'total_mass' => $listCnBol->sum('mass'),

				'cn_normal' => $listCnNormal->where('order_type', 'normal')->count(),
				'cn_deposit' => $listCnDeposit->where('order_type', 'deposit')->count(),
				'cn_missing_info' => $listCnMissingInfo->whereNull('order_type')->count(),

				'vn_normal' => $listVnNormal->where('order_type', 'normal')->count(),
				'vn_deposit' => $listVnDeposit->where('order_type', 'deposit')->count(),
				'vn_missing_info' => $listVnMissingInfo->whereNull('order_type')->count(),

				'export_normal' => $listExportNormal->where('order_type', 'normal')->count(),
				'export_deposit' => $listExportDeposit->where('order_type', 'deposit')->count(),
				'export_missing_info' => $listExportMissingInfo->whereNull('order_type')->count(),

				'inventory' => BillOfLanding::whereNull('export_store_date')->count()
			];
			$item = self::whereDate('report_date', $record['report_date'])->first();
			if($item){
				$item->update($record);
			}else{
				$item = self::create($record);
			}
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
