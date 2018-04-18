<?php

namespace App\Modules\CheckBill\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Order\Models\Order;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\CheckItem\Models\CheckItem;

class CheckBill extends Model{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'check_bills';
    protected $appends = [
        'bill_of_landing_code',
        'address_uid',
        'admin_full_name'
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
        'bill_of_landing_id',
        'admin_id',
        'note'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'billOfLanding',
        'admin'
    ];

    public static $fieldDescriptions = [
        'bill_of_landing_id' => 'str,required|max:70',
        'note' => 'str',
    ];

    public static $searchFields = ['code'];

    public function user(){
        return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
    }

    public function order(){
        return $this->belongsTo('App\Modules\Order\Models\Order', 'order_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Modules\Purchase\Models\Purchase', 'purchase_id');
    }

    public function billOfLanding(){
        return $this->belongsTo('App\Modules\BillOfLanding\Models\BillOfLanding', 'bill_of_landing_id');
    }

    public function admin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
    }

    public function getBillOfLandingCodeAttribute($value){
        if($this->billOfLanding){
            return $this->billOfLanding->code;
        }
        return null;
    }

    public function getAddressUidAttribute($value){
        if($this->billOfLanding){
            $billOfLanding = $this->billOfLanding;
            if($billOfLanding->address){
                return $billOfLanding->address->uid;
            }
            return null;
        }
        return null;
    }

    public function getAdminFullNameAttribute($value){
        return $this->admin->fullname;
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            $listItem = CheckBill::where($params);
            if($keyword && strlen($keyword) >=3){
                $listItem = $listItem->whereHas(['billOfLanding', function($query) use($keyword){
                    foreach(self::$searchFields as $key => $field){
                        $query->orWhere($field, 'ilike', '%' . $keyword . '%');
                    }
                }]);
            }
            $listItem = $listItem->
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            $extra = [
                'list_check_item_status' => config('app.list_check_item_status')
            ];
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
            return ResTools::obj($result, $original);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function checkFull($data, $executor){
        try{
            $result = [];
            $error = null;
            $data = json_decode($data, true);
            if(array_key_exists('bill_of_landing_code', $data)){
                $data['bill_of_landing_code'] = strtoupper($data['bill_of_landing_code']);
            }
            $purchaseId = $data['id']===null?null:ValidateTools::toInt($data['id']);
            $originalBillOfLandingCode = strtoupper($data['original_bill_of_landing_code']);
            if(!$purchaseId){
                # Deposit order
                $billOfLanding = BillOfLanding::where('code', $originalBillOfLandingCode)->first();
                if(!$billOfLanding){
                    return ResTools::err(
                        trans('messages.item_not_exist'),
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }

                // $billOfLanding->note .= ($billOfLanding->note?', ':'') . $data['note'];
                BillOfLanding::editItem($billOfLanding->id, [
                    'input_mass' => ValidateTools::toFloat($data['input_mass']),
                    'packages' => ValidateTools::toInt($data['packages']),
                    'length' => ValidateTools::toInt($data['length']),
                    'width' => ValidateTools::toInt($data['width']),
                    'height' => ValidateTools::toInt($data['height']),
                    'sub_fee' => ValidateTools::toInt($data['sub_fee']),
                    'checked_date' => Tools::nowDateTime(),
                    'check_staff_id' => $executor->id
                ], $executor, true);
                if($billOfLanding->order_id && $billOfLanding->order){
                    Order::recalculate($billOfLanding->order_id);
                }
                $extra = [
                    'list_purchase' => []
                ];
                # return ResTools::obj($result[0], trans('messages.add_success'), $extra);
                return ResTools::obj([], trans('messages.add_success'), $extra);
            }else{
                # Normal order
                # Kiểm tra xem có bị nhập lố số lượng?
                $error = null;
                $orderItems = $data['order_items'];
                $bol = $data['bill_of_landing'];
                $listCheckedData = [];
                foreach ($orderItems as $orderItem) {
                    $checkingQuantity = intval($orderItem['checking_quantity']);
                    $quantity = intval($orderItem['quantity']);
                    $checkingStatus = $orderItem['checking_status'];

                    # Lấy danh sách số lượng tồn (loại trừ item hiện tại)
                    $checkedQuantity = intval(
                        CheckItem::
                            where('bol_id', '!=', $bol['id'])->
                            where('order_item_id', $orderItem['id'])->
                            sum('quantity')
                    );

                    # Số lượng check lớn hơn số lượng tồn.
                    if($checkingQuantity + $checkedQuantity > $quantity){
                        $error = trans('messages.stock_quantity_larger_than_inventory_quantity');
                        $item = CheckItem::
                            where('bol_id', '!=', $bol['id'])->
                            where('order_item_id', '!=', $orderItem['id'])->
                            count();
                    }

                    $listCheckedData[] = [
                        'order_item_id' => intval($orderItem['id']),
                        'unit_price' => floatval($orderItem['unit_price']),
                        'bol_id' => intval($bol['id']),
                        'checked_quantity' => intval($checkedQuantity),
                        'checking_quantity' => intval($checkingQuantity),
                        'checking_status' => $checkingStatus
                    ];
                }

                if($error){
                    return ResTools::err(
                        $error,
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }

                $amountRaw = 0;
                foreach ($listCheckedData as $checkedData) {
                    $orderItem = OrderItem::find($checkedData['order_item_id']);
                    if($orderItem){
                        # Cập nhật Order Item
                        $orderItem->checked_quantity = $checkedData['checked_quantity'] + $checkedData['checking_quantity'];
                        $orderItem->save();

                        # Cập nhật Check item
                        $checkItem = CheckItem::
                            where([
                                'order_item_id' => $checkedData['order_item_id'], 
                                'bol_id' => $checkedData['bol_id']
                            ])->first();
                        if($checkItem){
                            $checkItem->quantity = $checkedData['checking_quantity'];
                            $checkItem->status = $checkedData['checking_status'];
                            $checkItem->save();
                            $amountRaw += ($checkedData['checking_quantity'] * $checkedData['unit_price']);
                        }
                    }
                }
                # print_r($amount_raw);die;

                $billOfLanding = BillOfLanding::
                    where('purchase_id', $purchaseId)->
                    where('code', $originalBillOfLandingCode)->
                    first();

                # $billOfLanding->note .= ($billOfLanding->note?', ':'') . $data['note'];

                if($billOfLanding){
                    BillOfLanding::editItem($billOfLanding->id, [
                        'input_mass' => ValidateTools::toFloat($data['input_mass']),
                        'packages' => ValidateTools::toInt($data['packages']),
                        'length' => ValidateTools::toInt($data['length']),
                        'width' => ValidateTools::toInt($data['width']),
                        'height' => ValidateTools::toInt($data['height']),
                        'sub_fee' => ValidateTools::toInt($data['sub_fee']),
                        'amount_raw' => $amountRaw,
                        'checked_date' => Tools::nowDateTime(),
                        'check_staff_id' => $executor->id
                    ], $executor, true);
                    if($billOfLanding->order_id && $billOfLanding->order){
                        Order::recalculate($billOfLanding->order_id);
                    }
                }
                /*
                $listBol = BillOfLanding::where('purchase_id', $purchaseId)->get();
                foreach ($listBol as $bol) {
                    BillOfLanding::recalculate($bol);
                }
                */
                $extra = [
                    'list_purchase' => null
                ];
                return ResTools::obj([], 'Kiểm hàng thành công', $extra);
            }
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function addItem($input){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }

            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }

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

            $item->update($input);
            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeItem($id){
        try{
            $result = null;
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->get();
            if($listItem->count()){
                foreach ($listItem as $item) {
                    $item->delete();
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
