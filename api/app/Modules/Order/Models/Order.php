<?php

namespace App\Modules\Order\Models;

use PHPExcel_Worksheet_Drawing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Shop\Models\Shop;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\OrderItemNote\Models\OrderItemNote;
use App\Modules\Address\Models\Address;
use App\Modules\Admin\Models\Admin;
use App\Modules\User\Models\User;
use App\Modules\RateLog\Models\RateLog;
use App\Modules\CollectBol\Models\CollectBol;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\UserOrderLog\Models\UserOrderLog;
use App\Modules\CartItem\Models\CartItem;
use App\Modules\UserTransaction\Models\UserTransaction;

class Order extends Model{
     use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'orders';
    protected $appends = [
        'customer_id',
        'customer_full_name',
        'customer_care_staff_full_name',
        'customer_email',
        'customer_phone',
        'address_name',
        'address_code',
        'admin_full_name',
        'confirm_full_name',
        'delivery_fee_unit',
        's_amount',
        's_mass',
        's_inland_delivery_fee',
        's_order_fee',

        // 'items',
        // 'discount',
        // 'real_amount',
        'bol_statistics',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'confirm_admin_id',
        'user_id',
        'address_id',
        'uid',
        'rate',
        'real_rate',
        'mass',
        'packages',
        'delivery_fee',
        'inland_delivery_fee',
        'inland_delivery_fee_raw',
        'order_fee',
        'amount',
        'total',
        'month',
        'year',
        'status',
        'type',
        'order',
        'order_fee_factor',
        'deposit_factor',
        'complain_day',
        'number_of_bills_of_landing',
        'number_of_purchases',
        'sub_fee',
        'insurance_fee',
        'total_raw',
        'confirm_date',
        'note'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user', 'address'
    ];

    public static $fieldDescriptions = [
        'admin_id' => 'int',
        'address_id' => 'int',
        'type' => 'str',
        'status' => 'str',
        'note' => 'str',
        'order_fee_factor' => 'float',
        'rate' => 'int'
    ];

    public static $searchFields = ['uid'];

    public function user(){
        return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
    }

