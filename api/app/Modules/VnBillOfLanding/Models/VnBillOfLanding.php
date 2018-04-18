<?php

namespace App\Modules\VnBillOfLanding\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;
use App\Modules\Order\Models\Order;

class VnBillOfLanding extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vn_bills_of_landing';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'code',
        'note',
        'match',
        'order_type',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public static $fieldDescriptions = [
        'admin_id' => 'int',
        'code' => 'str,required|max:100',
        'note' => 'str'
    ];

    public static $searchFields = ['code'];

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            $startDate = null;
            $endDate = null;

            if(array_key_exists('start_date', $params)){
                $startDate = ValidateTools::toDate($params['start_date']);
                unset($params['start_date']);
            }
            if(array_key_exists('end_date', $params)){
                $endDate = ValidateTools::toDate($params['end_date']);
                unset($params['end_date']);
            }

            $listItem = VnBillOfLanding::where($params);

            if($startDate && $endDate){
                $listItem->
                    whereDate('created_at', '>=', $startDate)->
                    whereDate('created_at', '<=', $endDate);
            }

            if($keyword && strlen($keyword) >=3){
                $listItem = $listItem->where(function($query) use($keyword){
                    foreach(self::$searchFields as $key => $field){
                        $query->orWhere($field, 'ilike', '%' . $keyword . '%');
                    }
                });
            }
            $listItem = $listItem->
                orderBy('match', 'asc')->
                orderBy('id', 'desc')->
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

    public static function upload($adminId, $file){
        try{
            if(!count($file)){
                return ResTools::err(
                    'Bạn cần chọn 1 file excel để upload.',
                    ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
                );
            }
            $file = $file['list_code'];
            $file_name = $file['tmp_name'];
            $firstCount = self::count();
            $totalItem = 0;
            $orderNeedToRecalculate = [];
            $error = null;
            \Excel::load($file_name, function($reader) use($adminId, $totalItem, &$orderNeedToRecalculate, &$error) {
                try{
                    // mabill, ghichu
                    $listItem = $reader->toArray();
                    $totalItem = count($listItem);
                    $createdItem = 0;
                    foreach ($listItem as $item) {
                        $item['mabill'] = trim(strtoupper(str_replace(" ", "", $item['mabill'])));
                        $input = [
                            'admin_id' => $adminId,
                            'code' => $item['mabill'],
                            'note' => $item['ghichu']
                        ];
                        if(!self::where('code', $input['code'])->first()){
                            $billOfLanding = BillOfLanding::where('code', $input['code'])->first();
                            $cnBillOfLanding = CnBillOfLanding::where('code', $input['code'])->first();
                            if($cnBillOfLanding && $billOfLanding){
                                $cnBillOfLanding->match = true;
                                $cnBillOfLanding->save();
                            }
                            if($billOfLanding){
                                $input['order_type'] = $billOfLanding->order_type;
                                $input['match'] = true;

                                if($input['note']){
                                    $billOfLanding->note .= ', '.$input['note'];
                                }
                                $billOfLanding->vn_store_date = Tools::nowDateTime();
                                $billOfLanding->save();
                            }
                            self::addItem($input);
                        }
                    }
                }
                catch(\Exception $e){$error = $e;}
                catch(\Error $e){$error = $e;}
            });
            if($error){
                return ResTools::err(
                    'Lỗi trong quá trình đọc excel. Bạn hãy kiểm tra lại cấu trúc của file excel vừa upload. Lưu ý: File excel chỉ được chứa 1 sheet duy nhất.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $lastCount = self::count();
            $createdItem = $lastCount - $firstCount;
            $extraMessage = " $createdItem code tạo thành công.";
            $result = [];
            return ResTools::obj($result, trans('messages.add_success').$extraMessage);
        }catch(\Error $e){
            return ResTools::err(
                Tools::errMessage($e),
                ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
            );
        }
    }

    public static function addItem($input){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }
            $input['code'] = trim(strtoupper(str_replace(" ", "", $input['code'])));
            $checkDuplicate = self::where(["code" => $input["code"]])->first();
            if($checkDuplicate){
                return ResTools::err(
                    trans('messages.duplicate_item'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $billOfLanding = BillOfLanding::where('code', $input['code'])->first();
            $cnBillOfLanding = CnBillOfLanding::where('code', $input['code'])->first();
            if($cnBillOfLanding && $billOfLanding){
                $cnBillOfLanding->match = true;
                $cnBillOfLanding->save();
            }
            if($billOfLanding){
                $input['order_type'] = $billOfLanding->order_type;
                $input['match'] = true;

                if($input['note']){
                    $billOfLanding->note .= ', '.$input['note'];
                }
                $billOfLanding->vn_store_date = Tools::nowDateTime();
                $billOfLanding->save();
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
            $input['code'] = trim(strtoupper(str_replace(" ", "", $input['code'])));
            $checkDuplicate = self::where(["code" => $input["code"]])->first();
            if($checkDuplicate && $checkDuplicate->id !== $id){
                return ResTools::err(
                    trans('messages.duplicate_item'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $oldCode = $item->code;
            $item->update($input);

            if($oldCode !== $item->code){
                # Change bol code -> mark vn_store_date to null
                $billOfLanding = BillOfLanding::where('code', $oldCode)->first();
                $cnBillOfLanding = CnBillOfLanding::where('code', $input['code'])->first();
                $oldCnBillOfLanding = CnBillOfLanding::where('code', $oldCode)->first();
                if($cnBillOfLanding && $billOfLanding){
                    $cnBillOfLanding->match = true;
                    $cnBillOfLanding->save();
                }
                if($oldCnBillOfLanding){
                    $oldCnBillOfLanding->match = false;
                    $oldCnBillOfLanding->save();
                }
                if($billOfLanding){
                    $billOfLanding->vn_store_date = null;
                    $billOfLanding->save();

                    $item->match = false;
                    $item->save();
                }
            }

            # Match with new bol
            $billOfLanding = BillOfLanding::where('code', $item->code)->first();
            if($billOfLanding){
                $item->match = true;
                $item->save();

                $billOfLanding->vn_store_date = Tools::nowDateTime();
                $billOfLanding->save();
            }

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
                    $code = $item->code;
                    $item->delete();

                    $billOfLanding = BillOfLanding::where('code', $code)->first();
                    $cnBillOfLanding = CnBillOfLanding::where('code', $code)->first();
                    if($cnBillOfLanding){
                        $cnBillOfLanding->match = false;
                        $cnBillOfLanding->save();
                    }
                    if($billOfLanding){
                        $billOfLanding->vn_store_date = null;
                        $billOfLanding->save();
                    }
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

    public function save(array $options = array()){
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        if(in_array('updated_at', $colums)){
            $this->updated_at = Tools::nowDateTime();
        }
        if($this->code){
            $this->code = strtoupper($this->code);
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
