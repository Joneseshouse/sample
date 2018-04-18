<?php

namespace App\Modules\BillOfLanding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Order\Models\Order;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;
use App\Modules\VnBillOfLanding\Models\VnBillOfLanding;
use App\Modules\Address\Models\Address;
use App\Modules\User\Models\User;
use App\Modules\UserOrderLog\Models\UserOrderLog;
use App\Modules\CheckBill\Models\CheckBill;
use App\Modules\CheckItem\Models\CheckItem;
use App\Modules\CollectBol\Models\CollectBol;
use App\Modules\UserTransaction\Models\UserTransaction;


class BillOfLanding extends Model{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'bills_of_landing';
    protected $appends = [
        'title',
        'address_code',
        'purchase_code',
        'landing_status',
        'check_staff_fullname'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'purchase_id',
        'address_id',
        'order_type',
        'code',
        'init_mass',
        'mass',
        'input_mass',
        'calculated_mass',
        'packages',
        'length',
        'width',
        'height',
        'transform_factor',
        'sub_fee',
        'insurance_register',
        'insurance_factor',
        'insurance_value',
        'insurance_fee',
        'insurance_fee_raw',
        'check_staff_id',
        'checked_date',
        'cn_store_date',
        'vn_store_date',
        'export_store_date',
        'complain_date',
        'export_bill_id',
        'rate',
        'delivery_fee',
        'delivery_fee_unit',
        'note',
        'total',
        'total_raw',
        'amount',
        'amount_raw',
        'order_fee',
        'address_code',
        'purchase_code',
        'landing_status',
        'wooden_box',
        'straight_delivery',

        'complain_amount',
        'complain_resolve',
        'complain_change_date',
        'complain_turn',
        'complain_type', // change, change_discount, reject, accept_discount
        'complain_note_user',
        'complain_note_admin'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'order',
        'purchase',
        'checkStaff',
    ];

    public static $fieldDescriptions = [
        'order_id' => 'int',
        'address_id' => 'int',
        'purchase_id' => 'int',
        'code' => 'str,max:70',
        'packages' => 'int',
        'transform_factor' => 'int',
        'input_mass' => 'float',
        'length' => 'int',
        'width' => 'int',
        'height' => 'int',
        'insurance_register' => 'bool',
        'insurance_value' => 'float',
        'note' => 'str',
        'address_code' => 'str',
        'purchase_code' => 'str',
        'landing_status' => 'str',
        'wooden_box' => 'bool',
        'straight_delivery' => 'bool',

        'complain_amount' => 'int',
        'complain_resolve' => 'bool',
        'complain_change_date' => 'date',
        'complain_turn' => 'str',
        'complain_type' => 'str', // change, change_discount, reject, accept_discount
        'complain_note_user' => 'str',
        'complain_note_admin' => 'str'
    ];

    public static $searchFields = ['code'];

    public function order(){
        return $this->belongsTo('App\Modules\Order\Models\Order', 'order_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Modules\Purchase\Models\Purchase', 'purchase_id');
    }

    public function address(){
        return $this->belongsTo('App\Modules\Address\Models\Address', 'address_id');
    }

    public function checkBills(){
        return $this->hasMany('App\Modules\CheckBill\Models\CheckBill', 'bill_of_landing_id');
    }

    public function checkItems(){
        return $this->hasMany('App\Modules\CheckItem\Models\CheckItem', 'bol_id');
    }

