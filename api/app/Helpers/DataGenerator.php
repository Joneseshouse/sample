<?php namespace App\Helpers;

use Carbon\Carbon as Carbon;
use App\Helpers\Tools;

use App\Modules\Atoken\Models\Atoken;
use App\Modules\Config\Models\Config;
use App\Modules\RoleType\Models\RoleType;
use App\Modules\Role\Models\Role;
use App\Modules\User\Models\User;
use App\Modules\Admin\Models\Admin;
use App\Modules\Address\Models\Address;
use App\Modules\Category\Models\Category;
use App\Modules\Banner\Models\Banner;
use App\Modules\Article\Models\Article;
use App\Modules\Shop\Models\Shop;
use App\Modules\Permission\Models\Permission;
use App\Modules\Order\Models\Order;
use App\Modules\OrderItem\Models\OrderItem;
use App\Modules\Purchase\Models\Purchase;
use App\Modules\Bank\Models\Bank;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;
use App\Modules\CnBillOfLandingFail\Models\CnBillOfLandingFail;
use App\Modules\VnBillOfLanding\Models\VnBillOfLanding;
use App\Modules\ExportBill\Models\ExportBill;
use App\Modules\RateLog\Models\RateLog;
use App\Modules\UserOrderLog\Models\UserOrderLog;
use App\Modules\AreaCode\Models\AreaCode;
use App\Modules\CheckBill\Models\CheckBill;
use App\Modules\CollectBol\Models\CollectBol;


class DataGenerator {
    public function __construct() {
        $this->password = 'Qwerty!@#456';
        $this->password_hash = '$2y$10$/zo2NiM6ZzE1V7OUFkA7veYGbFnYxv.be/49XlrGLpGYfEVPTsmz.';
        $this->fingerprint = '4742184c20820345808b6a70a22c19d9';
    }

