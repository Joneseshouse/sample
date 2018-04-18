<?php

namespace App\Modules\UserTransaction\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\User\Models\User;
use App\Modules\Admin\Models\Admin;
use App\Modules\Receipt\Models\Receipt;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\BillOfLanding\Models\BillOfLanding;


class UserTransaction extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_transactions';
    public $timestamps = false;
    protected $appends = [
        'admin_fullname',
        'user_fullname'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'user_id',
        'order_id',
        'receipt_id',
        'export_bill_id',
        'purchase_id',
        'bol_id',
        'uid',
        'type',
        'money_type',
        'note',
        'image',

        'amount',
        'credit_balance',
        'liabilities',
        'balance',
        'purchasing',
        'missing',

        'ordinal',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public static $fieldDescriptions = [
        'user_id' => 'int,required',
        'type' => 'str,required|max:50',
        'money_type' => 'str,required|max:50',
        'amount' => 'int,required',
        'note' => 'str,required',
        'image' => 'str',
    ];

    public static $searchFields = ['uid', 'note'];

    public function admin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
    }

    public function user(){
        return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
    }

    public function order(){
        return $this->belongsTo('App\Modules\Order\Models\Order', 'order_id');
    }

    public function getAdminFullNameAttribute($value){
        if($this->admin_id && $this->admin){
            return $this->admin->last_name . ' ' . $this->admin->first_name;
        }
        return null;
    }

    public function getUserFullNameAttribute($value){
        if($this->user_id && $this->user){
            return $this->user->last_name . ' ' . $this->user->first_name;
        }
        return null;
    }

    private static function totalCalculate($query){
        $amount = intVal($query->sum('amount'));
        $credit_balance = intVal($query->sum('credit_balance'));
        $purchasing = intVal($query->sum('purchasing'));
        $liabilities = intVal($query->sum('liabilities'));

        return [
            'amount' => $amount,
            'credit_balance' => $credit_balance,
            # 'purchasing' => $purchasing,
            'liabilities' => $liabilities,
        ];
    }

    /*
    public static function syncLatest($listUser=[]){
        if(!count($listUser)){
            $listUser = User::all();
        }else{
            $listUser = User::whereIn('id', $listUser)->get();
        }
        foreach ($listUser as $user) {
            $listItem = UserTransaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            foreach ($listItem as $index => $item) {
                if(!$index){
                    $item->latest = true;
                }else{
                    $item->latest = false;
                }
                $item->save();
            }
        }
    }
    */

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            $listItem = self::where([]);

            if(array_key_exists('user_id', $params) && $params['user_id']){
                $listItem->where('user_id', $params['user_id']);
            }

            $adminId = $params['chamsoc']?$params['admin_id']:null;
            unset($params['chamsoc']);
            $balance = $listItem->sum('credit_balance') - $listItem->sum('liabilities');
            $purchasing = intVal(abs($listItem->sum('purchasing')));
            $missing = $balance - $purchasing;

            $balance = $balance > 0 ? $balance : 0;
            if ($missing > 0) {
                $missing = 0;
            } else {
                $missing = abs($missing);
            }
            /*
            $balance = 0;
            $missing = 0;

            $lastItem = $listItem->orderBy('id', 'DESC')->first();
            if ($lastItem) {
                $balance = $lastItem->balance;
                $missing = $lastItem->missing;
            }
            */


            # $missing = $missing > 0 ? $missing : 0;
            $total = [
                'balance' => abs($balance) >= 100 ? $balance : 0,
                'purchasing' => abs($purchasing) >= 100 ? $purchasing : 0,
                'missing' => abs($missing) >= 100 ? $missing : 0
            ];
            # print_r($missing);die;

            if(array_key_exists('admin_id', $params) && $params['admin_id']){
                $listItem->where('admin_id', $params['admin_id']);
            }

            if(array_key_exists('chamsoc_id', $params) && $params['chamsoc_id']){
                $listItem->whereHas('user', function($q) use($params){
                    $q->where('admin_id', $params['chamsoc_id']);
                });
                unset($params['chamsoc_id']);
            }

            if(array_key_exists('type', $params) && $params['type']){
                $listItem->where('type', $params['type']);
            }

            if(array_key_exists('money_type', $params) && $params['money_type']){
                $listItem->where('money_type', $params['money_type']);
            }

            if(array_key_exists('from_amount', $params) & array_key_exists('to_amount', $params)){
                $fromAmount = $params['from_amount'];
                $toAmount = $params['to_amount'];
                if($fromAmount && $toAmount){
                    $listItem->whereBetween('amount', [$fromAmount, $toAmount]);
                }
            }

            if(array_key_exists('date_range', $params) && $params['date_range']){
                $dateRange = explode(',', $params['date_range']);
                $fromDate = ValidateTools::toDate($dateRange[0]);
                $toDate = ValidateTools::toDate($dateRange[1]);
                # var_dump($fromDate, $toDate);die;
                $listItem->
                    whereDate('created_at', '>=', $fromDate)->
                    whereDate('created_at', '<=', $toDate);
            }

            if(array_key_exists('note', $params) && $params['note']){
                $keyword = $params['note'];
                if($keyword && strlen($keyword) >=3){
                    $listItem->where('note', 'ilike', '%' . $keyword . '%');
                }
            }

            $query = clone $listItem;
            if($keyword && strlen($keyword) >=3){
                $listItem = $listItem->where(function($query) use($keyword){
                    foreach(self::$searchFields as $key => $field){
                        $query->orWhere($field, 'ilike', '%' . $keyword . '%');
                    }
                });
            }
            $listItem = $listItem->
                orderBy('created_at', 'desc')->
                paginate(config('app.page_size'));

            # Get list user, only nhanvienchamsot can see his customer
            $listUser = User::select('id', 'uid', 'first_name', 'last_name', 'email', 'phone');
            if($adminId){
                $listUser->where('admin_id', $adminId);
            }
            $listUser = $listUser->orderBy('email', 'asc')->get();

            $extra = [
                'list_admin' => Admin::
                    select('id', 'first_name', 'last_name')->
                    orderBy('last_name', 'asc')->get(),
                'list_user' => $listUser,
                'list_type' => config('app.list_user_transaction_type'),
                'total' => array_merge(self::totalCalculate($query), $total)
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

    public static function recalculate($item){
        switch($item->type){
            case 'NT':
            case 'CK':
            case 'KN':
                $item->credit_balance = abs($item->amount);
                $item->liabilities = 0;
                $item->purchasing = 0;
                break;
            case 'RT':
                $item->credit_balance = 0;
                $item->liabilities = abs($item->amount);
                $item->purchasing = 0;
                break;
            case 'GD':
                $item->credit_balance = 0;
                $item->liabilities = 0;
                $item->purchasing = abs($item->amount);
                break;
            case 'VC':
                $item->credit_balance = 0;
                $item->liabilities = 0;
                $item->purchasing = abs($item->amount);
                break;
            case 'TH':
            case 'XH':
                $item->credit_balance = 0;
                $item->liabilities = abs($item->amount);
                $item->purchasing = -abs($item->amount);
                break;
        }
        $item->save();

        $query = self::
            where('user_id', $item->user_id)->
            where('id', '<=', $item->id);

        $item->balance = $query->sum('credit_balance') - $query->sum('liabilities');

        $item->missing = ($item->balance >= 0 ? $item->balance : 0) - $query->sum('purchasing');

        if($item->missing >= 0){
            $item->missing = 0;
        }else{
            $item->missing = abs($item->missing);
        }

        $item->uid = $item->user_id . $item->type . $item->ordinal;
        $item->save();

        return $item;
    }

    /*
    public static function recalculate($item){
        switch($item->type){
            case 'NT':
            case 'CK':
            case 'KN':
                $item->credit_balance = abs($item->amount);
            break;
            case 'RT':
                $item->liabilities = abs($item->amount);
            break;
            case 'TH':
                $item->liabilities = abs($item->amount);
                # $item->purchasing = -abs($item->amount);
                $item->purchasing = -abs(ExportBill::goodsAmount($item->export_bill_id));
            break;
            case 'GD':
                if (in_array($item->status, ['confirm', 'purchasing', 'purchased', 'done'])) {
                    $item->purchasing = intVal($item->order->total);
                }
            break;
        }
        $item->save();

        $query = self::
            where('user_id', $item->user_id)->
            where('id', '<=', $item->id);

        $item->balance = $query->sum('credit_balance') - $query->sum('liabilities');
        $item->missing = $item->balance - $query->sum('purchasing');
        if($item->missing >= 0){
            $item->missing = 0;
        }else{
            $item->missing = abs($item->missing);
        }

        $item->uid = $item->user_id . $item->type . $item->ordinal;
        $item->save();

        return $item;
    }
    */

    public static function addItem($input, $executor){
        try{
            if(
                array_key_exists('success', $input) && 
                array_key_exists('status_code', $input) && 
                $input['status_code'] !== 200
            ){
                return $input;
            }

            if(!$input['type']){
                return ResTools::err(
                    ['type' => 'Bạn vui lòng chọn 1 hành động.'],
                    ResTools::$ERROR_CODES['NOT_FOUND']
                );
            }

            if(!$input['money_type']){
                return ResTools::err(
                    ['type' => 'Bạn vui lòng chọn 1 loại tiền.'],
                    ResTools::$ERROR_CODES['NOT_FOUND']
                );
            }

            if(!$input['user_id']){
                return ResTools::err(
                    ['user_id' => 'Bạn vui lòng chọn 1 khách hàng.'],
                    ResTools::$ERROR_CODES['NOT_FOUND']
                );
            }

            $input['uid'] = str_random(32);

            $input['amount'] = round($input['amount']);

            $input['admin_id'] = $executor?$executor->id:0;
            $ordinal = 1;
            $lastItem = self::where('type', $input['type'])->orderBy('ordinal', 'DESC')->first();
            if($lastItem){
                $ordinal = $lastItem->ordinal + 1;
            }

            $input['ordinal'] = $ordinal;

            $item = self::recalculate(self::create($input));
            # self::syncLatest([$item->user_id]);

            if(in_array($item->type, ['NT', 'CK', 'KN'])){
                $receipt = Receipt::addItem([
                    'user_id' => $item->user_id,
                    'amount' => $item->amount,
                    'note' => $item->note
                ], $executor);
                $item->receipt_id = $receipt['data']['id'];
                $item->save();
            }

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
            $newInput = [
                'note' => $input['note'],
                'amount' => $input['amount']
            ];

            $item->update($newInput);
            $item = self::recalculate($item);

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
            $listUser = [];
            if($listItem->count()){
                foreach ($listItem as $item) {
                    $listUser[] = $item->user_id;
                    $item->delete();
                }
                # self::syncLatest($listUser);
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

    public function save(array $options = array()){
        $item = $this;
        \DB::transaction(function() use($item){
            # Apply transaction here
            # Auto increase order (if order exist)
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
                /*
                if(in_array('order', $colums)){
                    if($item->order === 0){
                        $largestOrderItem = self::orderBy('order', 'desc')->first();
                        if($largestOrderItem){
                            $item->order = $largestOrderItem->order + 1;
                        }else{
                            $item->order = 1;
                        }
                    }
                }
                */
            }
            // before save code
            parent::save();
            // after save code
        }, 20);
    }
}
