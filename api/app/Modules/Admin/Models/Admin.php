<?php

namespace App\Modules\Admin\Models;

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
use App\Modules\Permission\Models\Permission;
use App\Modules\LoginFailed\Models\LoginFailed;
use App\Modules\Role\Models\Role;

class Admin extends Authenticatable{
	use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
	 *
	 * @var string
	 */
	protected $table = 'admins';
	protected $appends = ['full_name'];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'role_id',
		'password',
		'permissions',
		'block_account'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
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
		'permissions'
	];

	public static $fieldDescriptions = [
		'first_name' => 'str,required|max:70',
		'last_name' => 'str,required|max:70',
		'email' => 'str,required|email|max:100',
		'password' => 'str,max:100',
		'role_id' => 'int,required',
		'block_account' => 'bool'
	];

	public static $searchFields = ['first_name', 'last_name', 'email'];

	public function token(){
		return $this->hasMany('App\Modules\Atoken\Models\Atoken', 'admin_id');
	}

	public function role(){
        return $this->belongsTo('App\Modules\Role\Models\Role', 'role_id');
    }

    public function getFullNameAttribute($value){
        return $this->first_name . ' ' . $this->last_name;
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

	public static function list($params=[], $keyword=null, $orderBy='-id'){
		try{
			$orderBy = Tools::parseOrderBy($orderBy);
			$listItem = Admin::with(['role' => function($q){
				$q->select('id', 'uid', 'role_type_uid');
			}])->where($params);
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
				'list_role' => Role::select('id', 'title', 'uid')->
					where('role_type_uid', 'admin')->
					orderBy('id', 'asc')->
					get()
			];

			return ResTools::lst($listItem, $extra);
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function addItem($input){
		try{
			if(array_key_exists('success', $input) && array_key_exists('status_code', $input) && $input['status_code'] !== 200){
	            return $input;
			}
	        $excludedFields = ['role'];
	        $input = Tools::ignoreKeys($input, $excludedFields);

			$checkEmail = self::where(["email" => $input["email"]])->first();
			if($checkEmail){
				return ResTools::err(
					trans('messages.duplicate_email'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}
			if(array_key_exists('password', $input) && $input["password"]){
				$input["password"] = \Hash::make($input["password"]);
			}else{
				$input["password"] = \Hash::make(str_random(config('app.random_size')));
			}
			$role = Role::find(intVal($input['role_id']));
			$input["permissions"] = $role->detail;
			$input["role"] = $role->uid;
			$item = self::create($input);

			# Send email here;
			$clientResetPasswordUrl = \Config::get('app.client_admin_url');
			$to = $item->email;
			$subject = 'Account for '.$item->last_name . ' created';
			$params = [
				'clientResetPasswordUrl' => $clientResetPasswordUrl,
				'last_name' => $item->last_name,
			];
			Tools::sendEmail($to, $subject, 'emails.signup', $params);
			$item->role;
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

	        $excludedFields = ['role'];
	        $input = Tools::ignoreKeys($input, $excludedFields);

			$item = self::find($id);
			if(!$item){
				return ResTools::err(
					trans('messages.item_not_exist'),
					ResTools::$ERROR_CODES['BAD_REQUEST']
				);
			}

			if(array_key_exists('email', $input) && $input['email']){
				$checkDuplicate = self::where(["email" => $input["email"]])->first();
				if($checkDuplicate && $checkDuplicate->id !== $id){
					return ResTools::err(
						trans('messages.duplicate_item'),
						ResTools::$ERROR_CODES['BAD_REQUEST']
					);
				}
			}else{
				unset($input["email"]);
			}

			if(array_key_exists('password', $input) && $input["password"]){
				$input["password"] = \Hash::make($input["password"]);
			}else{
				unset($input["password"]);
			}

			if(array_key_exists('role_id', $input) && $input['role_id']){
				$role = Role::find(intVal($input['role_id']));
				$input["permissions"] = $role->detail;
				$input["role"] = $role->uid;
			}else{
				unset($input["role_id"]);
			}
			$item->update($input);
			$item->role;
			return ResTools::obj($item, trans('messages.edit_success'));
		}
		catch(\Exception $e){return ResTools::criticalErr(Tools::errMessage($e));}
		catch(\Error $e){return ResTools::criticalErr(Tools::errMessage($e));}
	}

	public static function removeItem($id, $force=false){
		try{
			$result = null;
			$listId = explode(',', $id);
			foreach ($listId as $index => $id) {$listId[$index] = intval($id);}

			$listItem = self::whereIn('id', $listId)->get();
			if($listItem->count()){
				foreach ($listItem as $item) {
					if($force){
						$item->forceDelete();
					}else{
						$item->delete();
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
			if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])){
				# Correct email/password
				# $user = Auth::user();
                $user = self::where('email', $email)->first();
				$token = Atoken::newToken($user->id, $fingerprint, $user->role);
				$user->token = $token->token;
				LoginFailed::removeItem($email, $fingerprint);
				$allowMenus = Tools::parseRole($user->role->detail);
				$allowMenus[] = 'area-code';
				$result = [
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'email' => $user->email,
					'role' => $user->role->uid,
					'allow_menus' => $allowMenus,
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
			$clientResetPasswordUrl = \Config::get('app.client_admin_url').'reset_password_confirm/reset/';
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
			if($tokenLife > config('app.password_token_life')){
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
			$clientChangePasswordUrl = \Config::get('app.client_admin_url').'change_password_confirm/change/';
			if($password){
				$item->change_password_token_created = Carbon::now();
				$item->change_password_token_tmp = \Hash::make($password);
				$item->change_password_token = str_random(36);
				$item->save();
				$clientChangePasswordUrl.=$item->change_password_token;
				# Send email here;
				$to = $item->email;
				$subject = 'Change password for '.$item->last_name;
				$params = [
					'clientChangePasswordUrl' => $clientChangePasswordUrl,
					'last_name' => $item->last_name,
				];
				Tools::sendEmail($to, $subject, 'emails.changePassword', $params);
				return ResTools::obj([], trans('auth.change_password_success'));
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
			if(in_array('created_at', $colums)){
				$this->created_at = Tools::nowDateTime();
			}
			if(self::withTrashed()->count() > 0){
				$largestIdItem = self::withTrashed()->orderBy('id', 'desc')->first();
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