    public function admin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
    }

    public function confirmAdmin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'confirm_admin_id');
    }

    public function purchases(){
        return $this->
            hasMany('App\Modules\Purchase\Models\Purchase', 'order_id')->
            with([
                'orderItems' => function($q){
                    $q->orderBy('id', 'asc');
                },
                'billsOfLanding' => function($q){
                    $q->orderBy('id', 'asc');
                }
            ])->
            orderBy('id', 'asc');
    }

    public function userTransactions(){
        return $this->
            hasMany('App\Modules\UserTransaction\Models\UserTransaction', 'order_id');
    }

    public function billsOfLanding(){
        return $this->
            hasMany('App\Modules\BillOfLanding\Models\BillOfLanding', 'order_id');
    }

    public function orderItems(){
        return $this->hasMany('App\Modules\OrderItem\Models\OrderItem', 'order_id');
    }

    public function address(){
        return $this->belongsTo('App\Modules\Address\Models\Address', 'address_id');
    }

    /*
    public function getRealAmountAttribute($value){
        $listPurchase = $this->purchases;
        $total = 0;
        foreach ($listPurchase as $purchase) {
            if($purchase->real_amount){
                $total += $purchase->real_amount;
            }else{
                $total += $purchase->amount;
            }
        }
        return $total;
        return 0;
    }
    
    public function getDiscountAttribute($value){
        // return $this->purchases()->sum('amount') + 
        // $this->purchases()->sum('inland_delivery_fee_raw') - 
        // $this->real_amount;
        return 0;
    }

    public function getItemsAttribute($value){
        return $this->orderItems->count();
        return 0;
    }
    */

    public function getSAmountAttribute($value){
        return implode("", [
            '¥ ',
            number_format($this->amount, 1),
            ' / ',
            number_format($this->rate*$this->amount),
            ' ₫'
        ]);
    }

    public function getSInlandDeliveryFeeAttribute($value){
        return implode("", [
            '¥ ',
            number_format($this->inland_delivery_fee_raw, 1),
            ' / ',
            number_format($this->inland_delivery_fee),
            ' ₫'
        ]);
    }

    public function getSOrderFeeAttribute($value){
        return implode("", [
            '¥ ',
            number_format($this->order_fee, 1),
            ' / ',
            number_format($this->rate*$this->order_fee),
            ' ₫'
        ]);
    }

    public function getSMassAttribute($value){
        if(!$this->mass) return null;
        return number_format($this->mass, 1).' Kg';
    }

    public function getCustomerIdAttribute($value){
        return $this->user_id;
    }

    public function getCustomerFullNameAttribute($value){
        if($this->user_id && $this->user){
            return $this->user->fullname;
        }
        return null;
    }

    public function getCustomerEmailAttribute($value){
        if($this->user_id && $this->user){
            return $this->user->email;
        }
        return null;
    }

    public function getCustomerPhoneAttribute($value){
        if($this->user_id && $this->user){
            return $this->user->phone;
        }
        return null;
    }

    public function getAddressNameAttribute($value){
        if($this->address_id && $this->address){
            return $this->address->address;
        }
        return null;
    }

    public function getAddressCodeAttribute($value){
        if($this->address_id && $this->address){
            return $this->address->uid;
        }
        return null;
    } 

    public function getDeliveryFeeUnitAttribute($value){
        if($this->user && $this->user->delivery_fee_unit){
            return $this->user->delivery_fee_unit;
        }

        if($this->address_id && $this->address){
            return $this->address->delivery_fee_unit;
        }

        return 0;
    }

    public function getAdminFullNameAttribute($value){
        if($this->admin_id && $this->admin){
            return $this->admin->fullname;
        }
        return null;
    }

    public function getConfirmFullNameAttribute($value){
        if($this->confirm_admin_id && $this->confirmAdmin){
            return $this->confirmAdmin->fullname;
        }
        return null;
    }

    public function getCustomerCareStaffFullNameAttribute($value){
        if($this->user_id && $this->user && $this->user->admin_id && $this->user->admin){
            return $this->user->admin->fullname;
        }
        return null;
    }

    public function getBolStatisticsAttribute($value){
        $totalBol = 0;
        $listPurchaseCodeRaw = Purchase::select('id', 'code')->where('order_id', $this->id)->get();
        $listPurchaseCode = [];
        foreach ($listPurchaseCodeRaw as $purchaseCodeRaw) {
            if($purchaseCodeRaw->code){
                $listPurchaseCode[] = $purchaseCodeRaw->code;
            }
        }
        $bolQuery = BillOfLanding::where('order_id', $this->id);
        $shopReleaseBol = CollectBol::whereIn('purchase_code', $listPurchaseCode)->count();
        $totalBolActual = $bolQuery->count();
        $totalBol = $shopReleaseBol > $totalBolActual ? $shopReleaseBol : $totalBolActual;
        $totalPurchase = $this->purchases->count();
        $paidPurchase = Purchase::where('order_id', $this->id)->whereNotNull('code')->count();

        $cnBolQuery = clone $bolQuery;
        $cnBol = $cnBolQuery->whereNotNull('cn_store_date')->count();

        $vnBolQuery = clone $bolQuery;
        $vnBol = $vnBolQuery->whereNotNull('vn_store_date')->count();

        $exportBolQuery = clone $bolQuery;
        $exportBol = $exportBolQuery->whereNotNull('export_store_date')->count();
        return [
            'total_purchase' => $totalPurchase,
            'paid_purchase' => $paidPurchase,
            'total_bol' => $totalBol,
            'shop_release_bol' => $shopReleaseBol,
            'cn_bol' => $cnBol,
            'vn_bol' => $vnBol,
            'export_bol' => $exportBol
        ];
        /*
        return [
            'total_purchase' => 0,
            'paid_purchase' => 0,
            'total_bol' => 0,
            'shop_release_bol' => 0,
            'cn_bol' => 0,
            'vn_bol' => 0,
            'export_bol' => 0
        ];
        */
    }

    public static function updateStatistics() {
        $listItem = self::all();
        foreach ($listItem as $item) {
            // Calculating real_amount
            $listPurchase = Purchase::where('order_id', $item->id);
            $listOrderItem = OrderItem::where('order_id', $item->id);
            $realAmount = 0;
            foreach ($listPurchase->get() as $purchase) {
                if($purchase->real_amount){
                    $realAmount += $purchase->real_amount;
                }else{
                    $realAmount += $purchase->amount;
                }
            }
            $item->real_amount = $realAmount;

            // Calculating discount
            $sumAmount = $listPurchase->sum('amount');
            $sumInlandDeliveryFeeRaw = $listPurchase->sum('inland_delivery_fee_raw');
            $item->discount = $sumAmount + $sumInlandDeliveryFeeRaw - $item->real_amount;

            // calculating items 
            $item->items = $listOrderItem->count();

            $item->save();
        }
        // real_amount
        /*
        $listPurchase = $this->purchases;
        $total = 0;
        foreach ($listPurchase as $purchase) {
            if($purchase->real_amount){
                $total += $purchase->real_amount;
            }else{
                $total += $purchase->amount;
            }
        }
        return $total;
        */

        // discount
        // return $this->purchases()->sum('amount') + 
        // $this->purchases()->sum('inland_delivery_fee_raw') - 
        // $this->real_amount;

        // items
        /*
        return $this->orderItems->count();
        */
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            if(array_key_exists('status', $params) && $params['status'] === 'all'){
                unset($params['status']);
            }
            /*
            if($keyword && strlen($keyword) >=3){
                $listItem = $listItem->where(function($query) use($keyword){
                    foreach(self::$searchFields as $key => $field){
                        $query->orWhere($field, 'ilike', '%' . $keyword . '%');
                    }
                });
            }
            */
            # Thực hiện tất cả query liên quan đến filter, vụ status để sau cùng
            unset($params['page']);
            $baseParams = [];
            if(array_key_exists('type', $params)){
                $baseParams['type'] = $params['type'];
            }
            if(array_key_exists('status', $params)){
                $baseParams['status'] = $params['status'];
            }

            # Query with type
            # $listItem = self::where('type', $baseParams['type']);
            $listItem = self::where([]);
            if(array_key_exists('status', $baseParams) && $baseParams['status'] === 'draft'){
                if(array_key_exists('type', $baseParams)){
                    $listItem = self::where('type', $baseParams['type']);
                }else{
                    $listItem = self::where([]);
                }
            }else{
                if(array_key_exists('type', $baseParams)){
                    $listItem = self::where('type', $baseParams['type'])->where('status', '!=', 'draft');
                }else{
                    $listItem = self::where('status', '!=', 'draft');
                }
            }

            if(array_key_exists('user_id', $params)){
                $listItem->where('user_id', $params['user_id']);
            }

            # Query with dathang_staff
            if(array_key_exists('dathang_staff', $params)){
                $dathangStaff = ValidateTools::toInt($params['dathang_staff']);
                unset($params['dathang_staff']);
                if($dathangStaff){
                    $listItem->where('admin_id', $dathangStaff);
                }
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
                $listItem->where('admin_id', $params['dathang_admin_id']);
                unset($params['dathang_admin_id']);
            }

            # Query with confirm_staff
            if(array_key_exists('confirm_staff', $params)){
                $confirmStaff = ValidateTools::toInt($params['confirm_staff']);
                unset($params['confirm_staff']);
                if($confirmStaff){
                    $listItem->where('confirm_admin_id', $confirmStaff);
                }
            }

            # Query with customer_staff
            if(array_key_exists('customer_staff', $params)){
                $customerStaff = ValidateTools::toInt($params['customer_staff']);
                unset($params['customer_staff']);
                if($customerStaff){
                    $listItem->whereHas('user', function($q) use($customerStaff){
                        $q->where('admin_id', $customerStaff);
                    });
                }
            }

            # Query with check_staff
            if(array_key_exists('check_staff', $params)){
                $checkStaff = ValidateTools::toInt($params['check_staff']);
                unset($params['check_staff']);
                if($checkStaff){
                    $listItem->whereHas('billsOfLanding', function($q) use($checkStaff){
                        $q->whereHas('checkBills', function($q1) use($checkStaff){
                            $q1->where('admin_id', $checkStaff);
                        });
                    });
                }
            }

            # Query with customer_name
            if(array_key_exists('customer_name', $params)){
                $customerName = ValidateTools::toStr($params['customer_name']);
                unset($params['customer_name']);
                if($customerName){
                    $listItem->whereHas('user', function($q) use($customerName){
                        $q->where(
                            \DB::raw("CONCAT(first_name, ' ', last_name)"), 'ilike', '%' . $customerName . '%'
                        );
                    });
                }
            }

            # Query with customer_id
            if(array_key_exists('customer_id', $params)){
                $customerId = ValidateTools::toStr($params['customer_id']);
                unset($params['customer_id']);
                if($customerId){
                    $listItem->where('user_id', $customerId);
                }
            }

            # Query with customer_phone
            if(array_key_exists('customer_phone', $params)){
                $customerPhone = ValidateTools::toStr($params['customer_phone']);
                unset($params['customer_phone']);
                if($customerPhone){
                    $listItem->whereHas('user', function($q) use($customerPhone){
                        $q->where('phone', $customerPhone);
                    });
                }
            }

            # Query with customer_email
            if(array_key_exists('customer_email', $params)){
                $customerEmail = ValidateTools::toStr($params['customer_email']);
                unset($params['customer_email']);
                if($customerEmail){
                    $listItem->whereHas('user', function($q) use($customerEmail){
                        $q->where('email', $customerEmail);
                    });
                }
            }

            # Query with rate
            if(array_key_exists('rate', $params)){
                $rate = ValidateTools::toInt($params['rate']);
                unset($params['rate']);

                $listItem->where('rate', $rate);
            }

            # Query with order_fee
            if(array_key_exists('order_fee', $params)){
                $orderFee = ValidateTools::toFloat($params['order_fee']);
                unset($params['order_fee']);

                $listItem->where('order_fee', $orderFee);
            }

            # Query with delivery_fee
            if(array_key_exists('delivery_fee', $params)){
                $deliveryFee = ValidateTools::toInt($params['delivery_fee']);
                unset($params['delivery_fee']);

                $listItem->where('delivery_fee', $deliveryFee);
            }


            # Query with complain_date

            # Query with created_at
            if(array_key_exists('created_at', $params) && $params['created_at']){
                $dateRange = explode(',', $params['created_at']);
                $fromDate = ValidateTools::toDate($dateRange[0]);
                $toDate = ValidateTools::toDate($dateRange[1]);
                unset($params['created_at']);
                $listItem->
                    whereDate('created_at', '>=', $fromDate)->
                    whereDate('created_at', '<=', $toDate);
            }

            # Query with updated_at
            if(array_key_exists('updated_at', $params)){
                $updatedAt = ValidateTools::toDate($params['updated_at']);
                unset($params['updated_at']);

                $listItem->whereDate('updated_at', '=', $updatedAt);
            }

            # Query with confirm_date
            if(array_key_exists('confirm_date', $params)){
                $confirmDate = ValidateTools::toDate($params['confirm_date']);
                unset($params['confirm_date']);

                $listItem->whereDate('confirm_date', '=', $confirmDate);
            }

            # Query with bill_of_landing_code
            if(array_key_exists('bill_of_landing_code', $params)){
                $billOfLandingCode = ValidateTools::toStr($params['bill_of_landing_code']);
                unset($params['bill_of_landing_code']);

                $listItem->whereHas('billsOfLanding', function($q) use($billOfLandingCode){
                    $q->where('code', $billOfLandingCode);
                });
            }

            # Query with purchase_code
            if(array_key_exists('purchase_code', $params)){
                $purchaseCode = ValidateTools::toStr($params['purchase_code']);
                unset($params['purchase_code']);

                $listItem->whereHas('purchases', function($q) use($purchaseCode){
                    $q->where('code', $purchaseCode);
                });
            }

            # Query with order_uid
            if(array_key_exists('order_uid', $params)){
                $orderUid = ValidateTools::toStr($params['order_uid']);
                unset($params['order_uid']);

                $listItem->where('uid', $orderUid);
            }

            # Query with shop_title
            if(array_key_exists('shop_title', $params)){
                $shopTitle = ValidateTools::toStr($params['shop_title']);
                unset($params['shop_title']);

                $listItem->whereHas('purchases', function($q) use($shopTitle){
                    $q->whereHas('shop', function($q1) use($shopTitle){
                        $q1->where('title', $shopTitle);
                    });
                });
            }

            # Query with order_item_url
            if(array_key_exists('order_item_url', $params)){
                $orderItemUrl = ValidateTools::toStr($params['order_item_url']);
                unset($params['order_item_url']);

                $listItem->whereHas('orderItems', function($q) use($orderItemUrl){
                    $q->where('url', $orderItemUrl);
                });
            }

            # Query with from_total and to_total
            $fromTotal = null;
            $toTotal = null;
            if(array_key_exists('from_total', $params)){
                $fromTotal = intVal($params['from_total']);
                unset($params['from_total']);
            }
            if(array_key_exists('to_total', $params)){
                $toTotal = intVal($params['to_total']);
                unset($params['to_total']);
            }
            if($fromTotal && $toTotal){
                $listItem->whereBetween('total', [$fromTotal, $toTotal]);
            }


            $listItemWithoutStatus = clone $listItem;
            $listItemForSum = clone $listItem;
            if(array_key_exists('status', $baseParams)){
                $listItem->where('status', $baseParams['status']);
            }

            $totalItems = 0;
            $totalRealAmount = 0;
            $totalDiscount = 0;
            $listItemForSum = $listItemForSum->get();
            foreach ($listItemForSum as $itemForSum) {
                $totalItems += $itemForSum->items;
                $totalRealAmount += $itemForSum->real_amount;
                $totalDiscount += $itemForSum->discount;
            }

            $totalAmountCny = floatval($listItemWithoutStatus->sum("amount"));
            $totalAmountVnd = floatval($listItemWithoutStatus->sum(\DB::raw('amount * rate')));
            $totalMass = floatval($listItemWithoutStatus->sum("mass"));
            $s_amount = '¥ '.number_format($totalAmountCny).' / '.number_format($totalAmountVnd).' ₫';
            $s_mass = $totalMass .' Kg';

            $totalInlandDeliveryFeeCny = floatval($listItemWithoutStatus->sum("inland_delivery_fee_raw"));
            $totalInlandDeliveryFeeVnd = floatval($listItemWithoutStatus->sum("inland_delivery_fee"));
            $s_inland_delivery_fee = implode("", [
                '¥ ', 
                number_format($totalInlandDeliveryFeeCny), 
                ' / ', 
                number_format($totalInlandDeliveryFeeVnd),
                ' ₫'
            ]);

            $totalOrderFeeCny = floatval($listItemWithoutStatus->sum("order_fee"));
            $totalOrderFeeVnd = floatval($listItemWithoutStatus->sum(\DB::raw('order_fee * rate')));
            $s_order_fee = '¥ '.number_format($totalOrderFeeCny).' / '.number_format($totalOrderFeeVnd).' ₫';

            $total = [
                'items' => $totalItems, # $listItemWithoutStatus->orderItems->count(),
                's_mass' => $s_mass,
                's_amount' => $s_amount,
                's_inland_delivery_fee' => $s_inland_delivery_fee,
                'delivery_fee' => $listItemWithoutStatus->sum("delivery_fee"),
                's_order_fee' => $s_order_fee,
                'sub_fee' => $listItemWithoutStatus->sum("sub_fee"),
                'real_amount' => $totalRealAmount,
                'discount' => $totalDiscount,
            ];

            // Get sum statistics here
            $totalStatus = [];
            foreach (config('app.list_order_status') as $status) {
                $query = clone $listItemWithoutStatus;
                $totalStatus[$status] = $query->where('status', $status)->count();
            }

            $listItem = $listItem->
                orderBy('id', 'desc')->
                paginate(config('app.page_size'));

            $extra = ['list_address' => []];
            if(array_key_exists('user_id', $params)){
                $extra['list_address'] = Address::
                    where('user_id', $params['user_id'])->
                    orderBy('id', 'asc')->
                    get();
            }
            $extra['total_status'] = $totalStatus;
            $extra['total'] = $total;
            /*
            $extra['list_admin'] = Admin::whereHas('role', function($q){
                $q->where('uid', 'nhan-vien-dat-hang')->orderBy('last_name', 'asc');
            })->get();
            */
            $extra['list_admin'] = Admin::
                select('id', 'first_name', 'last_name')->
                orderBy('last_name', 'asc')->get();
            $extra['list_user'] = User::
                select('id', 'uid', 'first_name', 'last_name', 'email', 'phone')->
                orderBy('id', 'asc')->get();
            return ResTools::lst($listItem, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function checkMistakeList() {
        # đơn hàng gd xong, hoàn thành 
        # tổng giá trị vận đơn nhỏ hơn giá trị đơn
        $result = [];
        $listItem = self::whereIn('status', ['purchased', 'done'])->orderBy('created_at')->get();
        foreach($listItem as $item) {
            $checkedTotal = $item->billsOfLanding->sum('total');
            if($item->total - $checkedTotal >= 1000) {
                $result[] = [
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'uid' => $item->uid,
                    'total' => $item->total,
                    'checked_total' => $checkedTotal
                ];
            }
        }
        return $result;
    }

    public static function exportMistakeList() {
        # đơn hàng gd xong, hoàn thành 
        # tổng giá trị vận đơn đã xuất nhỏ hơn giá trị đơn
        $listItem = self::whereIn('status', ['purchased', 'done'])->orderBy('created_at')->get();
        foreach($listItem as $item) {
            $exported = $item->billsOfLanding()->
                                whereNotNull('export_store_date')->
                                sum('total');
            if($item->total - $exported >= 1000) {
                $result[] = [
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'uid' => $item->uid,
                    'total' => $item->total,
                    'exported' => $exported
                ];
            }
        }
        return $result;
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
            $result->purchases;
            $extra = [
                'list_address' => ($result->user_id && $result->user)?Address::
                    where('user_id', $result->user->id)->
                    orderBy('id', 'asc')->
                    get():[],
                'list_admin' => Admin::whereHas('role', function($q){
                    $q->where('uid', 'nhan-vien-dat-hang')->orderBy('last_name', 'asc');
                })->get(),
                'list_check_item_status' => config('app.list_check_item_status')
            ];
            return ResTools::obj($result, $original, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function uploadCart($file, $executor=null){
        try{
            if(!count($file)){
                return ResTools::err(
                    'Bạn cần chọn 1 file excel để upload.',
                    ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
                );
            }
            $file = $file['list_item'];
            $file_name = $file['tmp_name'];
            $totalItem = 0;
            $error = null;
            $listItem = [];
            \Excel::load($file_name, function($reader) use($executor, $totalItem, &$listItem, &$error) {
                try{
                    // mabill, madiachi, kg, dai, rong, cao, soluong, ppvnd, ghichu
                    $listItemRaw = $reader->toArray();
                    foreach ($listItemRaw as $item) {
                        if(
                            !trim($item['link_san_pham']) || 
                            !ValidateTools::toInt($item['so_luong']) || 
                            !ValidateTools::toFloat($item['don_gia_web'])
                        ){
                            return;
                        }
                        $listItem[] = [
                            'id' => mt_rand(999999, 9999999999),
                            'title' => trim($item['link_san_pham']),
                            'properties' => implode(';', [$item['mau_sac_thuoc_tinh'], $item['size_neu_co']]),
                            'quantity' => ValidateTools::toInt($item['so_luong']),
                            'shop_name' => trim($item['ten_shop'])?:null,
                            'shop_uid' => trim($item['ten_shop'])?:null,
                            'avatar' => trim($item['link_hinh_anh']),
                            'unit_price' => ValidateTools::toFloat($item['don_gia_web']),
                            'message' => trim($item['ghi_chu']),
                            'url' => trim($item['link_san_pham']),
                            'vendor' => Tools::getVendorFromUrl($item['link_san_pham']),
                            'rate' => ($executor&&$executor->rate)?$executor->rate:intVal(\ConfigDb::get('cny-vnd'))
                        ];
                    }
                }
                catch(\Exception $e){$error = $e;}
                catch(\Error $e){$error = $e;}
            });
            # var_dump($error->getMessage());die;
            if($error){
                $errorMessage = implode(". ", [
                    "Lỗi trong quá trình đọc excel",
                    "Bạn hãy kiểm tra lại cấu trúc của file excel vừa upload",
                    "Lưu ý: File excel chỉ được chứa 1 sheet duy nhất"
                ]);
                return ResTools::err(
                    $errorMessage,
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            return ResTools::lst($listItem);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function recalculate($id, $markUpdate=true){
        $order = self::find($id);
        if($order){
            $oldTotal = $order->total;
            if(Purchase::recalculateAll($id)){
                $listPurchase = Purchase::where('order_id', $id)->get();
                $mass = 0;
                $sub_fee = 0;
                $packages = 0;
                $number_of_bills_of_landing = 0;
                $inland_delivery_fee = 0;
                $inland_delivery_fee_raw = 0;
                $delivery_fee = 0;
                $order_fee = 0;
                $amount = 0;
                $total_raw = 0;
                $total = 0;

                foreach ($listPurchase as $purchase) {
                    $mass += $purchase->mass;
                    // print_r("\n".$purchase->mass."\n");
                    $packages += $purchase->packages;
                    $number_of_bills_of_landing += $purchase->number_of_bills_of_landing;
                    $delivery_fee += $purchase->delivery_fee;
                    $inland_delivery_fee += $purchase->inland_delivery_fee;
                    $inland_delivery_fee_raw += $purchase->inland_delivery_fee_raw;
                    $sub_fee += $purchase->sub_fee;
                    $order_fee += $purchase->amount * $order->order_fee_factor / 100;
                    $amount += $purchase->amount;
                    $total_raw += $purchase->total_raw;
                    $total += $purchase->total;
                }
                # print_r($inland_delivery_fee_raw);die;
                /*
                $order_fee = $amount * $order->order_fee_factor / 100;
                $total_raw = $amount + $order_fee + $inland_delivery_fee_raw;
                $total = $total_raw * $order->rate + $delivery_fee + $sub_fee;
                */
                // print_r("\n".$mass."\n");
                $order->update([
                    'number_of_purchases' => $listPurchase->count(),
                    'mass' => $mass,
                    'packages' => $packages,
                    'number_of_bills_of_landing' => $number_of_bills_of_landing,
                    'delivery_fee' => $delivery_fee,
                    'inland_delivery_fee' => $inland_delivery_fee,
                    'inland_delivery_fee_raw' => $inland_delivery_fee_raw,
                    'sub_fee' => $sub_fee,
                    'order_fee' => $order_fee,
                    'amount' => $amount,
                    'total_raw' => $total_raw,
                    'total' => $total
                ]);
                $transaction = UserTransaction::where(['order_id' => $order->id, 'type' => 'GD'])->first();
                if ($transaction) {
                    $transaction->amount = floor($total);
                    $transaction->save();
                    UserTransaction::recalculate($transaction);
                }
                /*
                $delta = $total - $oldTotal;
                if(abs($delta) >= 0.001 && !in_array($order->status, ['draft', 'new']) && $markUpdate){
                    $transactionData = [
                        'user_id' => $order->user_id,
                        'type' => 'TD',
                        'money_type' => '-',
                        'amount' => $delta,
                        'order_id' => $order->id,
                        'note' => 'Đổi giá trị đơn hàng: '.$order->uid
                    ];
                    UserTransaction::addItem($transactionData, null);
                }
                 */
            }
            return self::find($order->id);
        }
        return [];
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

            $defaultAddressId = Address::
                where(['user_id' => $input['user_id'], 'default' => true])->
                first()->id;
            if(!array_key_exists('address_id', $input)){
                $input['address_id'] = $defaultAddressId;
            }else{
                if(!$input['address_id'] || !Address::where('id', $input['address_id'])->get()){
                    $input['address_id'] = $defaultAddressId;
                }
            }
            $input['real_rate'] = RateLog::latestRate();
            $item = self::create($input);

            $today = Tools::nowDate();
            $item->uid = $item->address->uid.(
                $today->day>9?(string)$today->day:'0'.(string)$today->day
            ).Tools::monthToChar($today->month).$item->order;
            $item->rate = $item->user->rate?:RateLog::latestOrderRate();
            $item->order_fee_factor = $item->user->order_fee_factor;
            $item->deposit_factor = $item->user->deposit_factor;
            $item->complain_day = $item->user->complain_day;
            $item->admin_id = $item->user->dathang_admin_id;
            $item->save();

            UserOrderLog::addOrder($item);
            return ResTools::obj(self::find($item->id), trans('messages.add_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function addItemFull($listOrderItem, $draft, $userId){
        try{
            # Step 1: Add blank order
            $orderData = [
                'user_id' => $userId,
                'uid' => 'default',
                'order' => 0
            ];
            if($draft){
                $orderData['status'] = 'draft';
            }
            if(count($listOrderItem) === 1 && array_key_exists('order_id', $listOrderItem[0])){
                # User manual add order item by form
                $order = self::find(ValidateTools::toInt($listOrderItem[0]['order_id']));
            }else{
                # User add order item by extension
                $order = self::find(self::addItem($orderData)['data']['id']);
            }

            # Step 2: Loop all order item
            $total = 0;
            foreach ($listOrderItem as $orderItem) {
                # Ensure user is the executor
                $orderItem['user_id'] = $userId;

                if(!array_key_exists('properties', $orderItem)){
                    $orderItem['properties'] = null;
                }
                if(!array_key_exists('title', $orderItem)){
                    $orderItem['title'] = $orderItem['url'];
                }

                # Add shop if not exist
                if(array_key_exists('shop_name', $orderItem)){
                    # Data for new shop
                    if(!array_key_exists('shop_uid', $orderItem)){
                        $orderItem['shop_uid'] = $orderItem['shop_name'];
                    }
                    $shopData = [
                        'uid' => $orderItem['shop_uid'],
                        'title' => $orderItem['shop_name'],
                        'vendor' => $orderItem['vendor']
                    ];
                }else{
                    # Use default shop
                    $shopData = [
                        'uid' => '000',
                        'title' => 'Shop khác',
                        'vendor' => 'KHAC'
                    ];
                }
                # Check shop exist
                $shop = Shop::where('title', $shopData['title'])->orWhere('uid', $shopData['uid'])->first();
                if(!$shop){
                    # If not exist: create new
                    $shop = Shop::addItem($shopData);
                    $shop = Shop::find($shop['data']['id']);
                }

                # Add purchase if not exist
                $purchaseData = [
                    'user_id' => $order->user->id,
                    'order_id' => $order->id,
                    'shop_id' => $shop->id
                ];
                # Check purchase exist
                $purchase = Purchase::where($purchaseData)->first();
                if(!$purchase){
                    # Add new purchase
                    if ($order->user->delivery_fee_unit) {
                        $purchaseData['delivery_fee_unit'] = $order->user->delivery_fee_unit;
                    } else {
                        $purchaseData['delivery_fee_unit'] = $order->address->areaCode->delivery_fee_unit;
                    }
                    $purchase = Purchase::find(Purchase::addItem($purchaseData)['data']['id']);
                }

                # Add order item
                $data = array_merge($orderItem, [
                    'order_id' => $order->id,
                    'shop_id' => $shop->id,
                    'purchase_id' => $purchase->id
                ]);
                if(!$data['unit_price']){
                    $data['unit_price'] = 0;
                }
                OrderItem::addItem($data);
                if(array_key_exists('id', $orderItem)){
                    CartItem::removeItem(intVal($orderItem['id']));
                }
            }

            # Update order total and amout
            # $order->total = intval($total*$order->rate);
            # $order->amount = $order->total;
            # $order->save();

            $order = self::recalculate($order->id);
            $order->purchases;
            $extra = [
                'order' => [
                    'data' => $order,
                    'extra' => [
                        'list_address' => Address::
                            where('user_id', $order->user->id)->
                            orderBy('id', 'asc')->
                            get(),
                        'list_admin' => Admin::whereHas('role', function($q){
                            $q->where('uid', 'nhan-vien-dat-hang')->orderBy('last_name', 'asc');
                        })->get()
                    ]
                ]
            ];
            return ResTools::obj(['id' => $order->id], trans('messages.add_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function updateDeliveryFeeUnit($id, $executor){
        try{
            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $listPurchase = Purchase::where('order_id', $item->id)->get();
            foreach ($listPurchase as $purchase) {
                # var_dump($item->address->areaCode->delivery_fee_unit);die;
                if ($item->user->delivery_fee_unit) {
                    $purchase->delivery_fee_unit = $item->user->delivery_fee_unit;
                } else {
                    $purchase->delivery_fee_unit = $item->address->areaCode->delivery_fee_unit;
                }
                $purchase->save();
            }
            $result = self::recalculate($item->id);
            $result->purchases;
            $extra = [
                'list_address' => ($result->user_id && $result->user)?Address::
                    where('user_id', $result->user->id)->
                    orderBy('id', 'asc')->
                    get():[],
                'list_admin' => Admin::whereHas('role', function($q){
                    $q->where('uid', 'nhan-vien-dat-hang')->orderBy('last_name', 'asc');
                })->get()
            ];
            return ResTools::obj($result, trans('messages.add_success'), $extra);
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
            # $isConfirm = false;
            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if (!in_array($item->status, ['draft', 'new'])) {
                if ($executor->role->role_type_uid === 'user') {
                    return ResTools::err(
                        'Đơn hàng đã chốt, bạn không thể sửa.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }

                if($item->billsOfLanding()->whereNotNull('export_store_date')->count()){
                    if (count($input) != 1 || !array_key_exists('status', $input)) {
                        return ResTools::err(
                            'Đơn hàng đã xuất, bạn không thể sửa.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                }

                if (array_key_exists("address_id", $input)) {
                    unset($input["address_id"]);
                }
                if (array_key_exists("rate", $input)) {
                    unset($input["rate"]);
                }
                if (array_key_exists("order_fee_factor", $input)) {
                    unset($input["order_fee_factor"]);
                }
            }
            if($executor->role->uid !== config('app.sadmin') && $item->status === 'done'){
            # if($item->status === 'done'){
                return ResTools::err(
                    'Đơn hàng đã chốt, bạn không thể sửa.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            // if(count($input) === 1 && array_key_exists('status', $input)){
            if(array_key_exists('status', $input)){
                if($executor->role->uid !== config('app.sadmin')){
                    # Không phải admin thì không được chỉnh trạng thái
                    unset($input['status']);
                }else{
                    if($input['status'] !== 'purchased' || $item->status !== 'done'){
                        # Admin chỉ được chỉnh trạng thái từ done qua purchased
                        # unset($input['status']);
                    }
                }
            }

            $oldItem = clone $item;
            $item->update($input);
            $item = self::find($item->id);

            if($oldItem->address_id !== $item->address_id){
                $today = Tools::nowDate();
                $item->uid = $item->address->uid.(
                    $today->day>9?(string)$today->day:'0'.(string)$today->day
                ).Tools::monthToChar($today->month).$item->order;
                $item->save();
                $listPurchase = Purchase::where('order_id', $item->id)->get();
                foreach ($listPurchase as $purchase) {
                    # var_dump($item->address->areaCode->delivery_fee_unit);die;
                    if ($item->user->delivery_fee_unit) {
                        $purchase->delivery_fee_unit = $item->user->delivery_fee_unit;
                    } else {
                        $purchase->delivery_fee_unit = $item->address->areaCode->delivery_fee_unit;
                    }
                    $purchase->save();
                }
            }
            # $item = self::recalculate($item->id, $isConfirm?false:true);
            $item = self::recalculate($item->id);
            $item->purchases;

            UserOrderLog::editOrder($item, $oldItem, $executor);
            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function draftToNew($id, $executor){
        try{
            $item = self::where('id', $id)->where('user_id', $executor->id)->first();
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if($item->status === 'draft'){
                $item->status = 'new';
                $item->save();
            }
            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function massConfirm($id, $executor){
        try{
            $result = null;
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->where('status', 'new')->whereNotNull('admin_id')->get();
            $listId = [];
            // if($listItem->count()){
            foreach ($listItem as $item) {
                $oldItem = clone $item;
                if($item->admin_id){
                    $oldRate = $item->rate;
                    $item->status = 'confirm';
                    $item->confirm_admin_id = $executor->id;
                    $item->confirm_date = Tools::nowDateTime();
                    $item->real_rate = RateLog::latestRate();
                    $item->rate = $item->user->rate?:RateLog::latestOrderRate();
                    $item->save();
                    if(abs($oldRate - $item->rate) > 1){
                        $item = self::recalculate($item->id, false);
                    }
                    $transactionData = [
                        'user_id' => $item->user_id,
                        'type' => 'GD',
                        'amount' => $item->total,
                        'money_type' => '-',
                        'order_id' => $item->id,
                        'note' => 'Duyệt mua đơn hàng: '.$item->uid
                    ];
                    UserTransaction::addItem($transactionData, $executor);
                    $listId[] = $item->id;
                }

                UserOrderLog::confirmOrder($oldItem, $executor);
            }
            $result = ['id' => $listId];
            /*
            }else{
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            */
            return ResTools::obj($result, 'Duyệt đơn thành công.');
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeItem($id, $executor, $force=false){
        try{
            $result = null;
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->get();
            if($listItem->count()){
                foreach ($listItem as $item) {
                    if($executor->role->role_type_uid === 'user' && !in_array($item->status, ['draft', 'new'])){
                        return ResTools::err(
                            'Đơn hàng đã chốt, bạn không thể xoá.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }

                    if($item->billsOfLanding()->whereNotNull('export_store_date')->count()){
                        return ResTools::err(
                            'Đơn hàng đã xuất, bạn không thể sửa.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }

                    if($executor->role->uid !== config('app.sadmin') && $item->status === 'done'){
                    # if($item->status === 'done'){
                        return ResTools::err(
                            'Đơn hàng đã chốt, bạn không thể xoá.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }

                    $oldItem = clone $item;
                    if($force){
                        $item->forceDelete();
                    }else{
                        $item->delete();
                    }
                    UserOrderLog::removeOrder($oldItem, $executor);
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

    public static function download($id, $uid){
        \Excel::create("Đơn hàng $uid", function($excel) use($id, $uid){
            $excel->sheet("Đơn hàng $uid", function($sheet) use($id, $uid){
                $params = [
                    'id' => intVal($id),
                    'uid' => $uid,
                ];
                #get order by id, uid
                $order = Order::where($params)->first();
                #get purchase
                $listPurchase = $order->purchases;
                #list user from order -> user by relationship user();
                $user = $order->user;

                $sheet->cell("A6", "Sản phẩm");
                $sheet->cell("B6", "Ảnh");
                $sheet->cell("C6", "Link gốc");
                $sheet->cell("D6", "Thuộc tính");
                $sheet->cell("E6", "Số lượng");
                $sheet->cell("F6", "Đơn giá");

                $sheet->setSize("A6:F6", 70,30);
                $sheet->cells("A6:F6", function ($cells) {
                     $cells->setFont(array(
                         'name' => 'Times New Roman',
                         'size' => 13,
                         'bold' => true
                     ));
                         $cells->setFontColor("#333");
                         $cells->setAlignment('center');
                         $cells->setValignment('center');
                });

                $sheet->cell("A1", "Họ tên:");
                $sheet->cell("A2", "Số điện thoại:");
                $sheet->cell("A3", "Email:");
                $sheet->cell("A4", "Địa chỉ:");

                $sheet->cell("C1", "Tỷ Giá(￥):");
                $sheet->cell("C2", "Cân Nặng (Kg ):");
                $sheet->cell("C3", "Tiền Vận Chuyển:");
                $sheet->cell("C4", "Phí Dịch Vụ:");
                $sheet->setSize("C1:C4", 20,30);

                $sheet->cell("E1", "Phí vận chuyển nội địa:");
                $sheet->cell("E2", "Tiền bảo hiểm");
                $sheet->cell("E3", "Phụ phí:");
                $sheet->cell("E4", "Tổng phí:");

                $sheet->cell("F1", $order->inland_delivery_fee?:"+0");
                $sheet->cell("F2", $order->insurance_fee?:"+0");
                $sheet->cell("F3", $order->sub_fee?:"+0");
                $sheet->cell("F4", $order->total?:"+0");

                $sheet->cell("B1", $user->full_name);
                $sheet->cell("B2", $user->phone);
                $sheet->cell("B3", $user->email);
                $sheet->cell("B4", $order->address->address);
                $sheet->setSize("B1:B4", 30,30);

                $sheet->cell("D1", $order->rate?:'+0');
                $sheet->cell("D2", $order->mass?:'+0');
                $sheet->cell("D3", $order->delivery_fee?:'+0');
                $sheet->cell("D4", $order->order_fee?:'+0');
                #setHeight for cell
                $sheet->setSize("B1:B4" , 30, 20);
                $sheet->setSize("A1:A4" , 20, 30);
                $sheet->setSize("A5:F5" , 20, 20);

                $sheet->cells("A1:F5", function ($cells) {
                     $cells->setFont(array(
                         'name' => 'Times New Roman',
                         'size' => 8,
                         'bold' => true
                     ));
                         $cells->setFontColor("#333");
                         $cells->setAlignment('center');
                         $cells->setValignment('center');
                });
                $sheet->mergeCells("A5:F5");

                #$index: index start row in excel
                $index = 7;
                #loop for list order in order_item for get shop name
                foreach ($listPurchase as $purchase) {
                    # code...
                    $sheet->cell("A$index", "Shop:  ".$purchase->shop->title);
                    $sheet->cell("A$index:F$index", function($cell){
                        $cell->setBackground('##30b27e');
                    });

                    $sheet->setSize("A$index", 70, 30);
                    $listOrderItem = $purchase->orderItems;
                    #for list order item for shop name
                    foreach ($listOrderItem as $key => $orderItem) {
                        $index++;
                        # STT for list product
                        $sheet->cells("A$index:F$index", function ($cells) {
                             $cells->setFont(array(
                                 'name' => 'Times New Roman',
                                 'size' => 8,
                                 'bold' => true,

                             ));
                             $cells->setFontColor("#333");
                             $cells->setAlignment('center');
                             $cells->setValignment('center');
                        });
                        $keyProduct = $key + 1;
                        $sheet->cell("A$index", ($keyProduct)."  -  ".$orderItem->title);
                        $sheet->setSize("A$index", 70, 40);
                        try{
                            if(
                                file_exists(config('app.media_root').$orderItem->local_avatar) && 
                                $orderItem->local_avatar
                            ){
                                $objDrawing = new PHPExcel_Worksheet_Drawing;
                                $objDrawing->setHeight(1);
                                $objDrawing->setWidth(40);
                                $objDrawing->setPath(
                                    public_path('media/'.$orderItem->local_avatar)
                                ); //your image path
                                $objDrawing->setCoordinates("B$index");
                                $objDrawing->setWorksheet($sheet);
                            }
                        }catch(\Exception $e){
                            // Do nothing
                        }catch(\Error $e){
                            // Do nothing
                        }

                        $sheet->cell("C$index", 'Link gốc');
                        $sheet->getCell("C$index")->setDataType('str');
                        $sheet->getCell("C$index")->getHyperlink()->setUrl($orderItem->url);
                        $sheet->cell("D$index", $orderItem->properties);
                        $sheet->cell("E$index", $orderItem->quantity?:0);
                        $sheet->cell("F$index", $orderItem->unit_price?:0);
                        // $sheet->setSize("C$index:F$index", 40, 30);
                    }
                    $index++;
                }
            });
        })->download('xls');
    }

    public function delete(){
        if(config('app.app_env') !== 'testing'){
            $orderItems = $this->orderItems;
            foreach($orderItems as $orderItem) $orderItem->delete();

            $billsOfLanding = $this->billsOfLanding;
            foreach($billsOfLanding as $billOfLanding) $billOfLanding->delete();

            $purchases = $this->purchases;
            foreach($purchases as $purchase) $purchase->delete();

            $userTransactions = $this->userTransactions;
            foreach($userTransactions as $userTransaction) $userTransaction->delete();
        }
        return parent::delete();
    }

    public function save(array $options = array()){
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        if(in_array('updated_at', $colums)){
            # $this->updated_at = Tools::nowDateTime();
            $this->updated_at = Tools::nowDateTime();
        }
        if($this->delivery_fee){
            $this->delivery_fee = round($this->delivery_fee);
        }
        if(!$this->exists){
            if(in_array('created_at', $colums)){
                # $this->created_at = Tools::nowDateTime();
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
                    $largestOrderItem = self::
                        whereDate('created_at', '=', Tools::nowDate())->
                        orderBy('order', 'desc')->first();
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