    public function config($counter, $dataOnly=false, $list=false){
        $model = new Config;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => "title-$i",
                'value' => "title $i"
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function roleType($counter, $dataOnly=false, $list=false){
        $model = new RoleType;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => "title-$i",
                'title' => "title $i"
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function roleTypeUser($counter, $dataOnly=false, $list=false){
        $model = new RoleType;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => "user".($i===1?'':$i),
                'title' => "user".($i===1?'':$i)
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function roleTypeAdmin($counter, $dataOnly=false, $list=false){
        $model = new RoleType;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => "admin".($i===1?'':$i),
                'title' => "admin".($i===1?'':$i)
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function role($counter, $dataOnly=false, $list=false){
        foreach (RoleType::all() as $item) {$item->forceDelete();}
        $roleType = $this->roleType(1);
        $model = new Role;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'role_type_id' => $roleType->id,
                'role_type_uid' => $roleType->uid,
                'uid' => "title-$i",
                'title' => "title $i",
                'detail' => "url$1/action$1",
                'default_role' => $i===1?true:false,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function roleUser($counter, $dataOnly=false, $list=false){
        foreach (RoleType::all() as $item) {$item->forceDelete();}
        $roleType = $this->roleTypeUser(1);
        $model = new Role;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'role_type_id' => $roleType->id,
                'role_type_uid' => $roleType->uid,
                'uid' => "title-$i",
                'title' => "title $i",
                'detail' => "url$1/action$1",
                'default_role' => $i===1?true:false,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function roleAdmin($counter, $dataOnly=false, $list=false){
        foreach (RoleType::all() as $item) {$item->forceDelete();}
        $roleType = $this->roleTypeAdmin(1);
        $model = new Role;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'role_type_id' => $roleType->id,
                'role_type_uid' => $roleType->uid,
                'uid' => "title-$i",
                'title' => "title $i",
                'detail' => "url$1/action$1",
                'default_role' => $i===1?true:false,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function admin($counter, $dataOnly=false, $list=false){
        foreach (Role::all() as $item) {$item->forceDelete();}
        $model = new Admin;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'email' => "email$i@gmail.com",
                'first_name' => "first name $i",
                'last_name' => "last name $i",
                'password' => $this->password_hash,
                'role_id' => $this->roleAdmin(1)->id,
                'permissions' => '',
                'block_account' => false
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function user($counter, $dataOnly=false, $list=false){
        foreach (Role::all() as $item) {$item->forceDelete();}
        $model = new User;
        $result = [];
        $areaCode = $this->areaCode(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'admin_id' => null,
                'uid' => "uid$i",
                'email' => "email$i@gmail.com",
                'first_name' => "first name $i",
                'last_name' => "last name $i",
                'password' => $this->password_hash,
                'avatar' => null,
                'area_code_id' => $areaCode->id,
                'phone' => "phone $i",
                'address' => "address $i",
                'company' => "company $i",
                'role_id' => $this->roleUser(1)->id,
                'permissions' => '',
                # Default values
                'rate' => 3400,
                'order_fee_factor' => 5,
                'deposit_factor' => 50,
                'complain_day' => 2,
                'delivery_fee_unit' => null,
                'block_account' => false
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        if(!$list){
            $item = $model::where('email', $result[$counter - 1]['email'])->first();
            if($item){
                return $item;
            }
            $item = $model::create($result[$counter - 1]);
            return $item;
        }
        return $model::insert($result);
        // return !$list?($model::where('email', $result[$counter - 1]['email'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function category($counter, $dataOnly=false, $list=false){
        $model = new Category;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => "title-$i",
                'title' => "title $i",
                'ascii_title' => "title $i",
                'single' => false,
                'type' => "article",
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function banner($counter, $dataOnly=false, $list=false){
        foreach (Category::all() as $item) {$item->forceDelete();}
        $category = $this->category(1);
        $model = new Banner;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'category_id' => $category->id,
                'title' => "title-$i",
                'subtitle' => "subtitle $i",
                'url' => "url $i",
                'image' => "image$i.jpg"
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function article($counter, $dataOnly=false, $list=false){
        foreach (Category::all() as $item) {$item->forceDelete();}
        $category = $this->category(1);
        $model = new Article;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'category_id' => $category->id,
                'title' => "title-$i",
                'ascii_title' => "title-$i",
                'slug' => "title-$i",
                'content' => "<div>content-$i</div>",
                'preview' => "preview $i",
                'thumbnail' => "image$i.jpg",
                'thumbnail_ratio' => 1,
                'public' => true,
                'home' => true,
                'order' => $i,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function shop($counter, $dataOnly=false, $list=false){
        $model = new Shop;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => "uid-$i",
                'title' => "title $i",
                'vendor' => "vendor $i"
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function permission($counter, $dataOnly=false, $list=false){
        $model = new Permission;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'title' => "title-$i",
                'ascii_title' => "title $i",
                'action' => "action $i",
                'route' => "api/v1/$i",
                'module' => "module1"
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function address($counter, $dataOnly=false, $list=false){
        foreach (Atoken::all() as $item) {$item->forceDelete();}
        foreach (User::all() as $item) {$item->forceDelete();}
        $model = new Address;
        $result = [];
        $user = $this->user(1);
        for($i = 1; $i <= $counter; $i++){
            $areaCode = $this->areaCode($i);
            array_push($result, [
                'user_id' => $user->id,
                'uid' => $user->id.$areaCode->code.$i,
                'address' => $areaCode->code."$i",
                'area_code_id' => $areaCode->id,
                'order' => $i - 1,
                'phone' => 'phone-'.$i,
                'fullname' => 'fullname-'.$i,
                'default' => $i===1?true:false,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('address', $result[$counter - 1]['address'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function bank($counter, $dataOnly=false, $list=false){
        foreach (User::all() as $item) {$item->forceDelete();}
        $model = new Bank;
        $result = [];
        $user = $this->user(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'user_id' => $user->id,
                'title' => "bank-$i",
                'branch' => "branch-$i",
                'account_number' => "account_number-$i",
                'type' => 'vn'
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('account_number', $result[$counter - 1]['account_number'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function order($counter, $dataOnly=false, $list=false){
        # foreach (Address::all() as $item) {$item->forceDelete();}
        $today = Tools::nowDate();
        foreach (User::all() as $item) {$item->forceDelete();}
        $model = new Order;
        $result = [];
        $user = $this->user(1);
        $admin = $this->admin(1);
        $address = $this->address(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'admin_id' => $admin->id,
                'confirm_admin_id' => $admin->id,
                'user_id' => $user->id,
                'address_id' => $address->id,
                'uid' => $address->uid.($today->day>9?(string)$today->day:'0'.(string)$today->day).Tools::monthToChar($today->month)."$i",
                'mass' => 20.6, // Sum of all shop
                'packages' => 20, // Sum of all shop
                'delivery_fee' => 130000, // Sum of all shop
                'inland_delivery_fee' => 130000, // Sum of all shop
                'inland_delivery_fee_raw' => 130000, // Sum of all shop
                'order_fee' => 5000000*$user->order_fee_factor/100,
                'amount' => 5000000, // Sum of all shop
                'total' => 5250000, // Sum of all shop + order_fee
                'month' => 1,
                'year' => 2017,
                'status' => 'new',
                'type' => 'normal',
                'order' => 0,
                # Default values
                'rate' => $user->rate,
                'order_fee_factor' => $user->order_fee_factor,
                'deposit_factor' => $user->deposit_factor,
                'complain_day' => $user->complain_day,
                'number_of_bills_of_landing' => 2,
                'number_of_purchases' => 2,
                'sub_fee' => 0,
                'insurance_fee' => 0,
                'total_raw' => 0,
                'confirm_date' => null,
                'note' => null
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('uid', $result[$counter - 1]['uid'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function orderItem($counter, $dataOnly=false, $list=false){
        foreach (Order::all() as $item) {$item->forceDelete();}
        $model = new OrderItem;
        $order = $this->order(1);
        $shop = $this->shop(1);
        $purchase = $this->purchase(1);
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'order_id' => $order->id,
                'shop_id' => $shop->id,
                'purchase_id' => $purchase->id,
                'title' => "title-$i",
                'avatar' => "avatar-$i",
                'message' => "message-$i",
                'properties' => "properties-$i",
                'quantity' => 5,
                'remain_quantity' => 5,
                'unit_price' => 36.5,
                'url' => "url-$i",
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function orderItem2($counter, $dataOnly=false, $list=false){
        foreach (Order::all() as $item) {$item->forceDelete();}
        $model = new OrderItem;
        $order = $this->order(1);
        $shop1 = $this->shop(1);
        $shop2 = $this->shop(2);
        $purchase1 = $this->purchase(1);
        $purchase2 = $this->purchase(2);
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'order_id' => $order->id,
                'shop_id' => $i<=5?$shop1->id:$shop2->id,
                'purchase_id' => $i<=5?$purchase1->id:$purchase2->id,
                'title' => "title-$i",
                'avatar' => "avatar-$i",
                'message' => "message-$i",
                'properties' => "properties-$i",
                'quantity' => $i<=5?5:7,
                'remain_quantity' => $i<=5?5:7,
                'unit_price' => $i<=5?36.5:39.5,
                'url' => "url-$i",
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where($result[$counter - 1])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function purchase($counter, $dataOnly=false, $list=false){
        $model = new Purchase;
        $result = [];
        $order = $this->order(1);
        $shop = $this->shop(1);
        for($i = 1; $i <= $counter; $i++){
            $mass = $i===1?20:10;
            $deliveryFeeUnit = $order->address->areaCode->delivery_fee_unit;
            # print_r($order->address->areaCode);die;
            $inlandDeliveryFeeRaw = 20;
            array_push($result, [
                'user_id' => $order->user->id,
                'order_id' => $order->id,
                'shop_id' => $shop->id,
                'code' => "code-$i",
                'amount' => 500,
                'real_amount' => 450,
                'delivery_fee' => $mass * $deliveryFeeUnit,
                'total' => 600,
                'mass' => $i===1?20:10,
                'packages' => $i===1?20:10,
                'delivery_fee_unit' => $deliveryFeeUnit, // 30
                'inland_delivery_fee_raw' => $inlandDeliveryFeeRaw,
                'inland_delivery_fee' => $inlandDeliveryFeeRaw * $order->rate, # 20 * 3400 = 68000
                'number_of_bills_of_landing' => 2,
                'sub_fee' => 0,
                'insurance_fee' => 0,
                'total_raw' => 0,
                'note' => null
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('code', $result[$counter - 1]['code'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function billOfLanding($counter, $dataOnly=false, $list=false){
        $model = new BillOfLanding;
        $result = [];
        $order = $this->order(1);
        $purchase = $this->purchase(1);
        $user = $this->user(1);
        $address = $this->address(1);
        for($i = 1; $i <= $counter; $i++){
            $mass = 4;
            $rate = $user->rate;
            $deliveryFeeUnit = $address->areaCode->delivery_fee_unit;
            $deliveryFee = $mass * $deliveryFeeUnit;
            $insuranceRegister = $i<3?true:false;
            $insuranceFactor = $insuranceRegister?5:3;
            $insuranceValue = 500;
            $insuranceFee = $insuranceRegister?$insuranceValue*5/100*$rate:$mass*$deliveryFeeUnit*3/100;
            $insuranceFeeRaw = $insuranceRegister?$insuranceValue*5/100:0;
            $subFee = 200000;
            $inlandDeliveryFeeRaw = $purchase->inland_delivery_fee_raw;
            $inlandDeliveryFee = $inlandDeliveryFeeRaw * $rate;
            $totalRaw = $inlandDeliveryFeeRaw;
            $total = round($totalRaw * $rate + $deliveryFee + $insuranceFee + $subFee);
            array_push($result, [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'purchase_id' => $purchase->id,
                'address_id' => $order->address_id,
                'order_type' => 'deposit',
                'code' => "CODE-$i",
                'transform_factor' => 6000,
                'packages' => 1,
                'length' => 10,
                'width' => 24,
                'height' => 100,
                'input_mass' => 3,
                'calculated_mass' => $mass,
                'mass' => $mass,
                'init_mass' => $mass,
                'packages' => 2,
                'sub_fee' => $subFee,
                'insurance_register' => $insuranceRegister,
                'insurance_factor' => $insuranceFactor,
                'insurance_value' => $insuranceValue,
                'insurance_fee' => $insuranceFee,
                'insurance_fee_raw' => $insuranceFeeRaw,
                'cn_store_date' => null,
                'vn_store_date' => null,
                'export_store_date' => null,
                'complain_date' => null,
                'export_bill_id' => null,
                'rate' => $user->rate,
                'address_id' => $address->id,
                'delivery_fee_unit' => $deliveryFeeUnit,
                'delivery_fee' => $deliveryFee,
                'total_raw' => $totalRaw,
                'total' => $total,
                'inland_delivery_fee_raw' => $inlandDeliveryFeeRaw,
                'inland_delivery_fee' => $inlandDeliveryFee,
                'amount_raw' => 0,
                'amount' => 0,
                'order_fee' => 0,
                'note' => 'note',
                'wooden_box' => false,

                'complain_amount' => 500000,
                'complain_resolve' => false,
                'complain_change_date' => Tools::nowDate(),
                'complain_turn' => 'admin',
                'complain_type' => 'change', // change, change_discount, reject, accept_discount
                'complain_note_user' => null,
                'complain_note_admin' => null,
                'straight_delivery' => false,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('code', $result[$counter - 1]['code'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function billOfLanding2($counter, $dataOnly=false, $list=false){
        $model = new BillOfLanding;
        $result = [];
        $order = $this->order(1);
        $purchase = $this->purchase(2);

        $rate = $order->rate;
        $inlandDeliveryFeeRaw = $purchase->inland_delivery_fee_raw;
        $inlandDeliveryFee = $inlandDeliveryFeeRaw * $rate;
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'user_id' => $order->user->id,
                'order_id' => $order->id,
                'purchase_id' => $purchase->id,
                'address_id' => $order->address_id,
                'order_type' => 'deposit',
                'code' => "CODE-$i",
                'transform_factor' => 6000,
                'packages' => 1,
                'length' => 10,
                'width' => 24,
                'height' => 100,
                'input_mass' => 9,
                'calculated_mass' => 4,
                'mass' => 9,
                'packages' => 1,
                'sub_fee' => 100000,
                'insurance_register' => true,
                'insurance_factor' => 5,
                'insurance_value' => 5000,
                'insurance_fee' => 250,
                'cn_store_date' => null,
                'vn_store_date' => null,
                'export_store_date' => null,
                'export_bill_id' => null,
                'rate' => $order->user->rate,
                'address_id' => null,
                'delivery_fee' => 0,
                'delivery_fee_unit' => $order->address->areaCode->delivery_fee_unit,
                'total_raw' => 0,
                'total' => 0,
                'inland_delivery_fee_raw' => $inlandDeliveryFeeRaw,
                'inland_delivery_fee' => $inlandDeliveryFee,
                'amount_raw' => 0,
                'amount' => 0,
                'order_fee' => 0,
                'note' => 'note'
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('code', $result[$counter - 1]['code'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function cnBillOfLanding($counter, $dataOnly=false, $list=false){
        $model = new CnBillOfLanding;
        $result = [];
        $admin = $this->admin(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'user_id' => null,
                'order_id' => null,
                'purchase_id' => null,
                'admin_id' => $admin->id,
                'code' => "CODE-$i",
                'mass' => 9,
                'input_mass' => 9,
                'length' => 60,
                'width' => 60,
                'height' => 10,
                'packages' => 1,
                'sub_fee' => 100000,
                'note' => 'note',
                'order_type' => $i <= 2 ? 'deposit' : 'normal',
                'match' => true,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('code', $result[$counter - 1]['code'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function vnBillOfLanding($counter, $dataOnly=false, $list=false){
        $model = new VnBillOfLanding;
        $result = [];
        $admin = $this->admin(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'admin_id' => $admin->id,
                'code' => "CODE-$i",
                'order_type' => 'deposit',
                'note' => 'note',
                'match' => true,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('code', $result[$counter - 1]['code'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function exportBill($counter, $dataOnly=false, $list=false){
        $model = new ExportBill;
        $result = [];
        $admin = $this->admin(1);
        $user = $this->user(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'uid' => "uid-$i",
                'note' => "note-$i",
                'sub_fee' => 50000,
                'amount' => 1000000,
                'total' => 150000,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('uid', $result[$counter - 1]['uid'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function rateLog($counter, $dataOnly=false, $list=false){
        $model = new RateLog;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'rate' => 3444,
                'buy_rate' => 3444,
                'sell_rate' => 3444,
                'order_rate' => 3444,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?$model::create($result[$counter - 1]):$model::insert($result);
    }

    public function cnBillOfLandingFail($counter, $dataOnly=false, $list=false){
        $model = new CnBillOfLandingFail;
        $result = [];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'code' => "CODE-$i",
                'input_mass' => '5.5',
                'length' => '60',
                'width' => '60',
                'height' => '60',
                'packages' => '5',
                'address_uid' => "1HN$i",
                'sub_fee' => '50000',
                'note' => 'note',
                'error_note' => 'Mã khách hàng không tồn tại',
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?$model::create($result[$counter - 1]):$model::insert($result);
    }

    public function userOrderLog($counter, $dataOnly=false, $list=false){
        $model = new UserOrderLog;
        $result = [];
        $uid = 1488420033;
        $order = $this->order(1);
        $purchase = $this->purchase(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'uid' => $uid + $i,
                'user_id' => $order->user->id, # User operations
                'user_full_name' => $order->user->full_name, # User operations
                'admin_id' => null, # Admin operations
                'admin_full_name' => null, # Admin operations
                'order_id' => $order->id,
                'purchase_id' => $purchase->id,
                'is_normal' => true,
                'target' => 'order', # add/edit/remove/chat
                'type' => 'add', # add/edit/remove/chat
                'content' => 'some content', # HTML format
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?$model::create($result[$counter - 1]):$model::insert($result);
    }

    public function areaCode($counter, $dataOnly=false, $list=false){
        $model = new AreaCode;
        $result = [];
        $listCode = ['HN', 'SG', 'DN', 'HL', 'SL', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN', 'HN'];
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'title' => "title-$i",
                'code' => $listCode[$i],
                'delivery_fee_unit' => 30,
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?($model::where('code', $result[$counter - 1]['code'])->first()?:$model::create($result[$counter - 1])):$model::insert($result);
    }

    public function checkBill($counter, $dataOnly=false, $list=false){
        $model = new CheckBill;
        $result = [];
        $purchase = $this->purchase(1);
        $admin = $this->admin(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'user_id' => $purchase->user->id, # User operations
                'order_id' => $purchase->order->id,
                'purchase_id' => $purchase->id,
                'bill_of_landing_id' => null,
                'admin_id' => $admin->id, # Admin operations
                'mass' => 10,
                'packages' => 6,
                'note' => null
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?$model::create($result[$counter - 1]):$model::insert($result);
    }

    public function collectBol($counter, $dataOnly=false, $list=false){
        $model = new CollectBol;
        $result = [];
        $admin = $this->admin(1);
        for($i = 1; $i <= $counter; $i++){
            array_push($result, [
                'admin_id' => $admin->id,
                'purchase_code' => "PURCHASE-$i",
                'bill_of_landing_code' => "CODE-$i",
                'real_amount' => 400,
                'note' => 'hello'
            ]);
        }
        if($dataOnly){
            return !$list?$result[$counter - 1]:$result;
        }
        return !$list?$model::create($result[$counter - 1]):$model::insert($result);
    }
}