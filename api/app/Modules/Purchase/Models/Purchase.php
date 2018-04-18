<?php

namespace App\Modules\Purchase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\Order\Models\Order;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\UserOrderLog\Models\UserOrderLog;
use App\Modules\CollectBol\Models\CollectBol;
use App\Modules\Admin\Models\Admin;


class Purchase extends Model{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'purchases';
    protected $appends = [
        'order_uid',
        'title',
        'vendor',
        'rate',
        'bols',
        'order_created_at',
        'user_fullname',
        'admin_fullname',
        'customer_care_staff_full_name',
        'number_of_items'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'shop_id',
        'code',
        'amount',
        'real_amount',
        'delivery_fee',
        'total',
        'mass',
        'packages',
        'delivery_fee_unit',
        'inland_delivery_fee',
        'inland_delivery_fee_raw',
        'number_of_bills_of_landing',
        'sub_fee',
        'insurance_fee',
        'total_raw',
        'note'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'shop', 'order',
        'user'
    ];

    public static $fieldDescriptions = [
        'code' => 'str|max:60',
        'inland_delivery_fee_raw' => 'float',
        'delivery_fee_unit' => 'float',
        'real_amount' => 'float',
        'note' => 'str'
    ];

    public static $searchFields = ['code'];

    public function user(){
        return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
    }

    public function order(){
        return $this->belongsTo('App\Modules\Order\Models\Order', 'order_id');
    }

    public function shop(){
        return $this->belongsTo('App\Modules\Shop\Models\Shop', 'shop_id');
    }

    public function orderItems(){
        return $this->
            hasMany('App\Modules\OrderItem\Models\OrderItem', 'purchase_id')->
            orderBy('id', 'desc');
    }

    public function billsOfLanding(){
        return $this->
            hasMany('App\Modules\BillOfLanding\Models\BillOfLanding', 'purchase_id')->
            orderBy('id', 'asc');
    }

    public function getTitleAttribute($value){
        return $this->shop->title;
    }

    public function getVendorAttribute($value){
        return $this->shop->vendor;
    }

    public function getRateAttribute($value){
        return $this->shop->rate;
    }

    public function getOrderUidAttribute($value){
        return $this->order?$this->order->uid:null;
    }

    public function getNumberOfItemsAttribute($value){
        return $this->orderItems->count();
    }

    public function getOrderCreatedAtAttribute($value){
        if($this->order_id && $this->order){
            return $this->order->created_at;
        }
        return null;
    }

    public function getBolsAttribute($value){
        $result = [];
        foreach ($this->billsOfLanding as $bol) {
            $result[] = $bol->code;
        }
        if(!count($result)){
            return null;
        }
        return implode(', ', $result);
    }

    public function getUserFullnameAttribute($value){
        if($this->user_id && $this->user){
            return $this->user->full_name.($this->user->uid?' / '.$this->user->uid:'');
        }
        return null;
    }

    public function getAdminFullnameAttribute($value){
        if($this->order_id && $this->order && $this->order->admin_id && $this->order->admin){
            return $this->order->admin->full_name;
        }
        return null;
    }

