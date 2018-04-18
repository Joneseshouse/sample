<?php

namespace App\Modules\AdminTransaction\Models;

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


class AdminTransaction extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_transactions';
    public $timestamps = false;
    protected $appends = [
        'admin_fullname',
        'target_admin_fullname'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'target_admin_id',
        'uid',
        'type',
        'money_type',
        'note',
        'image',

        'amount',
        'credit_balance',
        'liabilities',
        'balance',

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
        'target_admin_id' => 'int',
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

    public function targetAdmin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'target_admin_id');
    }

    public function getAdminFullNameAttribute($value){
        if($this->admin_id && $this->admin){
            return $this->admin->first_name . ' ' . $this->admin->last_name;
        }
        return null;
    }

    public function getTargetAdminFullNameAttribute($value){
        if($this->target_admin_id && $this->targetAdmin){
            return $this->targetAdmin->first_name . ' ' . $this->targetAdmin->last_name;
        }
        return null;
    }

    private static function totalCalculate($query){
        $amount = intVal($query->sum('amount'));
        $credit_balance = intVal($query->sum('credit_balance'));
        $liabilities = intVal($query->sum('liabilities'));

        $query->where('latest', true);

        return [
            'amount' => $amount,
            'credit_balance' => $credit_balance,
            'liabilities' => $liabilities,
            'balance' => intVal($query->sum('balance')),
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
            $listItem = AdminTransaction::where('target_admin_id', $user->id)->orderBy('created_at', 'desc')->get();
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

            if(array_key_exists('target_admin_id', $params) && $params['target_admin_id']){
                $listItem->where('target_admin_id', $params['target_admin_id']);
            }
            if(array_key_exists('admin_id', $params) && $params['admin_id']){
                $listItem->where('admin_id', $params['admin_id']);
            }

            if(array_key_exists('type', $params) && $params['type']){
                $listItem->where('type', $params['type']);
            }
            /*
            if(array_key_exists('money_type', $params) && $params['money_type']){
                $listItem->where('money_type', $params['money_type']);
            }
            */
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
                $listItem->
                    whereDate('updated_at', '>=', $fromDate)->
                    whereDate('updated_at', '<=', $toDate);
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
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            $extra = [
                'list_admin' => Admin::select('id', 'first_name', 'last_name')->orderBy('last_name', 'asc')->get(),
                'list_user' => User::select('id', 'uid', 'first_name', 'last_name')->orderBy('last_name', 'asc')->get(),
                'list_type' => config('app.list_admin_transaction_type'),
                'total' => self::totalCalculate($query)
            ];
            return ResTools::lst($listItem, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function get($key, $default=''){
        try{
            $result = self::where('uid', $key)->first();
            if($result){
                return $result->value;
            }
            return $default;
        }
        catch(\Exception $e){return null;}
        catch(\Error $e){return null;}
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
        $lastBalance = 0;
        $lastTransaction = self::
            where('target_admin_id', $item->target_admin_id)->
            where('id', '!=', $item->id)->
            orderBy('id', 'DESC')->
            first();
        if($lastTransaction){
            $lastBalance = $lastTransaction->balance;
        }

        switch($item->type){
            case 'NT':
                $item->credit_balance = abs($item->amount);
            break;
            case 'RT':
                $item->liabilities = abs($item->amount);
            break;
        }

        $item->balance = $item->credit_balance - $item->liabilities + $lastBalance;

        $item->uid = $item->target_admin_id . $item->type . $item->ordinal;
        $item->save();

        return $item;
    }

    public static function addItem($input, $executor){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
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

            $input['uid'] = str_random(32);

            $input['amount'] = abs($input['amount']);

            $input['admin_id'] = $executor->id;
            $ordinal = 1;
            $lastItem = self::where('type', $input['type'])->orderBy('ordinal', 'DESC')->first();
            if($lastItem){
                $ordinal = $lastItem->ordinal + 1;
            }

            $input['ordinal'] = $ordinal;

            $item = self::recalculate(self::create($input));
            # self::syncLatest([$item->target_admin_id]);

            return ResTools::obj($item, trans('messages.add_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editItem($id, $input, $executor){
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
                    $listUser[] = $item->target_admin_id;
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
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        if(in_array('updated_at', $colums)){
            $this->updated_at = Tools::nowDateTime();
        }
        if(!$this->exists){
            if(in_array('created_at', $colums)){
                $this->created_at = Tools::nowDateTime();
            }
            if(self::count() > 0){
                $largestIdItem = self::orderBy('id', 'desc')->first();
                $this->id = $largestIdItem->id + 1;
            }else{
                $this->id = 1;
            }
            /*
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
            */
        }
        // before save code
        parent::save();
        // after save code
    }
}