    public function checkStaff(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'check_staff_id');
    }

    public function getCheckStaffFullnameAttribute($value){
        if($this->check_staff_id && $this->checkStaff){
            return $this->checkStaff->fullname;
        }
        return null;
    }

    public function getTitleAttribute($value){
        return $this->code;
    }

    public function getAddressCodeAttribute($value){
        if($this->address_id){
            if($this->address){
                return $this->address->uid;
            }
        }
        return null;
    }

    public function getPurchaseCodeAttribute($value){
        if($this->purchase_id && $this->purchase){
            return $this->purchase->code;
        }
        return null;
    }

    public function getLandingStatusAttribute($value){
        if($this->export_store_date){
            return 'Đã xuất: '.ValidateTools::toDate($this->export_store_date)->format('d/m/Y');
        }
        if($this->vn_store_date){
            return 'Về VN: '.ValidateTools::toDate($this->vn_store_date)->format('d/m/Y');
        }
        if($this->cn_store_date){
            return 'Về TQ: '.ValidateTools::toDate($this->cn_store_date)->format('d/m/Y');
        }
        return 'Mới';
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $startDate = null;
            $endDate = null;
            $date = null;
            if(array_key_exists('start_date', $params)){
                $startDate = ValidateTools::toDate($params['start_date']);
                unset($params['start_date']);
            }
            if(array_key_exists('end_date', $params)){
                $endDate = ValidateTools::toDate($params['end_date']);
                unset($params['end_date']);
            }
            if(array_key_exists('date', $params)){
                $date = ValidateTools::toDate($params['date']);
                unset($params['date']);
            }

            $landingStatusFilter = null;
            if(array_key_exists('landing_status_filter', $params)){
                $landingStatusFilter = $params['landing_status_filter'];
                unset($params['landing_status_filter']);
            }

            $woodenBoxFilter = null;
            if(array_key_exists('wooden_box_filter', $params)){
                $woodenBoxFilter = $params['wooden_box_filter'];
                unset($params['wooden_box_filter']);
            }

            $userFilter = null;
            if(array_key_exists('user_filter', $params)){
                $userFilter = ValidateTools::toInt($params['user_filter']);
                unset($params['user_filter']);
            }

            $type = null;
            if(array_key_exists('type', $params)){
                $type = $params['type'];
                unset($params['type']);
            }

            $listItem = BillOfLanding::where($params);

            if($startDate && $endDate){
                $listItem->
                    whereDate('created_at', '>=', $startDate)->
                    whereDate('created_at', '<=', $endDate);
            }else{
                if($date){
                    $listItem->
                        whereDate('created_at', '>=', $date)->
                        whereDate('created_at', '<=', $date);
                }
            }

            if($type){
                switch($type){
                    case 'order':
                        $listItem->whereNotNull('purchase_id')->where('purchase_id', '!=', 0);
                    break;
                    case 'deposit':
                        # $listItem->whereNull('purchase_id')->whereNotNull('address_id');
                        $listItem->whereNotNull('address_id')->where(function($q){
                            $q->whereNull('purchase_id')->orWhere('purchase_id', 0);
                        });
                    break;
                    case 'missing':
                        $listItem->whereNull('purchase_id')->whereNull('address_id');
                    break;
                    case 'checked':
                        $listItem->whereNotNull('checked_date');
                    break;
                    case 'unchecked':
                        $listItem->whereNull('checked_date');
                    break;
                }
            }

            if($landingStatusFilter){
                switch($landingStatusFilter){
                    case 'new':
                        $listItem->whereNull('cn_store_date')->whereNull('vn_store_date');
                    break;
                    case 'cn':
                        $listItem->whereNotNull('cn_store_date')->whereNull('vn_store_date');
                    break;
                    case 'vn':
                        $listItem->whereNotNull('vn_store_date')->whereNull('export_store_date');
                    break;
                    case 'export':
                        $listItem->whereNotNull('export_store_date');
                    break;
                    case 'complain':
                        $listItem->whereNotNull('complain_date');
                    break;
                    default:
                        $listItem->whereNull('cn_store_date')->whereNull('vn_store_date');
                }
            }

            if($woodenBoxFilter){
                $listItem->where('wooden_box', $woodenBoxFilter);
            }

            if($userFilter){
                $listItem->where('user_id', $userFilter);
            }

            $orderBy = Tools::parseOrderBy($orderBy);
            if($keyword && strlen($keyword) >=3){
                $listItem->
                    where('code', 'ilike', '%' . $keyword . '%')->
                    orWhereHas('address', function($query) use($keyword){
                        $query->where('uid', 'ilike', '%' . $keyword . '%');
                    })->
                    orWhereHas('purchase', function($query) use($keyword){
                        $query->where('code', 'ilike', '%' . $keyword . '%');
                    });
            }

            $total = [
                'mass' => $listItem->sum('mass'),
                'packages' => $listItem->sum('packages'),
                'delivery_fee' => $listItem->sum('delivery_fee'),
                'insurance_fee' => $listItem->sum('insurance_fee'),
                'sub_fee' => $listItem->sum('sub_fee'),
                'total' => $listItem->sum('total')
            ];

            $listItem = $listItem->
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            if(array_key_exists('user_id', $params)){
                $extra = [
                    'list_address' => Address::where('user_id', $params['user_id'])->orderBy('uid', 'asc')->get(),
                    'list_user' => []
                ];
            }else{
                $extra = [
                    'list_address' => [],
                    'list_user' => User::
                        select('id', 'uid', 'first_name', 'last_name', 'email')->
                        orderBy('last_name', 'asc')->get()
                ];
            }
            $extra['total'] = $total;
            return ResTools::lst($listItem, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function listPure($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            $listItem = BillOfLanding::
                whereNotNull('user_id')->
                whereNotNull('vn_store_date')->
                whereNull('export_bill_id')->
                where(function($q){
                    $q->whereNull('order_id')->
                        orWhere(function($q1){
                            $q1->whereNotNull('order_id')->whereNotNull('checked_date');
                        });
                });
            if(array_key_exists('address_uid', $params)){
                $listItem->whereHas('address', function($q) use($params){
                    $q->where('uid', strtoupper($params['address_uid']));
                });
                unset($params['address_uid']);
            }

            if($keyword && strlen($keyword) >=3){
                $listItem->where('code', strtoupper($keyword));
            }

            $listItem = $listItem->
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            return ResTools::lst($listItem);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function listCheckBill($params=[], $keyword=null, $orderBy='-id'){
        try{
            $bolCode = strtoupper(trim($params['code']));
            /*
            if(CheckBill::whereHas('billOfLanding', function($q) use($bolCode){
                $q->where('code', $bolCode);
            })->first()){
                return ResTools::err(
                    trans('messages.bill_checked'),
                    ResTools::$ERROR_CODES['NOT_FOUND']
                );
            }
            */
            $item = self::
                where('code', $bolCode)->
                whereNotNull('cn_store_date')->
                # whereNull('export_bill_id')-> # Tạm thời để cho check lại
                orderBy('id', 'asc')->
                first();
            if(!$item){
                # Bill not exist
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['NOT_FOUND']
                );
            }

            $result = [];
            $listChecked = [];
            # foreach ($listItem as $item) {
            $originalItem = self::
                select(
                    'id', 
                    'code', 
                    'packages', 
                    'input_mass', 
                    'length', 
                    'width', 
                    'height', 
                    'insurance_register', 
                    'insurance_value', 
                    'sub_fee', 
                    'note'
                )->find($item->id);
            if($item->purchase_id && $item->purchase){
                $item->purchase->orderItems;
                $item->purchase->bill_of_landing = $originalItem;
                CheckItem::sync($item, $item->purchase->orderItems);

                $purchase = $item->purchase;
                $orderItems = $item->purchase->orderItems;

                foreach ($orderItems as $key => $orderItem) {
                    $checkedItem = CheckItem::
                        where('bol_id', $item->id)->
                        where('order_item_id', $orderItem->id)->
                        first();
                    $orderItems[$key]->checking_quantity = (
                        ($checkedItem && $checkedItem->quantity)?
                        $checkedItem->quantity:
                        0
                    );
                    $orderItems[$key]->checking_status = (
                        ($checkedItem && $checkedItem->status)?
                        $checkedItem->status:
                        ''
                    );
                }

                $purchase->order_items = $orderItems;

                $listChecked = CheckItem::where('bol_id', $item->id)->get();
                $result[] = $purchase;
            }
            # }
            $extra = [
                'list_checked' => $listChecked
            ];
            return ResTools::lst($result, $extra);
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

    public static function checkDuplicateCode($code){
        try{
            $result = ['duplicate' => self::where('code', $code)->count()?true:false];
            return ResTools::obj($result);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function recalculate($item, $isAdd=false, $recalculatePurcase=true){
        # Có vận đơn có nghĩa là đơn đã được duyệt, các biến sau được giữ nguyên:
        # delivery_fee_unit
        # rate
        # order_fee_factor
        if($item->order_id && $item->order){
            $item->delivery_fee_unit = $item->delivery_fee_unit?:(
                $item->order->user->delivery_fee_unit
                ?:
                $item->order->address->areaCode->delivery_fee_unit
            );
            $item->rate = $item->rate?:$item->order->rate;
        }else{
            if($item->address_id && $item->address){
                $item->delivery_fee_unit = $item->delivery_fee_unit?:(
                    $item->address->user->delivery_fee_unit
                    ?:
                    $item->address->areaCode->delivery_fee_unit
                );
                $item->rate = $item->rate?:$item->address->user->rate;
            }
        }
        /*
        if($item->order_id && $item->order){
            $item->delivery_fee_unit = (
                $item->order->user->delivery_fee_unit
                ?:
                $item->order->address->areaCode->delivery_fee_unit
            );
            $item->rate = $item->rate?:$item->order->rate;
        }else{
            if($item->address_id && $item->address){
                $item->delivery_fee_unit = (
                    $item->address->user->delivery_fee_unit
                    ?:
                    $item->address->areaCode->delivery_fee_unit
                );
                $item->rate = $item->rate?:$item->address->user->rate;
            }
        }
        */
        # Calculating mass
        $oldMass = $item->mass;
        $item->init_mass = $item->mass?:0;
        $item->calculated_mass = floatVal($item->length * $item->width * $item->height / $item->transform_factor);
        if($item->calculated_mass > $item->input_mass){
            $item->mass = $item->calculated_mass;
        }else{
            $item->mass = $item->input_mass;
        }
        $item->save();

        if($item->order && $recalculatePurcase){
            # Recalculate mass of purchase and all inland_delivery_fee of BoL
            Purchase::recalculate($item->purchase_id);
        }

        $item = self::find($item->id);

        # Calculating delivery fee
        $item->delivery_fee = $item->delivery_fee_unit * $item->mass;

        if (abs($oldMass - $item->mass) > 0.001) {
            // Mass changed -> update VC transaction
            if (!$item->purchase_id && $item->user_id) {
                // No purchase ID -> transport bol
                $transactionData = [
                    'user_id' => $item->user_id,
                    'type' => 'VC',
                    'amount' => floor(abs($item->delivery_fee)),
                    'money_type' => '-',
                    'note' => 'Tiền đơn VC: '.$item->code,
                    'bol_id' => $item->id
                ];
                $transaction = UserTransaction::
                    where('bol_id', $item->id)->
                    where('type', 'VC')->
                    first();
                if (!$transaction) {
                    UserTransaction::addItem($transactionData, 0);
                } else {
                    UserTransaction::editItem($transaction->id, $transactionData, 0);
                }
            }
        }

        # Calculating insurance fee
        if($item->order_id){
                $item->insurance_factor = 0;
                $item->insurance_fee = 0;
                $item->insurance_fee_raw = 0;
                $item->insurance_value = 0;
        }else{
            if($item->insurance_register){
                $item->insurance_factor = 3;
                $item->insurance_fee = $item->insurance_value*$item->insurance_factor/100*$item->rate;
                $item->insurance_fee_raw = $item->insurance_value*$item->insurance_factor/100;
            }else{
                $item->insurance_factor = 0;
                $item->insurance_fee = $item->delivery_fee*$item->insurance_factor/100;
                $item->insurance_fee_raw = 0;
                $item->insurance_value = 0;
            }
        }

        if($item->order){
            $item->amount = $item->amount_raw * $item->rate;
            $item->order_fee = $item->amount_raw * $item->order->order_fee_factor / 100;
        }

        # Calculate inland delivery raw
        # Do not touch to exported bols
        # Just recalculate the others
        if(
            !$item->export_store_date &&
            $item->purchase_id &&
            $item->purchase
            # $item->purchase->inland_delivery_fee_raw
        ){
            $listExportedBol = $item->purchase->billsOfLanding()->whereNotNull('export_store_date');

            $remainBol = $item->purchase->billsOfLanding()->whereNull('export_store_date')->count();
            # So luong bill chua duoc xuat cua shop

            $purchaseInlandDeliveryFeeRaw = $item->purchase->inland_delivery_fee_raw;
            # Tong tien ship noi dia cua shop

            $usedInlandDeliveryFeeRaw = $listExportedBol->sum('inland_delivery_fee_raw'); # Lượng đã phân phối
            $remain  = $purchaseInlandDeliveryFeeRaw - $usedInlandDeliveryFeeRaw;
            if ($remainBol) {
                $item->inland_delivery_fee_raw = $remain / $remainBol;
                $item->inland_delivery_fee = round($item->inland_delivery_fee_raw * $item->purchase->order->rate);
            }
        }

        # Calculating total fee
        $item->total_raw = $item->amount_raw + $item->order_fee + $item->inland_delivery_fee_raw; # Tien hang
        $item->total = round(
            $item->total_raw * $item->rate + $item->delivery_fee + $item->insurance_fee + $item->sub_fee
        );

        $item->save();
        if($isAdd){
            # If this function after adding new bill of landing
            # Let check china bill of landing to get info from them
            $cnBillOfLanding = CnBillOfLanding::where('code', $item->code)->first();
            if($cnBillOfLanding){
                $cnBillOfLanding->order_type = $item->order?$item->order->type:'deposit';
                $cnBillOfLanding->match = true;
                $cnBillOfLanding->user_id = $item->user_id;
                $cnBillOfLanding->order_id = $item->order_id;
                $cnBillOfLanding->purchase_id = $item->purchase_id;
                $cnBillOfLanding->save();

                $item->sub_fee = $cnBillOfLanding->sub_fee;
                $item->init_mass = $cnBillOfLanding->mass?:0;
                $item->mass = $cnBillOfLanding->mass;
                $item->packages = $cnBillOfLanding->packages;
                $item->save();
            }
        }
        return $item;
    }

    public static function addItem($input, $executor){
        try{
            if(
                array_key_exists('success', $input) && 
                array_key_exists('status_code', $input) && $input['status_code'] !== 200
            ){
                return $input;
            }
            /*
            if($executor->role->role_type_uid !== 'user'){
                if(!$input['input_mass'] && !$input['length'] && !$input['width'] && !$input['height']){
                    return ResTools::err(
                        'Bạn cần nhập khối lượng hoặc kích thước của vận đơn.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
            }
            */
            /*
            $purchaseId = null;
            if(array_key_exists('purchase_id', $input)){
                $purchaseId = $input['purchase_id'];
            }
            */
            if(array_key_exists('code', $input)){
                $input['code'] = strtoupper(trim($input['code']));
            }
            if(array_key_exists('from_client', $input) && $input['from_client']){
                if(self::where('code', $input['code'])->first()){
                    return ResTools::err(
                        'Mã vận đơn đã được sử dụng, bạn vui lòng chọn mã vận đơn khác.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                // if(!$purchaseId){
                unset($input['from_client']);
                if($executor->role->role_type_uid === 'admin'){
                    $addressCode = null;
                    if(array_key_exists('address_code', $input)){
                        $addressCode = $input['address_code'];
                        unset($input['address_code']);
                    }

                    $purchaseCode = null;
                    if(array_key_exists('purchase_code', $input)){
                        $purchaseCode = $input['purchase_code'];
                        unset($input['purchase_code']);
                    }

                    $landingStatus = 'Mới';
                    if(array_key_exists('landing_status', $input)){
                        $landingStatus = $input['landing_status'];
                        unset($input['landing_status']);
                    }
                    $order = null;
                    if(array_key_exists('order_id', $input) && $input['order_id']){
                        $order = Order::find(intVal($input['order_id']));
                        if(!$order){
                            return ResTools::err(
                                'Đơn hàng không tồn tại',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }
                        if($executor->role->uid !== config('app.sadmin') && $order->status === 'done'){
                        # if($item->order->status === 'done'){
                            return ResTools::err(
                                'Đơn hàng đã chốt, bạn không thể thêm vận đơn.',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }
                    }
                    if($order){
                        $input['user_id'] = $order->user_id;
                        $input['address_id'] = $order->address_id;
                        $input['order_id'] = $order->id;
                        $input['purchase_id'] = $input['purchase_id'];
                    }else{
                        if(!$addressCode && !$purchaseCode){
                            return ResTools::err(
                                'Bạn cần nhập mã địa chỉ hoặc mã giao dịch',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }

                        if($addressCode){
                            $address = Address::where('uid', $addressCode)->first();
                            if(!$address){
                                return ResTools::err(
                                    'Mã địa chỉ không tồn tại',
                                    ResTools::$ERROR_CODES['BAD_REQUEST']
                                );
                            }
                            $input['user_id'] = $address->user_id;
                            $input['address_id'] = $address->id;
                        }

                        if($purchaseCode){
                            $purchase = Purchase::where('code', $purchaseCode)->first();
                            if(!$purchase){
                                return ResTools::err(
                                    'Mã giao dịch không tồn tại',
                                    ResTools::$ERROR_CODES['BAD_REQUEST']
                                );
                            }
                            $input['user_id'] = $purchase->user_id;
                            $input['address_id'] = $purchase->order->address_id;
                            $input['order_id'] = $purchase->order->id;
                            $input['purchase_id'] = $purchase->id;
                        }
                    }

                    switch($landingStatus){
                        case 'Mới':
                            // Do nothing
                        break;
                        case 'Về TQ':
                            $input['cn_store_date'] = Tools::nowDateTime();
                        break;
                        case 'Về VN':
                            $input['cn_store_date'] = Tools::nowDateTime();
                            $input['vn_store_date'] = Tools::nowDateTime();
                        break;
                        case 'Đã xuất':
                            // $input['export_store_date'] = Tools::nowDateTime();
                            // Do nothing
                        break;
                        default:
                            return ResTools::err(
                                'Trạng thái không hợp lệ',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                    }
                }else{
                    $addressId = ValidateTools::toInt($input['address_id']);
                    $address = Address::find($addressId);
                    if(!$address){
                        return ResTools::err(
                            'Địa chỉ không tồn tại',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                    $input['user_id'] = $address->user_id;
                    $input['address_id'] = $address->id;
                }
                // }
            }

            if(!array_key_exists('transform_factor', $input) || !$input['transform_factor']){
                $input['transform_factor'] = config('app.default_transform_factor');
            }
            $order = null;
            if(array_key_exists('order_id', $input) && $input['order_id']){
                $order = Order::find($input['order_id']);
            }
            if($order){
                if(!$order->user_id || !$order->user){
                    return ResTools::err(
                        'Khách hàng sở hữu đơn hàng tương ứng không tồn tại.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                $input['order_type'] = $order->type;
                $input['user_id'] = $order->user->id;
                $input['rate'] = $order->rate;
                $input['address_id'] = $order->address->id;
                $input['delivery_fee_unit'] = (
                    $order->user->delivery_fee_unit
                    ?:
                    $order->address->areaCode->delivery_fee_unit
                );
            }else{
                if(array_key_exists('address_id', $input)){
                    $address = Address::find($input['address_id']);
                    if($address){
                        $input['delivery_fee_unit'] = (
                            $address->user->delivery_fee_unit
                            ?:
                            $address->areaCode->delivery_fee_unit
                        );
                        $input['rate'] = $address->user->rate;
                    }
                    if($executor->role->role_type_uid === 'user'){
                        $input['rate'] = $executor->rate;
                    }
                    $input['order_type'] = 'deposit';
                }else{
                    $input['rate'] = intVal(\ConfigDb::get('cny-vnd'));
                }
            }

            $vnBillOfLanding = VnBillOfLanding::where('code', $input['code'])->first();
            if($vnBillOfLanding){
                $input['vn_store_date'] = Tools::nowDateTime();
            }

            if(array_key_exists('landing_status', $input)){
                unset($input['landing_status']);
            }
            if(array_key_exists('address_code', $input)){
                unset($input['address_code']);
            }
            if(array_key_exists('purchase_code', $input)){
                unset($input['purchase_code']);
            }
            $collectBol = CollectBol::where('bill_of_landing_code', $input['code'])->first();
            if($collectBol){
                $collectBol->match = true;
                $collectBol->save();
                if(!$input['purchase_id']){
                    $purchase = Purchase::where('code', 'purchase_code')->first();
                    if($purchase){
                        $input['purchase_id'] = $purchase->id;
                        $purchase->real_amount = $collectBol->real_amount;
                        $purchase->save();
                    }
                }
            }
            $item = self::create($input);
            $item = self::recalculate($item, true);
            # print_r($item);die;
            if($item->user_id){
                UserOrderLog::addBillOfLanding($item, $executor);
            }
            $order = null;
            if($item->order_id && $item->order){
                $order = Order::obj(Order::recalculate($item->order_id)->id);
                if($item->order->status === 'purchasing'){
                    if(Purchase::allPurchaseHaveAtLeastOneBol($item->order)){
                        $item->order->status = 'purchased';
                        $item->order->save();
                    }
                }
            }
            $extra = [
                'order' => $order?:[]
            ];
            return ResTools::obj($item, trans('messages.add_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editItem($id, $input, $executor, $isCheck=false){
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

            if ($item->order) {
                if($executor->role->uid !== config('app.sadmin') && $item->order->status === 'done'){
                # if($item->order->status === 'done'){
                    return ResTools::err(
                        'Đơn hàng đã chốt, bạn không thể sửa vận đơn.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }

                if($item->export_store_date){
                    return ResTools::err(
                        'Bill đã xuất, bạn không thể sửa.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
            }

            if($executor->role->role_type_uid === 'user' && $item->cn_store_date){
                # Vận đơn đã tồn tại và đã về kho CN -> Không cho sửa gì cả
                return ResTools::err(
                    trans('messages.stop_insurance_register'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if(array_key_exists('code', $input)){
                $input['code'] = strtoupper(trim($input['code']));
            }

            $oldItem = clone $item;

            $insuranceRegisterChange = (
                array_key_exists('insurance_register', $input) && 
                $input['insurance_register'] !== $item->insurance_register
            );
            $insuranceValueChange = (
                array_key_exists('insurance_value', $input) && 
                abs(ValidateTools::toFloat($input['insurance_value']) - $item->insurance_value) > 0.01
            );
            if($insuranceRegisterChange || $insuranceValueChange){
                if($item->cn_store_date){
                    $cnStoreDate = Carbon::createFromFormat('Y-m-d H:i:s', $item->cn_store_date, 'Asia/Saigon');
                    if($cnStoreDate <= Tools::nowDateTime()){
                        return ResTools::err(
                            trans('messages.stop_insurance_register'),
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                }
            }
            /*
            if(!$input['input_mass'] && !$input['length'] && !$input['width'] && !$input['height']){
                return ResTools::err(
                    'Bạn cần nhập khối lượng hoặc kích thước của vận đơn.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            */
            /*
            $purchaseId = null;
            if(array_key_exists('purchase_id', $input)){
                $purchaseId = $input['purchase_id'];
            }
            */
            if(array_key_exists('from_client', $input) && $input['from_client']){
                $duplicateItem = self::where('code', $input['code'])->first();
                if($duplicateItem && $duplicateItem->code !== $item->code){
                    return ResTools::err(
                        'Mã vận đơn đã được sử dụng, bạn vui lòng chọn mã vận đơn khác.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                // if(!$purchaseId){
                unset($input['from_client']);
                if($executor->role->role_type_uid === 'admin'){
                    $addressCode = strtoupper($input['address_code']);
                    unset($input['address_code']);

                    $purchaseCode = $input['purchase_code'];
                    unset($input['purchase_code']);

                    $landingStatus = $input['landing_status'];
                    unset($input['landing_status']);

                    if(!$addressCode && !$purchaseCode){
                        return ResTools::err(
                            'Bạn cần nhập mã địa chỉ hoặc mã giao dịch',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }

                    if($addressCode){
                        $address = Address::where('uid', $addressCode)->first();
                        if(!$address){
                            return ResTools::err(
                                'Mã địa chỉ không tồn tại',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }

                        $input['user_id'] = $address->user_id;
                        $input['address_id'] = $address->id;
                    }

                    if($purchaseCode){
                        $purchase = Purchase::where('code', $purchaseCode)->first();
                        if(!$purchase){
                            return ResTools::err(
                                'Mã giao dịch không tồn tại',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }
                        $input['user_id'] = $purchase->user_id;
                        $input['address_id'] = $purchase->order->address_id;
                        $input['order_id'] = $purchase->order->id;
                        $input['purchase_id'] = $purchase->id;
                    }
                    if(!$isCheck){
                        switch($landingStatus){
                            case 'Mới':
                                # Hàng chưa xuất mới được đổi thông tin.
                                if(!$item->export_store_date){
                                    $input['cn_store_date'] = null;
                                    $input['vn_store_date'] = null;
                                }
                            break;
                            case 'Về TQ':
                                # Hàng chưa xuất mới được đổi thông tin.
                                if(!$item->export_store_date){
                                    $input['cn_store_date'] = Tools::nowDateTime();
                                    $input['vn_store_date'] = null;
                                }
                            break;
                            case 'Về VN':
                                # Hàng chưa xuất mới được đổi thông tin.
                                if(!$item->export_store_date){
                                    // Về VN thì phải về TQ trước
                                    $input['vn_store_date'] = Tools::nowDateTime();
                                    if(!$item->cn_store_date){
                                        $input['cn_store_date'] = Tools::nowDateTime();
                                    }
                                }
                            break;
                            case 'Đã xuất':
                                // $input['export_store_date'] = Tools::nowDateTime();
                                // Do nothing
                            break;
                            default:
                                return ResTools::err(
                                    'Trạng thái không hợp lệ',
                                    ResTools::$ERROR_CODES['BAD_REQUEST']
                                );
                        }
                    }
                }else{
                    $addressId = ValidateTools::toInt($input['address_id']);
                    $address = Address::find($addressId);
                    if(!$address){
                        return ResTools::err(
                            'Địa chỉ không tồn tại',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                    $input['user_id'] = $address->user_id;
                    $input['address_id'] = $address->id;
                }
                // }
            }

            if(array_key_exists('landing_status', $input)){
                unset($input['landing_status']);
            }
            if(array_key_exists('address_code', $input)){
                unset($input['address_code']);
            }
            if(array_key_exists('purchase_code', $input)){
                unset($input['purchase_code']);
            }

            if(array_key_exists('code', $input)){
                $collectBol = CollectBol::where('bill_of_landing_code', $input['code'])->first();
                if($collectBol){
                    $collectBol->match = true;
                    $collectBol->save();
                    if(!$input['purchase_id']){
                        $purchase = Purchase::where('code', 'purchase_code')->first();
                        if($purchase){
                            $input['purchase_id'] = $purchase->id;
                            $purchase->real_amount = $collectBol->real_amount;
                            $purchase->save();
                        }
                    }
                }
            }

            $oldCode = $item->code;
            $item->update($input);
            $item = self::recalculate($item);

            # If change code -> lookup cn and vn bol to change match status and change new code date status.
            if($item->code !== $oldCode){
                $cnBillOfLanding = CnBillOfLanding::where('code', $oldCode)->first();
                if($cnBillOfLanding){
                    $cnBillOfLanding->match = false;
                    $cnBillOfLanding->save();

                    $item->cn_store_date = null;
                    $item->save();
                }
                $vnBillOfLanding = VnBillOfLanding::where('code', $oldCode)->first();
                if($vnBillOfLanding){
                    $vnBillOfLanding->match = false;
                    $vnBillOfLanding->save();

                    $item->vn_store_date = null;
                    $item->save();
                }
            }

            # Update new match cn bol
            $cnBillOfLanding = CnBillOfLanding::where('code', $item->code)->first();
            if($cnBillOfLanding){
                $item->cn_store_date = Tools::nowDateTime();
                $item->save();

                CnBillOfLanding::editItem($cnBillOfLanding->id, [
                    'match' => true,
                    'code' => $item->code,
                    'mass' => $item->mass,
                    'input_mass' => $item->input_mass,
                    'length' => $item->length,
                    'width' => $item->width,
                    'height' => $item->height,
                    'packages' => $item->packages,
                    'sub_fee' => $item->sub_fee
                ]);
            }

            # Update new match vn bol
            $vnBillOfLanding = VnBillOfLanding::where('code', $item->code)->first();
            if($vnBillOfLanding){
                $item->vn_store_date = Tools::nowDateTime();
                $item->save();

                $vnBillOfLanding->match = true;
                $vnBillOfLanding->save();
            }

            if($oldItem->user_id){
                UserOrderLog::editBillOfLanding($item, $oldItem, $executor);
            }
            $extra = [
                'order' => []
            ];
            if($item->order_id){
                $order = Order::obj(Order::recalculate($item->order_id)->id);
                $extra['order'] = $order;
            }
            return ResTools::obj($item, trans('messages.edit_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function resetComplain($id, $executor){
        try{
            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if($executor->role->role_type_uid === 'admin'){
                return ResTools::err(
                    'Chỉ người dùng mới được quyền tạo mới khiếu nại.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if(!$item->complain_resolve){
                $errorMessage = impode('' , [
                    'Khiếu nại chưa được giải quyết xong. Bạn vui lòng giải quyết',
                    'khiếu nại trước khi tạo khiếu nại mới.'
                ]);
                return ResTools::err(
                    $errorMessage,
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $input = [
                'complain_amount' => 0,
                'complain_type' => null,
                'complain_resolve' => true,
                'complain_note_user' => null,
                'complain_note_admin' => null,
                'complain_change_date' => null,
                'complain_date' => null,
                'complain_turn' => null
            ];
            $result  = $item->update($input);
            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editComplain($id, $input, $executor){
        try{
            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if($executor->role->role_type_uid === 'user'){
                if($item->complain_change_date && $item->complain_resolve){
                    return ResTools::err(
                        'Khiếu nại đã được giải quyết. Bạn vui lòng tạo mới trước khi cập nhật.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                if($item->complain_turn && $item->complain_turn !== 'user'){
                    return ResTools::err(
                        'Bạn cần chờ bên kia trả lời trước khi cập nhật.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                if(!$item->complain_change_date){
                    // Người dùng vừa tạo yêu cầu khiếu nại thì không thể resolve
                    # Đánh dấu thời gian khiếu nại
                    $input['complain_date'] = Tools::nowDateTime();
                    $input['complain_resolve'] = false;
                }

                # Không cập nhật note của admin khi user cập nhật
                if(array_key_exists('complain_note_admin', $input)){
                    unset($input['complain_note_admin']);
                }
                $input['complain_turn'] = 'admin';
            }else{
                if(!$item->complain_change_date){
                    return ResTools::err(
                        'Chỉ khách hàng được chủ động tạo khiếu nại.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                if($item->complain_resolve){
                    return ResTools::err(
                        'Khiếu nại đã được giải quyết. Bạn không thể cập nhật thêm.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                if($item->complain_turn !== 'admin'){
                    return ResTools::err(
                        'Bạn cần chờ bên kia trả lời trước khi cập nhật.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                # Admin không được sửa mục đích khiếu nại
                if(array_key_exists('complain_type', $input)){
                    unset($input['complain_type']);
                }
                # Không cập nhật note của user khi admin cập nhật
                if(array_key_exists('complain_note_user', $input)){
                    unset($input['complain_note_user']);
                }
                $input['complain_turn'] = 'user';
            }
            $input['complain_change_date'] = Tools::nowDateTime();
            $result  = $item->update($input);
            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeItem($id, $executor, $force=false){
        try{
            $result = null;
            $orderId = null;
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->get();
            if($listItem->count()){
                foreach ($listItem as $item) {
                    $listCode[] = $item->code;
                    $order = null;
                    if($item->order_id && $item->order){
                        $order = $item->order;

                        if($executor->role->uid !== config('app.sadmin') && $item->order->status === 'done'){
                        # if($item->order->status === 'done'){
                            return ResTools::err(
                                'Đơn hàng đã chốt, bạn không thể xoá vận đơn.',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }

                        if($item->export_store_date){
                            return ResTools::err(
                                'Bill đã xuất, bạn không thể sửa.',
                                ResTools::$ERROR_CODES['BAD_REQUEST']
                            );
                        }
                    }

                    $oldItem = clone $item;
                    $orderId = $item->order_id;
                    if($force){
                        $item->forceDelete();
                    }else{
                        $item->delete();
                    }

                    if($order){
                        if(Purchase::allPurchaseHaveAtLeastOneBol($order)){
                            if($order->status === 'purchasing'){
                                $order->status = 'purchased';
                            }
                        }else{
                            if($order->status === 'purchased'){
                                $order->status = 'purchasing';
                            }
                        }
                        $order->save();
                    }

                    $cnBillOfLanding = CnBillOfLanding::where('code', $oldItem->code)->first();
                    if($cnBillOfLanding){
                        $cnBillOfLanding->match = false;
                        $cnBillOfLanding->save();
                    }

                    $vnBillOfLanding = VnBillOfLanding::where('code', $oldItem->code)->first();
                    if($vnBillOfLanding){
                        $vnBillOfLanding->match = false;
                        $vnBillOfLanding->save();
                    }
                    if($oldItem->user_id){
                        UserOrderLog::removeBillOfLanding($oldItem, $executor);
                    }
                }
                $result = ['id' => count($listId)>1?$listId:$listId[0]];
            }else{
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if($orderId){
                $order = Order::obj(Order::recalculate($orderId)->id);
                $extra = [
                    'order' => $order
                ];
            }else{
                $extra = [
                    'order' => ['data' => []]
                ];
            }
            return ResTools::obj($result, trans('messages.remove_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public function delete(){
        # Xoá các item đã kiểm
        $this->checkItems()->delete();
        $purchase = $this->purchase;
        $result = parent::delete();

        # Check fulfill
        if ($purchase) {
            $userId = $purchase->user_id;
            $unExportedBols = $purchase->billsOfLanding()->whereNull('export_bill_id')->count();
            if (!$unExportedBols) {
                # All exported -> liability here
                $lastTransaction = UserTransaction::
                    where('purchase_id', $purchase->id)->
                    where('type', 'TH')->
                    orderBy('id', 'desc')->first();
                if ($lastTransaction) {
                    $transactionData = [
                        'user_id' => $userId,
                        'type' => 'TH',
                        'amount' => floor(abs($purchase->total - $purchase->delivery_fee)),
                        'money_type' => '-',
                        'note' => 'Tiền hàng mã GD: '.$purchase->code,
                        'export_bill_id' => $lastTransaction->export_bill_id,
                        'purchase_id' => $purchase->id
                    ];
                    UserTransaction::addItem($transactionData, $executor);
                }
            }
        }

        return $result;
    }

    public function save(array $options = array()){
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        if(in_array('updated_at', $colums)){
            $this->updated_at = Tools::nowDateTime();
        }
        if($this->amount){
            $this->amount = round($this->amount);
        }
        if($this->code){
            $this->code = strtoupper($this->code);
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
