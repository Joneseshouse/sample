<?php

namespace App\Modules\OrderItem\Models;

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
use App\Modules\UserOrderLog\Models\UserOrderLog;
use App\Modules\CheckItem\Models\CheckItem;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\OrderItemNote\Models\OrderItemNote;


class OrderItem extends Model{
     use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'order_items';
    public $timestamps = false;
    protected $appends = [
        'note',
        'checked_status'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'shop_id',
        'purchase_id',
        'title',
        'avatar',
        'message',
        'properties',
        'quantity',
        'checked_quantity',
        'unit_price',
        'url',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'check_items'
    ];

    public static $fieldDescriptions = [
        'order_id' => 'int',
        'quantity' => 'float,required',
        'properties' => 'str|max:555',
        'unit_price' => 'str|max:250',
        'title' => 'str|max:555',
        'avatar' => 'str|max:250',
        'url' => 'str|max:250',
        'message' => 'str|max:250'
    ];

    public static $searchFields = ['title'];

    public function order(){
        return $this->belongsTo('App\Modules\Order\Models\Order', 'order_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Modules\Purchase\Models\Purchase', 'purchase_id');
    }

    public function shop(){
        return $this->belongsTo('App\Modules\Shop\Models\Shop', 'shop_id');
    }

    public function checkItems(){
        return $this->hasMany('App\Modules\CheckItem\Models\CheckItem', 'order_item_id');
    }

    public function orderItemNotes(){
        return $this->hasMany('App\Modules\OrderItemNote\Models\OrderItemNote', 'order_item_id');
    }

    public function getNoteAttribute($value){
        if($this->orderItemNotes()->count()){
            return $this->orderItemNotes()->orderBy('created_at', 'desc')->first()->note;
        }
        return null;
    }

