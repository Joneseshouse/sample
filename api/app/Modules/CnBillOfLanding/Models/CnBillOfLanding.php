<?php

namespace App\Modules\CnBillOfLanding\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\CnBillOfLandingFail\Models\CnBillOfLandingFail;
use App\Modules\Order\Models\Order;
use App\Modules\Address\Models\Address;

class CnBillOfLanding extends Model{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cn_bills_of_landing';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'order_id',
        'purchase_id',
        'user_id',
        'code',
        'mass',
        'input_mass',
        'length',
        'width',
        'height',
        'packages',
        'sub_fee',
        'note',
        'order_type',
        'match'
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
        'input_mass' => 'float',
        'length' => 'int',
        'width' => 'int',
        'height' => 'int',
        'packages' => 'int,required',
        'sub_fee' => 'int',
        'note' => 'str',
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
            $listItem = CnBillOfLanding::where($params)->where("match", false);

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
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            return ResTools::lst($listItem);
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

    public static function calculateMass($input){
        if($input['length'] && $input['width'] && $input['height']){
            $length = ValidateTools::toInt($input['length']);
            $width = ValidateTools::toInt($input['width']);
            $height = ValidateTools::toInt($input['height']);
            $inputMass = ValidateTools::toFloat($input['input_mass']);
            $calculatedMass = floatval(($length * $width * $height)/config('app.default_transform_factor'));
            if($inputMass > $calculatedMass){
                return $inputMass;
            }
            return $calculatedMass;
        }
        return $input['input_mass']?:0;
    }

    public static function floatProcessing($input){
        $input = (string)$input;
        if(strlen($input) >= 10){
            return substr($input, 0, 10);
        }
        return $input;
    }

