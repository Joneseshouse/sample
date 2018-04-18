<?php

namespace App\Modules\ExportBill\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\ExportBillDaily\Models\ExportBillDaily;
use App\Modules\Contact\Models\Contact;
use App\Modules\UserTransaction\Models\UserTransaction;
use App\Modules\Admin\Models\Admin;


class ExportBill extends Model{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'export_bills';
    protected $appends = [
        'addresses',
        'contact',
        'admin_fullname'
    ];
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'user_id',
        'uid',
        'note',
        'sub_fee',
        'total',
        'amount',
        'contact_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'bols',
        'admin'
    ];

    public static $fieldDescriptions = [
        'sub_fee' => 'int,required',
        'note' => 'str,max:250',
        'list_id' => 'str',
        'contact_id' => 'int'
    ];

    public static $searchFields = ['uid'];

    public function bols(){
        return $this->hasMany('App\Modules\BillOfLanding\Models\BillOfLanding', 'export_bill_id');
    }

    public function contactObj(){
        return $this->belongsTo('App\Modules\Contact\Models\Contact', 'contact_id');
    }

    public function admin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
    }

    public function getContactAttribute($value){
        if($this->contact_id){
            $item = Contact::find($this->contact_id);
            if($item){
                return $item;
            }
        }
        return [
            'company' => \ConfigDb::get('contact-cong-ty'),
            'address' => \ConfigDb::get('contact-dia-chi'),
            'website' => \ConfigDb::get('contact-website'),
            'phone' => \ConfigDb::get('contact-phone'),
            'email' => \ConfigDb::get('contact-email'),
        ];
    }

    public function getAddressesAttribute($value){
        if($this->bols()->count()){
            $result = [];
            foreach ($this->bols as $bol) {
                if(!in_array($bol->address->uid, $result)){
                    $result[] = $bol->address->uid;
                }
            }
            return implode(', ', $result);
        }
        return '';
    }

    public function getAdminFullnameAttribute($value){
        if($this->admin_id && $this->admin){
            return $this->admin->full_name;
        }
        return null;
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $listItem = self::where([]);
            # print_r($params);die;
            if(array_key_exists('user_id', $params)){
                $listItem->where('user_id', $params['user_id']);
            }

            if(array_key_exists('admin_id', $params) && $params['admin_id']){
                $listItem->where('admin_id', $params['admin_id']);
            }

            if(array_key_exists('uid', $params) && $params['uid']){
                $listItem->where('uid', $params['uid']);
            }

            if(array_key_exists('address_code', $params) && $params['address_code']){
                $listItem->whereHas('bols', function($q) use($params){
                    $q->whereHas('address', function($q1) use($params){
                        $q1->where('uid', strtoupper($params['address_code']));
                    });
                });
            }

            if(array_key_exists('date_range', $params) && $params['date_range']){
                $dateRange = explode(',', $params['date_range']);
                $fromDate = ValidateTools::toDate($dateRange[0]);
                $toDate = ValidateTools::toDate($dateRange[1]);
                $listItem->
                    whereDate('created_at', '>=', $fromDate)->
                    whereDate('created_at', '<=', $toDate);
            }

            $extra = [
                'total' => [
                    'amount' => $listItem->sum('amount'),
                    'sub_fee' => $listItem->sum('sub_fee'),
                    'total' => $listItem->sum('total')
                ],
                'list_admin' => Admin::orderBy('last_name', 'asc')->get()
            ];
            $listItem = $listItem->
                orderBy('created_at', 'desc')->
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

            $addressDetail = [];
            $firstBol = BillOfLanding::where('export_bill_id', $result->id)->first();
            if($firstBol && $firstBol->address()->count()){
                $addressDetail = $firstBol->address;
                $addressDetail->from_address = \ConfigDb::get('in-ten-dich-vu');
                $addressDetail->from_phone = \ConfigDb::get('in-so-dt');
                if(!$addressDetail->fullname){
                    $addressDetail->fullname = $addressDetail->user->full_name;
                }
                if(!$addressDetail->phone){
                    $addressDetail->phone = $addressDetail->user->phone;
                }
                if($addressDetail->areaCode){
                    $addressDetail->area = $addressDetail->areaCode->title;
                }
                // $addressDetail->area = $addressDetail->areaCode->title;
            }


            $extra = [
                'list_bill_of_landing' => BillOfLanding::
                    where('export_bill_id', $result->id)->
                    orderBy('vn_store_date', 'asc')->
                    get(),
                'address_detail' => $addressDetail,
                'list_contact' => Contact::orderBy("id", 'DESC')->get()
            ];
            return ResTools::obj($result, $original, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function goodsAmount($id){
        if(!$id || !ExportBill::find($id)) return 0;
        $result = BillOfLanding::
            where('export_bill_id', $id)->
            whereNotNull('purchase_id')->
            where('purchase_id', '!=', 0)->
            sum('total');
        return intval($result);
    }

    public static function addItem($input, $executor){
        /*
         * input: sub_fee, list_id, admin_id
         */
        try{
            if(
                array_key_exists('success', $input) && 
                array_key_exists('status_code', $input) && $input['status_code'] !== 200
            ){
                return $input;
            }

            $oldItem = null;
            if (array_key_exists('oldItem', $input)) {
                $oldItem = $input['oldItem'];
                unset($input['oldItem']);
            }
            $listId = explode(',', $input['list_id']);
            unset($input['list_id']);

            $input['uid'] = (string)Tools::nowDateTime()->timestamp;

            # BEGIN --- Check duplication of user ID
            $userIdList = [];
            $userId = null;
            foreach ($listId as $id) {
                $id = ValidateTools::toInt($id);
                $billOfLanding = BillOfLanding::find($id);
                if($billOfLanding && $billOfLanding->user_id){
                    $userId = $billOfLanding->user_id;
                    $input['user_id'] = $userId;
                    if(!in_array($userId, $userIdList)){
                        $userIdList[] = $userId;
                    }
                }
            }

            if(count($userIdList) !== 1){ # No user ID of 2 kinds of user ID not allowed
                return ResTools::err(
                    trans('messages.missing_user_from_bill_of_landing'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            # END ---

            $item = $oldItem ? $oldItem : self::create($input);
            $amount = 0;
            $listPurchaseId = [];
            foreach ($listId as $id) {
                $id = ValidateTools::toInt($id);
                $billOfLanding = BillOfLanding::find($id);
                if ($oldItem) {
                    $billOfLanding->export_bill_id = null;
                }
                if($billOfLanding && !$billOfLanding->export_bill_id){
                    $billOfLanding->export_store_date = Tools::nowDateTime();
                    $billOfLanding->export_bill_id = $item->id;
                    $billOfLanding->save();
                    $amount += $billOfLanding->delivery_fee;
                    if (!in_array($billOfLanding->purchase_id, $listPurchaseId)) {
                        array_push($listPurchaseId, $billOfLanding->purchase_id);
                    }
                }
            }
            if (!$amount) {
                return ResTools::err(
                    'Bạn vui lòng chọn ít nhất 1 vận đơn để làm hoá đơn xuất',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $item->amount = round($amount);
            # $item->total = $item->amount + $item->sub_fee;
            $item->total = $item->amount;
            $item->save();

            $transactionData = [
                'user_id' => $userId,
                'type' => 'XH',
                'amount' => floor(abs($item->total)),
                'money_type' => '-',
                'note' => 'Xuất hàng: '.$item->uid,
                'export_bill_id' => $item->id
            ];

            if($transactionData['amount']){
                if (!$oldItem) {
                    UserTransaction::addItem($transactionData, $executor);
                } else {
                    $transaction = UserTransaction::where([
                        'type' => 'XH',
                        'user_id' => $userId,
                        'export_bill_id' => $item->id
                    ])->first();
                    UserTransaction::editItem($transaction->id, $transactionData, null);
                }
            }

            if (!$oldItem) {
                # Check purchased that fulfilled
                foreach ($listPurchaseId as $id) {
                    $purchase = Purchase::find($id);
                    if ($purchase) {
                        $unExportedBols = $purchase->billsOfLanding()->whereNull('export_bill_id')->count();
                        if (!$unExportedBols) {
                            # All exported -> liability here
                            $transactionData = [
                                'user_id' => $userId,
                                'type' => 'TH',
                                'amount' => floor(abs($purchase->total - $purchase->delivery_fee)),
                                'money_type' => '-',
                                'note' => 'Tiền hàng mã GD: '.$purchase->code,
                                'export_bill_id' => $item->id,
                                'purchase_id' => $purchase->id
                            ];
                            UserTransaction::addItem($transactionData, $executor);
                        }
                    }
                }
                ExportBillDaily::updateByDate($item->created_at);
            }
            return ResTools::obj($item, trans('messages.add_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editItem($id, $input){
        try{
            if(
                array_key_exists('success', $input) && 
                array_key_exists('status_code', $input) && 
                $input['status_code'] !== 200
            ){
                return $input;
            }

            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $input['total'] = $item->total - $item->sub_fee + $input['sub_fee'];
            $item->update($input);

            ExportBillDaily::updateByDate($item->created_at);
            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editContact($id, $contact_id){
        try{
            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if(!$contact_id){
                $contact_id = null;
            }
            $item->contact_id = $contact_id;
            $item->save();
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
                $listFulfilledPurchaseId = [];
                foreach ($listItem as $item) {
                    $createdAt = $item->created_at;
                    $listBillOfLanding = BillOfLanding::where('export_bill_id', $item->id)->get();
                    foreach ($listBillOfLanding as $billOfLanding) {
                        // Get fulfilled list of purchase
                        $purchase = $billOfLanding->purchase;
                        $unExportedBols = $purchase->billsOfLanding()->whereNull('export_store_date')->count();
                        if (!$unExportedBols && !in_array($purchase->id, $listFulfilledPurchaseId)) {
                            array_push($listFulfilledPurchaseId, $purchase->id);
                        }

                        $billOfLanding->export_bill_id = null;
                        $billOfLanding->export_store_date = null;
                        $billOfLanding->save();
                    }

                    if (count($listFulfilledPurchaseId)) {
                        UserTransaction::whereIn('purchase_id', $listFulfilledPurchaseId)->delete();
                    }

                    if($force){
                        $item->forceDelete();
                    }else{
                        $item->delete();
                    }
                    ExportBillDaily::updateByDate($createdAt);
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

    public function delete(){
        UserTransaction::where('export_bill_id', $this->id)->delete();
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