    public function getCheckedStatusAttribute($value){
        $result = [];
        $listCheckItem = $this->checkItems;
        $listCheckItemStatusRef = config('app.list_check_item_status_ref');
        foreach ($listCheckItem as $checkItem) {
            $result[] = [
                'bol_code' => $checkItem->bol->code,
                'quantity' => $checkItem->quantity,
                'status' => (
                    array_key_exists(
                        $checkItem->status, $listCheckItemStatusRef)?
                        $listCheckItemStatusRef[$checkItem->status]:null
                    )
            ];
        }
        return $result;
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            if(!array_key_exists('order_id', $params)){
                $params['order_id'] = null;
            }
            $listItem = OrderItem::where($params);
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

    public static function addCart($listItem){
        foreach ($listItem as $item) {
            $item['order_id'] = 0;
            self::addItem($item);
        }
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
            if(array_key_exists('quantity', $input)){
                $input['checked_quantity'] = 0;
            }
            $item = self::create($input);
            if($item->message){
                OrderItemNote::addItem([
                    'order_item_id' => $item->id,
                    'user_id' => ($item->order_id && $item->order) ? $item->order->user_id : null,
                    'note' => $item->message
                ]);
            }
            UserOrderLog::addOrderItem($item);
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
            # if($item->order->status === 'done'){
                return ResTools::err(
                    'Đơn hàng đã chốt, bạn không thể sửa.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if($executor->role->role_type_uid === 'user' && !in_array($item->order->status, ['draft', 'new'])){
                $message = $input['message'];
                $input = [
                    'message' => $message
                ];
            }

            if(array_key_exists('unit_price', $input) && $item->purchase->billsOfLanding()->count()){
                $errorMessage = implode(' ', [
                    'Giao dịch của sản phẩm này đã có vận đơn,',
                    'bạn không thể sửa đơn giá sản phẩm.'
                ]);
                return ResTools::err(
                    $errorMessage,
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if(array_key_exists('quantity', $input) && $item->purchase_id && $item->purchase){
                # print_r($input['quantity']);die;
                $using = CheckItem::where('order_item_id', $item->id)->sum('quantity');
                if($input['quantity'] < $using){
                    $errorMessage = implode(' ', [
                        "Số lượng đã kiểm cho sản phẩm này là $using sản phẩm.",
                        "Số lượng mới không thể nhỏ hơn số này."
                    ]);
                    return ResTools::err(
                         $errorMessage,
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
            }

            $oldItem = clone $item;
            /*
            if(array_key_exists('quantity', $input)){
                $input['checked_quantity'] = $input['quantity'];
            }
            */
            $item->update($input);

            # self::recalculate($item);

            UserOrderLog::editOrderItem($item, $oldItem, $executor);
            $order = Order::obj(Order::recalculate($item->order_id)->id);
            $extra = [
                'order' => $order
            ];
            return ResTools::obj($item, trans('messages.edit_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeItem($id, $executor, $force=false){
        try{
            $result = null;
            $orderId = null;
            $listPurchaseId = [];
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->get();
            if($listItem->count()){
                foreach ($listItem as $item) {
                    if(
                        $executor->role->role_type_uid === 'user' && 
                        !in_array($item->order->status, ['draft', 'new'])
                    ){
                        return ResTools::err(
                            'Đơn hàng đã chốt, bạn không thể xoá mặt hàng.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                    if($executor->role->uid !== config('app.sadmin') && $item->order->status === 'done'){
                    # if($item->order->status === 'done'){
                        return ResTools::err(
                            'Đơn hàng đã chốt, bạn không thể xoá mặt hàng.',
                            ResTools::$ERROR_CODES['BAD_REQUEST']
                        );
                    }
                    $id = $item->id;
                    $oldItem = clone $item;
                    $orderId = $item->order_id;
                    # $purchaseId = $item->purchase_id;
                    array_push($listPurchaseId, $item->purchase_id);
                    if($force){
                        $item->forceDelete();
                    }else{
                        $item->delete();
                    }
                    UserOrderLog::removeOrderItem($oldItem, $executor);
                }
                $result = ['id' => count($listId)>1?$listId:$listId[0]];
            }else{
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if(count($listPurchaseId)){
                foreach($listPurchaseId as $purchaseId) {
                    $purchase = Purchase::find($purchaseId);
                    if($purchase){
                        if(!$purchase->orderItems->count()){
                            $order = $purchase->order;
                            Purchase::removeItem("{$purchase->id}", $executor, $force);
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
                        }
                    }
                }
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

    public static function empty($orderId, $executor){
        try{
            $order = Order::find($orderId);
            if(!$order){
                return ResTools::err(
                    'Đơn hàng này không tồn tại',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $listItem = self::where('order_id', $orderId)->get();
            foreach ($listItem as $item) {
                if(!$item->quantity){
                    self::removeItem((string)$item->id, $executor);
                }
            }
            return ResTools::obj([], 'Xoá mặt hàng rỗng thành công.');
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function recalculate($item){
        # Tính lại remain của item hiện tại 
        # (nếu sửa số lượng) -> không cho giảm xuống nhỏ hơn tổng các item đã kiểm
        # Tính lại amount của item hiện tại (nếu sửa đơn giá)
        # Tính lại amount của bol tương ứng.
        if($item->purchase_id && $item->purchase){
            $bols = $item->purchase->billsOfLanding;
            foreach ($bols as $bol) {

                $listCheckItem = CheckItem::where('bol_id', $bol->id)->get();
                $amountRaw = 0;
                foreach ($listCheckItem as $checkItem) {
                    $orderItem = $checkItem->orderItem;
                    $amountRaw += ($checkItem->quantity * $orderItem->unit_price);
                    # $orderItem->checked_quantity = $remain;
                    # $orderItem->save();
                }
                $bol->amount_raw = $amountRaw;
                $bol->save();

                $bol = BillOfLanding::recalculate($bol);
            }
        }
    }

    public function delete(){
        # Xoá các item đã kiểm
        $this->checkItems()->delete();

        self::recalculate($this);

        return parent::delete();
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
                    $largestOrderItemItem = self::orderBy('order', 'desc')->first();
                    if($largestOrderItemItem){
                        $this->order = $largestOrderItemItem->order + 1;
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
