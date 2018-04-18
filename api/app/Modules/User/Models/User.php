<?php

namespace App\Modules\User\Models;

# use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon as Carbon;
use Validator;
use Auth;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Modules\Atoken\Models\Atoken;
use App\Modules\Admin\Models\Admin;
use App\Modules\WeightLog\Models\WeightLog;
use App\Modules\Permission\Models\Permission;
use App\Modules\LoginFailed\Models\LoginFailed;
use App\Modules\Role\Models\Role;
use App\Modules\Order\Models\Order;
use App\Modules\ShopDeliveryFee\Models\ShopDeliveryFee;
use App\Modules\DepositLog\Models\DepositLog;
use App\Modules\Address\Models\Address;
use App\Modules\AreaCode\Models\AreaCode;
use App\Modules\UserTransaction\Models\UserTransaction;

class User extends Authenticatable{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var string
     */
    protected $table = 'users';
    protected $appends = ['full_name', 'fulltitle', 'default_address_code'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'dathang_admin_id',
        'first_name',
        'last_name',
        'email',
        'uid',
        'password',
        'area_code_id',
        'phone',
        'address',
        'company',
        'avatar',
        'role_id',
        'permissions',

        'balance',
        'debt',
        'purchasing',

        'delivery_fee_unit',
        'rate',
        'order_fee_factor',
        'deposit_factor',
        'complain_day',
        'block_account'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'permissions',
        'fingerprint',
        'remember_token',
        'change_password_token',
        'change_password_token_created',
        'change_password_token_tmp',
        'reset_password_token',
        'reset_password_token_created',
        'reset_password_token_tmp',
        'signup_token',
        'signup_token_created',
        'created_at',
        'updated_at',
    ];

    public static $fieldDescriptions = [
        'first_name' => 'str,required|max:70',
        'last_name' => 'str,required|max:70',
        'email' => 'str,required|max:100',
        'uid' => 'str,max:100',
        'password' => 'str,max:100',
        'area_code_id' => 'int,required',
        'phone' => 'str',
        'address' => 'str,max:250',
        'company' => 'str,max:250',
        'avatar' => 'str',
        'role_id' => 'int',
        'admin_id' => 'int',
        'dathang_admin_id' => 'int',

        'order_fee_factor' => 'float',
        'rate' => 'int',
        'delivery_fee_unit' => 'int',
        'deposit_factor' => 'float',
        'complain_day' => 'float',
        'block_account' => 'bool'
    ];

    public static $searchFields = ['first_name', 'last_name', 'email', 'phone', 'company', 'uid'];

    public function role(){
        return $this->belongsTo('App\Modules\Role\Models\Role', 'role_id');
    }

    public function token(){
        return $this->hasMany('App\Modules\Atoken\Models\Atoken', 'user_id');
    }

    public function orders(){
        return $this->hasMany('App\Modules\Order\Models\Order', 'user_id');
    }

    public function addresses(){
        return $this->hasMany('App\Modules\Address\Models\Address', 'user_id');
    }

    public function userTransactions(){
        return $this->hasMany('App\Modules\UserTransaction\Models\UserTransaction', 'user_id');
    }

    public function areaCode(){
        return $this->belongsTo('App\Modules\AreaCode\Models\AreaCode', 'area_code_id');
    }

    public function admin(){
        return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
    }

    public function getFullNameAttribute($value){
        return $this->last_name . ' ' . $this->first_name;
    }

    public function getFulltitleAttribute($value){
        return trim(
            $this->id . ' / ' . 
            # $this->uid . ' / ' . 
            $this->last_name . ' ' . 
            $this->first_name
            # $this->phone . ' / ' . 
            # $this->email
        );
    }

    public function getDefaultAddressCodeAttribute($value){
        $defaultAddress = Address::where("user_id", $this->id)->where('default', true)->first();
        if($defaultAddress){
            return $defaultAddress->uid.': '.$this->address;
        }
        return $this->address;
    }

