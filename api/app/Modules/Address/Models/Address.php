<?php

namespace App\Modules\Address\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\AreaCode\Models\AreaCode;

class Address extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addresses';
    public $timestamps = false;
    protected $appends = [
        'delivery_fee_unit',
        'area_code_uid',
        'from_address',
        'from_phone',
        'area',
        'title'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'area_code_id',
        'uid',
        'address',
        'order',
        'default',
        'phone',
        'fullname'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'areaCode'
    ];

    public static $fieldDescriptions = [
        'area_code_id' => 'int,required',
        'address' => 'str,required|max:250',
        'phone' => 'str',
        'fullname' => 'str',
        'default' => 'bool',
    ];

    public static $searchFields = ['uid', 'address', 'phone', 'fullname'];

    public function user(){
        return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
    }

    public function areaCode(){
        return $this->belongsTo('App\Modules\AreaCode\Models\AreaCode', 'area_code_id');
    }

    public function getTitleAttribute($value){
        return $this->uid.': '.$this->address;
    }

    public function getDeliveryFeeUnitAttribute($value){
        if($this->area_code_id && $this->areaCode){
            return $this->areaCode->delivery_fee_unit;
        }
        return 0;
    }

    public function getAreaCodeUidAttribute($value){
        if($this->area_code_id && $this->areaCode){
            return $this->areaCode->code;
        }
        return 0;
    }

    public function getAreaAttribute($value){
        if($this->area_code_id && $this->areaCode){
            return $this->areaCode->title;
        }
        return null;
    }

    public function getFromAddressAttribute($value){
        return \ConfigDb::get('in-ten-dich-vu');
    }

    public function getFromPhoneAttribute($value){
        return \ConfigDb::get('in-so-dt');
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            $listItem = Address::where($params);
            if($keyword && strlen($keyword) >=1){
                if(is_numeric($keyword)){
                    $listItem->where('user_id', intval($keyword));
                }else{
                    $listItem = $listItem->where(function($query) use($keyword){
                        foreach(self::$searchFields as $key => $field){
                            $query->orWhere($field, 'ilike', '%' . $keyword . '%');
                        }
                    });
                }
            }
            $listItem = $listItem->
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            $extra = [
                'list_area_code' => AreaCode::
                    select('id', 'title', 'code', 'delivery_fee_unit')->
                    orderBy('id','asc')->
                    get()
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

    public static function addItem($input){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }
            if(self::where('user_id', $input['user_id'])->count() >= config('app.max_address')){
                return ResTools::err(
                    trans('messages.max_address'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $areaCode = AreaCode::find($input['area_code_id']);
            if(!$areaCode){
                return ResTools::err(
                    trans('messages.area_code_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $oldDefaultItem = self::where(['user_id' => $input['user_id'], 'default' => true])->first();
            $listAddress = self::where(['user_id' => $input['user_id']])->get();
            if(!$listAddress->count()){
                $input['default'] = true;
            }else{
                if(array_key_exists('default', $input) && $input['default']){
                    # Set default of all item to FALSE
                    foreach ($listAddress as $address) {
                        $address->default = false;
                        $address->save();
                    }
                }
            }

            // Auto generate uid here
            $listItem  = self::where(['user_id' => $input['user_id'], 'area_code_id' => $input['area_code_id']]);
            if($listItem->count()){
                $input['order'] = $listItem->orderBy('order', 'desc')->first()->order + 1;
            }else{
                $input['order'] = 0;
            }
            $input['uid'] = $input['user_id'].$areaCode->code.$input['order'];
            $item = self::create($input);

            $extra = [
                'oldDefaultId' => null
            ];
            if($oldDefaultItem && $oldDefaultItem->id !== $item->id && $item->default){
                $extra['oldDefaultId'] = $oldDefaultItem->id;
            }
            return ResTools::obj($item, trans('messages.add_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editItem($id, $input){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }
            $excludedFields = ['uid', 'order'];
            $input = Tools::ignoreKeys($input, $excludedFields);

            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $areaCode = AreaCode::find($input['area_code_id']);
            if(!$areaCode){
                return ResTools::err(
                    trans('messages.area_code_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $oldDefaultItem = self::where(['user_id' => $item->user_id, 'default' => true])->first();
            $listAddress = self::where(['user_id' => $item->user_id])->get();
            if($listAddress->count() === 1){
                $input['default'] = true;
            }else{
                if(array_key_exists('default', $input)){
                    if($item->default){
                        $input['default'] = true;
                    }else{
                        if($input['default']){
                            # Set default of all item to FALSE
                            foreach ($listAddress as $address) {
                                $address->default = false;
                                $address->save();
                            }
                        }
                    }
                }
            }

            // Update uid here
            $oldAreaCodeId = $item->area_code_id;
            # $oldUid = $item->uid;
            if($oldAreaCodeId !== $input['area_code_id']){
                $listItem  = self::where(['user_id' => $item->user_id, 'area_code_id' => $input['area_code_id']]);
                if($listItem->count()){
                    $input['order'] = $listItem->orderBy('order', 'desc')->first()->order + 1;
                }else{
                    $input['order'] = 0;
                }
                $input['uid'] = $item->user_id.$areaCode->code.$input['order'];
            }

            $item->update($input);
            $extra = [
                'oldDefaultId' => null
            ];
            if($oldDefaultItem && $oldDefaultItem->id !== $item->id && $item->default){
                $extra['oldDefaultId'] = $oldDefaultItem->id;
            }
            return ResTools::obj($item, trans('messages.edit_success'), $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeItem($id, $userId){
        try{
            $result = null;
            $listId = explode(',', $id);
            foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

            $listItem = self::whereIn('id', $listId)->where('user_id', $userId)->get();
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

            $extra = [
                'oldDefaultId' => null
            ];
            if(!Address::where(['user_id' => $userId, 'default' => true])->count()){
                $item = Address::first();
                if($item){
                    $item->default = true;
                    $item->save();
                    $extra['oldDefaultId'] = $item->id;
                }
            }

            return ResTools::obj($result, trans('messages.remove_success'), $extra);
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