    public function getCustomerCareStaffFullNameAttribute($value){
        if($this->user_id && $this->user && $this->user->admin_id && $this->user->admin){
            return $this->user->admin->fullname;
        }
        return null;
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            if(!array_key_exists('order_id', $params)){
                $params['order_id'] = null;
            }
            $listItem = Purchase::where($params);
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

    public static function check($params=[]){
        try{
            $listItem = Purchase::whereNotNull('code')->withCount('billsOfLanding');

            if(array_key_exists('user_id', $params)){
                $listItem->where('user_id', $params['user_id']);
            }

            if(
                array_key_exists('dathang_filter_admin_id', $params) && 
                $params['dathang_filter_admin_id']
            ){
                $listItem->whereHas('order', function($q) use($params){
                    $q->where('admin_id', $params['dathang_filter_admin_id']);
                });
                unset($params['dathang_filter_admin_id']);
            }

            # Query with nhan vien cham soc
            if(array_key_exists('chamsoc_admin_id', $params)){
                $listItem->whereHas('user', function($q) use($params){
                    $q->where('admin_id', $params['chamsoc_admin_id']);
                });
                unset($params['chamsoc_admin_id']);
            }

            # Query with nhan vien dat hang
            if(array_key_exists('dathang_admin_id', $params)){
                $listItem->whereHas('order', function($q) use($params){
                    $q->where('admin_id', $params['dathang_admin_id']);
                });
                # $listItem->where('admin_id', $params['dathang_admin_id']);
                unset($params['dathang_admin_id']);
            }

            if(array_key_exists('date_range', $params) && $params['date_range']){
                $dateRange = explode(',', $params['date_range']);
                $fromDate = ValidateTools::toDate($dateRange[0]);
                $toDate = ValidateTools::toDate($dateRange[1]);
                $listItem->whereHas('order', function($q) use($fromDate, $toDate){
                    $q->
                        whereDate('created_at', '>=', $fromDate)->
                        whereDate('created_at', '<=', $toDate);
                });
            }

            if($params['purchase_code']){
                $listItem->where('code', $params['purchase_code']);
            }

            if($params['order_uid']){
                $listItem->whereHas('order', function($q) use($params){
                    $q->where('uid', $params['order_uid']);
                });
            }

            $listItem = $listItem->
                orderBy('bills_of_landing_count', 'asc')->
                orderBy('code', 'desc')->
                paginate(config('app.page_size'));
            $extra = [
                'list_admin' => Admin::whereHas('role', function($q){
                    $q->where('uid', config('app.dathang'));
                })->orderBy('last_name', 'asc')->get()
            ];
            return ResTools::lst($listItem, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function recalculate($id){
        $purchase = self::find($id);
        $amount = 0;
        # $insuranceFee = 0;
        if($purchase){
            $listOrderItem = OrderItem::where('purchase_id', $id)->get();
            foreach ($listOrderItem as $orderItem) {
                $amount += $orderItem->quantity * $orderItem->unit_price;
            }

            /*
            # Don't use bill of landing to calculate purchase things
            if($purchase->order->type === 'deposit'){
                # Chỉ mua bảo hiểm cho đơn hàng ký gửi
                $listBillOfLanding = BillOfLanding::where('purchase_id', $id)->get();
                foreach ($listBillOfLanding as $billOfLanding) {
                    if($billOfLanding->insurance_register){
                        # CÓ đăng ký bảo hiểm
                        $insuranceFee += $billOfLanding->insurance_fee;
                    }
                }
            }
            */

            $purchase->amount = $amount;
            $purchase->number_of_bills_of_landing = intval($purchase->billsOfLanding->count());
            $purchase->sub_fee = floatval($purchase->billsOfLanding->sum('sub_fee'));
            # $purchase->insurance_fee = $insuranceFee;
            $purchase->packages = intVal($purchase->billsOfLanding->sum('packages'));
            $purchase->mass = floatval($purchase->billsOfLanding->sum('mass'));
            $purchase->delivery_fee = $purchase->mass * $purchase->delivery_fee_unit; # VND
            # Inland delivery fee -> as it is

            $purchase->total_raw = 
                $purchase->amount * 
                (1 + $purchase->order->order_fee_factor/100) + 
                $purchase->inland_delivery_fee_raw;
            $purchase->total = 
                $purchase->total_raw * 
                $purchase->order->rate + 
                $purchase->delivery_fee + 
                $purchase->sub_fee;
            $purchase->save();

            $listBol = $purchase->billsOfLanding()->whereNull('export_store_date')->get();
            foreach ($listBol as $bol) {
                BillOfLanding::recalculate($bol, false, false);
            }

            return self::find($purchase->id);
        }
        return null;
    }

    public static function recalculateAll($orderId){
        $order = Order::find($orderId);
        if($order){
            $listItem = self::select('id')->where('order_id', $orderId)->get();
            foreach ($listItem as $item) {
                self::recalculate($item->id);
            }
            return $order;
        }
        return null;
    }

    public static function checkPurchasing($order){
        if($order && $order->status === 'confirm'){
            $totalPurchase = $order->purchases()->count();
            $totalPurchased = $order->purchases()->whereNotNull('code')->count();
            if($totalPurchased && $totalPurchased === $totalPurchase){
                $order->status = 'purchasing';
                $order->save();
            }
        }
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

    public static function addItem($input){
        try{
            if(
                array_key_exists('success', $input) && 
                array_key_exists('status_code', $input) && 
                $input['status_code'] !== 200
            ){
                return $input;
            }
            $order = Order::find($input['order_id']);
            $input['user_id'] = $order->user->id;
            $item = self::create($input);
            UserOrderLog::addPurchase($item);
            return ResTools::obj($item, trans('messages.add_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editItem($id, $input, $executor){
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

            if($executor->role->uid !== config('app.sadmin') && $item->order->status === 'done'){
            // if($item->order->status === 'done'){
                return ResTools::err(
                    'Đơn hàng đã chốt, bạn không thể sửa.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if($item->billsOfLanding()->whereNotNull('export_store_date')->count()){
                return ResTools::err(
                    'Đơn hàng đã xuất, bạn không thể sửa.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if(array_key_exists('code', $input)){
                $input['code'] = trim($input['code']);
                if($input['code']){
                    $checkDuplicate = self::where(["code" => $input["code"]])->first();
                    if($checkDuplicate && $checkDuplicate->id !== $id){
                        return ResTools::err(
                            trans('messages.duplicate_item'),
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                }
            }

            $exportedBols = $item->billsOfLanding()->whereNotNull('export_store_date')->count();
            $totalBols = $item->billsOfLanding()->count();
            $totalExportedInlandDeliveryFeeRaw = $item->
                billsOfLanding()->
                whereNotNull('export_store_date')->
                sum('inland_delivery_fee_raw');

            if(array_key_exists('inland_delivery_fee_raw', $input)){
                /*
                if ($exportedBols === $totalBols) {
                    $errorMessage = implode(' ', [
                        'Tất cả vận đơn đã xuất,',
                        'bạn không thể sửa phí ship nội địa'
                    ]);
                    return ResTools::err(
                        $errorMessage,
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                 */
                if($input['inland_delivery_fee_raw'] - $totalExportedInlandDeliveryFeeRaw < 0){
                    $errorMessage = implode(' ', [
                        'Phí vận chuyển nội địa mới',
                        'nhỏ hơn tổng phí vận chuyển nội địa của các vận đơn đã xuất.'
                    ]);
                    return ResTools::err(
                        $errorMessage,
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
            }

            $oldItem = clone $item;

            $item->update($input);

            # Check condition to change status to 'purchasing' => all shop has code
            self::checkPurchasing($item->order);

            if($item->code){
                $collectBol = CollectBol::where('purchase_code', $item->code)->first();
                if($collectBol){
                    $item->real_amount = $collectBol->real_amount;
                    $item->save();
                }
            }
            $order = Order::obj(Order::recalculate($item->order_id)->id);
            $extra = [
                'order' => $order
            ];
            $item=self::find($item->id);
            UserOrderLog::editPurchase($item, $oldItem, $executor);
            return ResTools::obj($item, trans('messages.edit_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeItem($id, $executor, $force){
        try{
            $result = null;
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->get();
            if($listItem->count()){
                foreach ($listItem as $item) {

                    if($executor->role->uid !== config('app.sadmin') && $item->order->status === 'done'){
                    # if($item->order->status === 'done'){
                        return ResTools::err(
                            'Đơn hàng đã chốt, bạn không thể xoá shop.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }

                    if($item->billsOfLanding()->whereNotNull('export_store_date')->count()){
                        return ResTools::err(
                            'Đơn hàng đã xuất, bạn không thể sửa.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }

                    $oldItem = clone $item;
                    if($force){
                        $item->forceDelete();
                    }else{
                        $item->delete();
                    }
                    self::checkPurchasing($item->order);
                    UserOrderLog::removePurchase($oldItem, $executor);
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

    public static function allPurchaseHaveAtLeastOneBol($order){
        $listPurchase = $order->purchases;
        foreach ($listPurchase as $purchase) {
            if(!$purchase->billsOfLanding()->count()){
                return false;
            }
        }
        return true;
    }

    public static function upload($listData, $executor){
        try{
            $listId = [];
            foreach ($listData as $data) {
                $purchaseCode = $data['purchase_code'];
                $billOfLandingCode = $data['bill_of_landing_code'];
                $realAmount = ValidateTools::toFloat($data['real_amount']);
                if($billOfLandingCode){
                    $item = self::where('code', $purchaseCode)->first();
                    if($item){
                        $item->real_amount = $realAmount;
                        $item->save();
                        $listId[] = $data['id'];

                        $billOfLanding = BillOfLanding::where([
                            'purchase_id' => $item->id,
                            'code' => $billOfLandingCode
                        ])->count();
                        if(!$billOfLanding){
                            $data = [
                                'purchase_id' => $item->id,
                                'order_id' => $item->order_id,
                                'code' => $billOfLandingCode,
                                'rate' => $item->order->rate,
                                'delivery_fee_unit' => (
                                    $item->user->delivery_fee_unit
                                    ?:
                                    $item->order->address->areaCode->delivery_fee_unit
                                ),
                                'input_mass' => 0,
                                'sub_fee' => 0
                            ];
                            BillOfLanding::addItem($data, $executor);
                        }
                    }
                }
            }
            if(count($listId)){
                $result = ['id' => count($listId)>1?$listId:$listId[0]];
            }else{
                $result = ['id' => null];
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
        # if($this->inland_delivery_fee){
            $this->inland_delivery_fee = round($this->inland_delivery_fee_raw * $this->order->rate);
        # }
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
                    $largestPurchaseItem = self::orderBy('order', 'desc')->first();
                    if($largestPurchaseItem){
                        $this->order = $largestPurchaseItem->order + 1;
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