    public static function list($params=[], $keyword=null, $orderBy='-id'){
        try{
            $orderBy = Tools::parseOrderBy($orderBy);
            $listItem = User::where([]);

            /*
            if($keyword && strlen($keyword) >=1){
                if(is_numeric($keyword)){
                    $listItem->where('id', intval($keyword));
                }else{
                    if($keyword === 'no'){
                        $listDebtId = [];
                        $listItemForDebt = clone $listItem;
                        $listItemForDebt = $listItemForDebt->get();
                        foreach ($listItemForDebt as $item) {
                            $debt = UserTransaction::where('user_id', $item->id)->sum(\DB::raw('liabilities-credit_balance'));
                            if($debt > 0){
                                $listDebtId[] = $item->id;
                            }
                        }
                        if(count($listDebtId)){
                            $listItem->whereIn('id', $listDebtId);
                        }
                    }else{
                        $listItem = $listItem->where(function($query) use($keyword){
                            foreach(self::$searchFields as $key => $field){
                                $query->orWhere($field, 'ilike', '%' . $keyword . '%');
                            }
                        });
                    }
                }
            }
            */
            if(array_key_exists('admin_id', $params) && intval($params['admin_id'])){
                $listItem->where('admin_id', intval($params['admin_id']));
            }

            if(array_key_exists('customer_id', $params) && intval($params['customer_id'])){
                $listItem->where('id', intval($params['customer_id']));
            }

            if(array_key_exists('customer_staff', $params) && intval($params['customer_staff'])){
                $listItem->where('admin_id', intval($params['customer_staff']));
            }

            if(array_key_exists('lock', $params) && $params['lock'] !== 'all'){
                $listItem->where('block_account', $params['lock']==='yes'?true:false);
            }

            if(array_key_exists('debt', $params) && $params['debt'] !== 'all'){
                $listDebtId = [];
                $listItemForDebt = clone $listItem;
                $listItemForDebt = $listItemForDebt->get();
                foreach ($listItemForDebt as $item) {
                    $debt = UserTransaction::where('user_id', $item->id)->sum(\DB::raw('purchasing + liabilities - credit_balance'));
                    if($params['debt'] === 'yes'){
                        if($debt > 0){
                            $listDebtId[] = $item->id;
                        }
                    }else{
                        if($debt <= 0){
                            $listDebtId[] = $item->id;
                        }
                    }
                }
                if(count($listDebtId)){
                    $listItem->whereIn('id', $listDebtId);
                }
            }

            if(array_key_exists('care', $params) && $params['care'] !== 'all'){
                if($params['care'] === 'yes'){
                    $listItem->where(function($q){
                        $q->whereNotNull('admin_id')->where('admin_id', '!=', 0);
                    });
                }else{
                    $listItem->where(function($q){
                        $q->whereNull('admin_id')->orWhere('admin_id', 0);
                    });
                }
            }

            if(array_key_exists('address_uid', $params) && $params['address_uid']){
                $listItem->whereHas('addresses', function($q) use($params){
                    $q->where('uid', strtoupper($params['address_uid']));
                });
            }

            if(array_key_exists('rate', $params) && intval($params['rate'])){
                $listItem->where('rate', intval($params['rate']));
            }

            if(array_key_exists('deposit_factor', $params) && floatval($params['deposit_factor'])){
                $listItem->where('deposit_factor', floatval($params['deposit_factor']));
            }

            if(array_key_exists('order_fee_factor', $params) && floatval($params['order_fee_factor'])){
                $listItem->where('order_fee_factor', floatval($params['order_fee_factor']));
            }

            $listItem = $listItem->
                orderBy($orderBy[0], $orderBy[1])->
                paginate(config('app.page_size'));
            $extra = [
                'list_admin' => Admin::whereHas('role', function($q){
                    $q->where('uid', config('app.chamsoc'))->orderBy('last_name', 'asc');
                })->get(),
                'list_dathang_admin' => Admin::whereHas('role', function($q){
                    $q->where('uid', config('app.dathang'))->orderBy('last_name', 'asc');
                })->get(),
                'list_area_code' => AreaCode::
                    select('id', 'title', 'code', 'delivery_fee_unit')->
                    orderBy('id','asc')->
                    get(),
                'order_fee_factor' => \ConfigDb::get("phi-dat-hang")
            ];
            $extra['list_admin'] = Admin::
                select('id', 'first_name', 'last_name')->
                whereHas('role', function($q){
                    $q->where('uid', config('app.chamsoc'));
                })->
                orderBy('last_name', 'asc')->
                get();
            $extra['list_user'] = self::
                select('id', 'uid', 'first_name', 'last_name', 'email', 'phone')->
                orderBy('id', 'asc')->
                get();
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

            $listTransaction = UserTransaction::where('user_id', $result->id);
            $result->balance = $listTransaction->sum('credit_balance') - $listTransaction->sum('liabilities');
            $result->balance = $result->balance >= 0 ? $result->balance : 0;
            $result->liabilities = $listTransaction->sum('liabilities');
            $promotionLink = \ConfigDb::get('promotion-link');
            $extra = [
                'extension_url_shopping' => \ConfigDb::get('extension-url-shopping'),
                'promotion_link' => $promotionLink === '0' ? null : $promotionLink,
                'promotion_message' => \ConfigDb::get('promotion-message')
            ];
            return ResTools::obj($result, $original, $extra);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function addItem($input){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }
            $checkDuplicate = self::where(["uid" => $input["uid"]])->first();
            if($checkDuplicate){
                return ResTools::err(
                    'Tên đăng nhập này đã tồn tại, bạn vui lòng chọn tên đăng nhập khác.',
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $role = Role::where(['role_type_uid' => 'user', 'default_role' => true])->first();

            $checkEmail = self::where(["email" => $input["email"]])->first();
            if($checkEmail){
                return ResTools::err(
                    trans('messages.duplicate_email'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            if(array_key_exists('password', $input) && $input['password']){
                $input["password"] =\Hash::make($input["password"]);
            }else{
                $input["password"] =\Hash::make(str_random(config('app.random_size')));
            }
            $input["role_id"] = $role->id;
            $input["rate"] = intval(\ConfigDb::get('cny-vnd'));
            $item = self::create($input);
            # Add default address

            Address::addItem([
                'user_id' => $item->id,
                'area_code_id' => $item->area_code_id,
                'address' => $item->address
            ]);
            # Send email here;
            $clientResetPasswordUrl = \Config::get('app.client_user_url');
            $to = $item->email;
            $subject = 'Account for '.$item->last_name . ' created';
            $params = [
                'clientResetPasswordUrl' => $clientResetPasswordUrl,
                'last_name' => $item->last_name,
            ];
            Tools::sendEmail($to, $subject, 'emails.signup', $params);
            return ResTools::obj($item, trans('messages.add_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function editItem($id, $input, $file=null){
        try{
            if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
                return $input;
            }
            $excludedFields = ['email', 'role'];
            $input = Tools::ignoreKeys($input, $excludedFields);

            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            if(array_key_exists('uid', $input)){
                $checkDuplicate = self::where(["uid" => $input["uid"]])->first();
                if($checkDuplicate && $checkDuplicate->id !== $id){
                    return ResTools::err(
                        trans('messages.duplicate_item'),
                        ResTools::$ERROR_CODES['BAD_REQUEST']
                    );
                }
            }
            if(array_key_exists('admin_id', $input) && !$input['admin_id']){
                $input['admin_id'] = null;
            }

            if(array_key_exists('dathang_admin_id', $input) && !$input['dathang_admin_id']){
                $input['dathang_admin_id'] = null;
            }

            if($file){
                $image = Tools::uploadHandler($file, $item->avatar, 'avatar', 'avatar');
                if(!$image['success'] && !$image['blank']){
                    return ResTools::err(
                        $image['message'],
                        ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
                    );
                }
                $input['avatar'] = $image['path'];
                if(!$input['avatar']){
                    unset($input['avatar']);
                }
            }

            if(array_key_exists('password', $input) && $input['password']){
                $input['password'] = \Hash::make($input['password']);
            }
            if(array_key_exists('role_id', $input)){
                $role = Role::find(intVal($input['role_id']));
                $input["permissions"] = $role->detail;
            }
            # var_dump($input["permissions"]);die;
            $item->update($input);

            return ResTools::obj($item, trans('messages.edit_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function assign($user_id, $admin_id){
        $item = self::find($user_id);
        if(!$item){
            return ResTools::err(
                trans('messages.item_not_exist'),
                ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
            );
        }
        if($admin_id){
            $item->admin_id = $admin_id;
        }else{
            $item->admin_id = null;
        }
        $item->save();
        return ResTools::obj($item);
    }

    public static function removeItem($id, $force=false){
        try{
            $listId = explode(',', $id);
            $total = count($listId);
            if($total > 1){
                # Remove all
                $successList = [];
                foreach ($listId as $id) {
                    # code...
                    $id = ValidateTools::toInt($id);
                    $removeResult = self::removeSingle($id, $force);
                    if($removeResult['success']){
                        array_push($successList, $id);
                    }else{
                        $result = ['id' => $successList];
                        return ResTools::obj(
                            $result,
                            trans('messages.remove_all_message', [
                                    'count' => count($successList),
                                    'total' => $total
                                ]
                            )
                        );
                    }
                }
                $result = ['id' => $successList];
                return ResTools::obj($result, trans('messages.remove_success'));
            }

            # Remove single object
            $id = ValidateTools::toInt($id);
            return self::removeSingle($id, $force);
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function removeSingle($id, $force=false){
        try{
            $item = self::find($id);
            if(!$item){
                return ResTools::err(
                    trans('messages.item_not_exist'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $tokens = $item->token;
            foreach ($tokens as $token) {
                $token->delete();
            }
            if($force){
                $item->forceDelete();
            }else{
                $item->delete();
            }
            $result = ["id" => $id];
            return ResTools::obj($result, trans('messages.remove_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function authenticate($email, $password, $fingerprint){
        try{
            # Check user exist by email or fingerprint
            if(!LoginFailed::checkAllowLogin($email, $fingerprint) && config('app.app_env') !== 'testing'){
                # Can not login, must to wait ? minutes?
                $transParams = [
                    'count' => \Config::get('app.max_fail'),
                    'minutes' => \Config::get('app.waiting_on_max_fail')
                ];
                return ResTools::err(
                    trans('auth.login_fail_waiting', $transParams),
                    ResTools::$ERROR_CODES['UNAUTHORIZED']
                );
            }
            $user = null;

            if(Auth::guard('user')->attempt(['uid' => $email, 'password' => $password])){
                $user = self::where('uid', $email)->first();
            }
            if(Auth::guard('user')->attempt(['email' => $email, 'password' => $password])){
                $user = self::where('email', $email)->first();
            }

            if ($user){
                # Correct email/password
                # $user = Auth::user();
                $email = $user->email;
                $token = Atoken::newToken($user->id, $fingerprint, $user->role);
                $user->token = $token->token;
                LoginFailed::removeItem($email, $fingerprint);
                $result = [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role->uid,
                    'role_type' => $user->role->role_type_uid,
                    'token' => $user->token
                ];
                return ResTools::obj($result, trans('auth.authenticate_success'));
            }
            # Incorrect email/password
            LoginFailed::updateItem($email, $fingerprint);
            return ResTools::err(
                trans('auth.wrong_email_password'),
                ResTools::$ERROR_CODES['UNAUTHORIZED']
            );
        }catch(\Error $e){
            return ResTools::err(
                $e->getMessage(),
                ResTools::$ERROR_CODES['UNAUTHORIZED']
            );
        }
    }

    public static function resetPassword($email, $password, $fingerprint){
        try{
            $item = self::where('email', $email)->first();
            if(!$item){
                return ResTools::obj([], trans('auth.reset_password_success'));
            }
            $clientResetPasswordUrl = \Config::get('app.client_user_url').'reset_password_confirm/reset/';
            if($password){
                $item->reset_password_token_created = Carbon::now();
                $item->reset_password_token_tmp = \Hash::make($password);
                $item->reset_password_token = Atoken::newToken($item->id, $fingerprint, $item->role)->token;
                $item->save();
                $clientResetPasswordUrl.=$item->reset_password_token;
                # Send email here;
                $to = $item->email;
                $subject = 'Reset password for '.$item->last_name;
                $params = [
                    'clientResetPasswordUrl' => $clientResetPasswordUrl,
                    'last_name' => $item->last_name,
                ];
                Tools::sendEmail($to, $subject, 'emails.resetPassword', $params);
                return ResTools::obj([], trans('auth.reset_password_success'));
            }else{
                return ResTools::err(
                    trans('auth.password_can_not_be_null'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function resetPasswordConfirm($token){
        try{
            if(!$token){
                return ResTools::err(
                    trans('auth.invalid_reset_token'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $item = self::where('reset_password_token', $token)->first();
            if(!$item){
                return ResTools::err(
                    trans('auth.invalid_reset_token'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $currentDate = Carbon::now();
            $tokenCreatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $item->reset_password_token_created);
            $tokenLife = $currentDate->diffInMinutes($tokenCreatedAt);
            if($tokenLife > \Config::get('app.password_token_life')){
                $item->reset_password_token_created = null;
                $item->reset_password_token_tmp = null;
                $item->reset_password_token = null;
                $item->save();
                return ResTools::err(
                    trans('auth.reset_token_expired'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $item->password = $item->reset_password_token_tmp;
            $item->reset_password_token_created = null;
            $item->reset_password_token_tmp = null;
            $item->reset_password_token = null;
            $item->save();
            return ResTools::obj([], trans('auth.reset_password_confirm_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }

    public static function changePassword($password, $item){
        try{
            $fingerprint = $item->token[0]->fingerprint;
            $clientChangePasswordUrl = \Config::get('app.client_user_url').'change_password_confirm/change/';
            if($password){
                $item->change_password_token_created = Carbon::now();
                $item->change_password_token_tmp = \Hash::make($password);
                $item->change_password_token = str_random(36);
                $item->password = $item->change_password_token_tmp;
                $item->save();
                /*
                $clientChangePasswordUrl.=$item->change_password_token;
                # Send email here;
                $to = $item->email;
                $subject = 'Change password for '.$item->last_name;
                $params = [
                    'clientChangePasswordUrl' => $clientChangePasswordUrl,
                    'last_name' => $item->last_name,
                ];
                Tools::sendEmail($to, $subject, 'emails.changePassword', $params);
                */
                return ResTools::obj([], 'Mật khẩu của bạn đã được đổi thành công.');
            }else{
                return ResTools::err(
                    trans('auth.password_can_not_be_null'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }
    /*
    public static function changePasswordConfirm($token){
        try{
            if(!$token){
                return ResTools::err(
                    trans('auth.invalid_change_token'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $item = self::where('change_password_token', $token)->first();
            if(!$item){
                return ResTools::err(
                    trans('auth.invalid_change_token'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }

            $currentDate = Carbon::now();
            $tokenCreatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $item->change_password_token_created);
            $tokenLife = $currentDate->diffInMinutes($tokenCreatedAt);
            if($tokenLife > \Config::get('app.password_token_life')){
                $item->change_password_token_created = null;
                $item->change_password_token_tmp = null;
                $item->change_password_token = null;
                $item->save();
                return ResTools::err(
                    trans('auth.change_token_expired'),
                    ResTools::$ERROR_CODES['BAD_REQUEST']
                );
            }
            $item->password = $item->change_password_token_tmp;
            $item->change_password_token_created = null;
            $item->change_password_token_tmp = null;
            $item->change_password_token = null;
            $item->save();
            return ResTools::obj([], trans('auth.change_password_confirm_success'));
        }
        catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
        catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
    }
    */
    public static function logout($token, $fingerprint){
        if($token->fingerprint === $fingerprint){
            $token->delete();
            return ResTools::obj([], trans('auth.logout_success'));
        }else{
            return ResTools::err(
                trans('auth.not_enough_permission'),
                ResTools::$ERROR_CODES['UNAUTHORIZED']
            );
        }
    }

    public function save(array $options = array()){
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        if(in_array('updated_at', $colums)){
            $this->updated_at = Tools::nowDateTime();
        }
        if(!$this->exists){
            $role = Role::where(['role_type_uid' => 'user', 'default_role' => true])->first();
            $this->role_id = $role->id;
            $this->permissions = $role->detail;

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