    public static function upload($file, $executor){
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
            \Excel::load($file_name, function($reader) use($totalItem, $executor, &$orderNeedToRecalculate, &$error) {
                try{
                    // mabill, madiachi, kg, dai, rong, cao, soluong, ppvnd, ghichu
                    $totalItem = 0;
                    $listItem = $reader->takeRows(2000)->toArray();
                    foreach ($listItem as $item) {
                        if($item['mabill']){
                            $totalItem++;
                        }else{
                            break;
                        }
                    }
                    $createdItem = 0;
                    for($index = 0; $index < $totalItem; $index++) {
                        $item = $listItem[$index];
                        $item['mabill'] = trim($item['mabill']);
                        $input = [
                            'user_id' => null,
                            'order_id' => null,
                            'purchase_id' => null,
                            'admin_id' => $executor->id,
                            'address_uid' => trim(strtoupper(str_replace(" ", "", $item['madiachi']))),
                            'code' => trim(strtoupper(str_replace(" ", "", $item['mabill']))),
                            'input_mass' => self::floatProcessing($item['kg']),
                            'length' => $item['dai']?:'0',
                            'width' => $item['rong']?:'0',
                            'height' => $item['cao']?:'0',
                            'packages' => $item['soluong']?$item['soluong']:'1',
                            'sub_fee' => $item['ppvnd']?$item['ppvnd']:'0',

                            'note' => $item['ghichu'],
                        ];  

                        $uploadValidate = self::uploadValidate($input);

                        $isError = $uploadValidate['isError'];
                        $isIgnore = $uploadValidate['isIgnore'];
                        $errorDetail = $uploadValidate['errorDetail'];
                        if(!$isError){
                            if(!$isIgnore){
                                $changeId = self::afterPassValidate($input, $executor);
                                if($changeId){
                                    $orderNeedToRecalculate[] = $changeId;
                                }
                            }
                        }else{
                            # Lỗi -> Ghi lại cn bill of landing false
                            if(!CnBillOfLandingFail::where('code', $input['code'])->first()){
                                CnBillOfLandingFail::addItem([
                                    'code' => $input['code'],
                                    'input_mass' => $input['input_mass'],
                                    'length' => $input['length'],
                                    'width' => $input['width'],
                                    'height' => $input['height'],
                                    'packages' => $input['packages'],
                                    'address_uid' => $item['madiachi'],
                                    'sub_fee' => $input['sub_fee'],
                                    'note' => $input['note'],
                                    'error_note' => implode(', ', $errorDetail)
                                ]);
                            }
                        }
                    }
                }
                catch(\Exception $e){$error = $e;}
                catch(\Error $e){$error = $e;}
            });
            # var_dump($error->getMessage());die;
            if($error){
                return ResTools::err(
                    'Lỗi trong quá trình đọc excel. Bạn hãy kiểm tra lại cấu trúc của file excel vừa upload. Lưu ý: File excel chỉ được chứa 1 sheet duy nhất.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            foreach ($orderNeedToRecalculate as $recalculateOrder){
                Order::recalculate($recalculateOrder);
            }
            $lastCount = self::count();
            $createdItem = $lastCount - $firstCount;
            $extraMessage = " $createdItem code tạo thành công.";
            $result = [];
            return ResTools::obj($result, trans('messages.add_success').$extraMessage);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function uploadValidate($input){
        # 1 bill 2 mã giao dịch -> impossible when uploading    
        $isError = false;
        $isIgnore = false;
        $errorDetail = [];
        $existItem = self::where('code', $input['code'])->first();
        if(
            !Tools::isInt($input['length']) ||
            !Tools::isInt($input['width']) ||
            !Tools::isInt($input['height']) ||
            !Tools::isInt($input['packages']) ||
            !Tools::isInt($input['sub_fee'])
        ){
            # Số nguyên -> kiện, dài, rộng, cao, số kiện, phụ phí
            $isError = true;
            $errorDetail[] = 'Dài, rộng, cao, số kiện, phụ phí không phải số nguyên';
        }else if(
            !is_numeric($input['input_mass'])
        ){
            # Số nguyên / ko nguyên -> khối lượng
            $isError = true;
            $errorDetail[] = 'Khối lượng không phải số';
        }else if(
            array_key_exists('address_uid', $input) &&
            trim($input['address_uid']) &&
            !Address::where('uid', $input['address_uid'])->first()
        ){
            # Cung cấp mã khách nhưng ko tồn tại
            $isError = true;
            $errorDetail[] = 'Mã khách hàng không tồn tại';
        }else if($existItem){
            if(
                $existItem->input_mass - ValidateTools::toFloat($input['input_mass']) >= 0.001 ||
                $existItem->length !== ValidateTools::toInt($input['length']) ||
                $existItem->width !== ValidateTools::toInt($input['width']) ||
                $existItem->height !== ValidateTools::toInt($input['height']) ||
                $existItem->sub_fee !== ValidateTools::toInt($input['sub_fee'])
            ){
                # Reupload trùng mã bill nhưng khác thuộc tính khác
                $isError = true;
                $errorDetail[] = 'Upload lại dữ liệu trùng mã bill nhưng khác các thuộc tính khác';
            }else{
                $isIgnore = true;
                # Trùng mã bill + trùng thuộc tính -> bỏ qua
            }
        }else if(BillOfLanding::where('code', $input['code'])->whereNotNull('cn_store_date')->first()){
            # 2 bill 1 mã giao dịch
            $isError = true;
            $errorDetail[] = 'Trùng mã bill';
        }
        
        $result = [
            'isError' => $isError,
            'isIgnore' => $isIgnore,
            'errorDetail' => $errorDetail
        ]; 

        return $result;
    }

    public static function afterPassValidate($input, $executor, $singleItem=false){
        # Thành công
        # Ghi lại cn bill of landing
        # Đánh dấu cn
        $input['input_mass'] = ValidateTools::toFloat($input['input_mass']);
        $input['length'] = ValidateTools::toInt($input['length']);
        $input['width'] = ValidateTools::toInt($input['width']);
        $input['height'] = ValidateTools::toInt($input['height']);
        $input['packages'] = ValidateTools::toInt($input['packages']);
        $input['sub_fee'] = ValidateTools::toInt($input['sub_fee']);
        $input['mass'] = self::calculateMass($input);

        if(array_key_exists('address_uid', $input) && trim($input['address_uid'])){
            $address = Address::where('uid', strtoupper($input['address_uid']))->first();
            unset($input['address_uid']);
            $input['user_id'] = $address->user->id;
            $input['address_id'] = $address->id;
            $input['rate'] = $address->user->rate;
            $input['delivery_fee_unit'] = $address->user->delivery_fee_unit?:$address->areaCode->delivery_fee_unit;
            $input['order_type'] = 'deposit';
            $input['cn_store_date'] = Tools::nowDateTime();
            $oldBildOfLanding = BillOfLanding::
                where('code', $input['code'])->
                // where('order_type', 'deposit')->
                first();
            # Nếu có cung cấp mã khách và mã khách có tồn tại
            if(!$oldBildOfLanding){
                # Nếu mã vận đơn chưa được khai báo -> thêm mới
                BillOfLanding::addItem($input, $executor);
            }else{
                # Nếu mã vận đơn đã được khai báo và chưa được đánh dấu về kho TQ -> cập nhật
                $oldBildOfLanding = BillOfLanding::
                    where('code', $input['code'])->
                    where('order_type', 'deposit')->
                    whereNull('cn_store_date')->
                    first();
                if($oldBildOfLanding){
                    $input1 = $input;
                    $input1['note'] = $oldBildOfLanding->note?$oldBildOfLanding->note.', '.$input1['note']:$input1['note'];
                    $item = BillOfLanding::editItem($oldBildOfLanding->id, $input1, $executor);
                }
            }
            # Ghi xuống danh sách vận đơn TQ
            self::addItemPure($input, $executor);
        }else{
            $correspondingCode = BillOfLanding::
                where('code', $input['code'])->
                // where('order_type', 'normal')->
                first();
            $oldCorrespondingCode = null;
            if($correspondingCode){
                # Nếu mã vận đơn này đã tồn tại trong danh sách vận đơn -> lấy vài thông tin cũ + cập nhật vận đơn gốc
                // For comparing sub_fee | mass to recalculate
                $oldCorrespondingCode = clone $correspondingCode;

                // Get info from correspongding code
                if($correspondingCode->vn_store_date){
                    $input['match'] = true;
                }
                $input['order_type'] = $correspondingCode->order?$correspondingCode->order->type:'deposit'; 
                $input['user_id'] = $correspondingCode->user_id;
                $input['order_id'] = $correspondingCode->order_id;
                $input['purchase_id'] = $correspondingCode->purchase_id;

                // Update info for corresponding code
                $correspondingCode->packages = $input['packages'];
                $correspondingCode->input_mass = $input['input_mass'];
                $correspondingCode->length = $input['length'];
                $correspondingCode->width = $input['width'];
                $correspondingCode->height = $input['height'];
                $correspondingCode->sub_fee = $input['sub_fee'];
                $correspondingCode->cn_store_date = Tools::nowDateTime();
                $correspondingCode->note = $correspondingCode->note?$correspondingCode->note . ', ' . $input['note'] : $input['note'];
                $correspondingCode->save();
                $correspondingCode = BillOfLanding::recalculate($correspondingCode);
            }else{
                # Nếu mã này không có trong danh sách vận đơn
                    # -> Tạo mới, thiếu user_id
                    # -> đơn hàng thiếu thông tin.
                $input['vn_store_date'] = null;
                $input['cn_store_date'] = Tools::nowDateTime();
                BillOfLanding::addItem($input, $executor);
            }

            # Ghi xuống danh sách vận đơn TQ
            $item = self::addItemPure($input, $executor);
            if($item['success'] && $oldCorrespondingCode){
                if($oldCorrespondingCode->sub_fee !== $input['sub_fee'] ||
                    $oldCorrespondingCode->mass !== $correspondingCode->mass
                ){
                    # Đối với các vận đơn đã tồn tại, có thay đổi phụ phí/khối lượng -> ghi lại để tính lại
                    if($oldCorrespondingCode->order_id){
                        // $orderNeedToRecalculate[] = $oldCorrespondingCode->order->id;
                        if($singleItem){
                            # Trong trường hợp sửa trong CnBillOfLandingFail -> chạy recalculate luôn
                            Order::recalculate($oldCorrespondingCode->order->id);
                        }
                        # Trả về các ID cần phải recalculate
                        return $oldCorrespondingCode->order->id;
                    }
                }
            }
            return null;
        }
    }

    public static function addItem($input, $executor){
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
            $checkDuplicate = CnBillOfLandingFail::where(["code" => $input["code"]])->first();
            if($checkDuplicate){
                return ResTools::err(
                    'Mã vận đơn này đang trong phần Vận Đơn TQ Lỗi. Bạn vui lòng sửa trong phần Vận Đơn TQ Lỗi.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if(array_key_exists('address_uid', $input) && $input['address_uid']){
                $address = Address::where('uid', strtoupper($input['address_uid']))->first();
                if(!$address){
                    return ResTools::err(
                        'Mã địa chỉ không tồn tại, xin vui lòng kiểm trai lại.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                $input['user_id'] = $address->user_id;
            }
            self::afterPassValidate($input, $executor, true);
            return ResTools::obj([], trans('messages.add_success'));

            // $input['mass'] = self::calculateMass($input);
            # $item = self::create($input);
            # return ResTools::obj($item, trans('messages.add_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function addItemPure($input, $executor){
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
            $checkDuplicate = CnBillOfLandingFail::where(["code" => $input["code"]])->first();
            if($checkDuplicate){
                return ResTools::err(
                    'Mã vận đơn này đang trong phần Vận Đơn TQ Lỗi. Bạn vui lòng sửa trong phần Vận Đơn TQ Lỗi.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if(array_key_exists('address_uid', $input) && trim($input['address_uid'])){
                $address = Address::where('uid', strtoupper($input['address_uid']))->first();
                if(!$address){
                    return ResTools::err(
                        'Mã địa chỉ không tồn tại, xin vui lòng kiểm trai lại.',
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
                $input['user_id'] = $address->user_id;
            }

            // $input['mass'] = self::calculateMass($input);
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
            $input['mass'] = self::calculateMass($input);
            $item->update($input);

            if($oldCode !== $item->code){
                # Change bol code -> mark cn_store_date to null
                $billOfLanding = BillOfLanding::where('code', $oldCode)->first();
                if($billOfLanding){
                    $billOfLanding->cn_store_date = null;
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

                $billOfLanding->input_mass = $item->input_mass;
                $billOfLanding->length = $item->length;
                $billOfLanding->width = $item->width;
                $billOfLanding->height = $item->height;
                $billOfLanding->packages = $item->packages;
                $billOfLanding->sub_fee = $item->sub_fee;
                $billOfLanding->cn_store_date = Tools::nowDateTime();
                $billOfLanding->save();
                BillOfLanding::recalculate($billOfLanding);
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
                    if($billOfLanding){
                        $billOfLanding->cn_store_date = null;
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
