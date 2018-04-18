<?php

namespace App\Modules\User\Controllers;

use Validator;
use Carbon\Carbon as Carbon;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class UserController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function list(Request $request){
        $model = new User();
        $params = ValidateTools::getFetchParams($request->all());
        $keyword = $params['keyword'];
        $orderBy = $params['orderBy'];
        $params = $params['params'];

        if(array_key_exists('page', $params)) unset($params['page']);
        if($request->token->role_type === 'admin'){
            $role = $request->token->role;
            if($role === config('app.chamsoc')){
                $params['admin_id'] = $request->token->parent->id;
                if(array_key_exists('customer_staff', $params)) unset($params['customer_staff']);
            }else{
                # if($role !== config('app.sadmin')){
                if(!in_array($role, [config('app.sadmin'), config('app.ketoan'), config('app.ketoantong')])){
                    $params['admin_id'] = -1;
                    if(array_key_exists('customer_staff', $params)) unset($params['customer_staff']);
                }
            }
        }else{
            $params['admin_id'] = -1;
            if(array_key_exists('customer_staff', $params)) unset($params['customer_staff']);
        }
        $result = User::list($params, $keyword, $orderBy);
        return response()->json($result);
    }

    public function obj(Request $request){
        $result = User::obj((int)$request->input('id', null));
        return response()->json($result);
    }

    public function statistics(Request $request){
        $dateType = ValidateTools::getRequestValue($request, 'date_type', 'str', 'last_30_days');
        $startDate = ValidateTools::getRequestValue($request, 'start_date', 'date', Carbon::today());
        $endDate = ValidateTools::getRequestValue($request, 'end_date', 'date', Carbon::today());
        if($request->token->role_type === 'admin'){
            $id = ValidateTools::getRequestValue($request, 'id', 'int', null);
        }else{
            $id = $request->token->parent->id;
        }

        $result = User::statistics($id, $dateType, $startDate, $endDate);
        return response()->json($result);
    }

    public function addItem(Request $request){
        $excludedFields = ['role', 'password'];
        $input = ValidateTools::validateData(
            $request->all(),
            User::$fieldDescriptions,
            [], $excludedFields);
        $result = User::addItem($input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $excludedFields = ['email', 'role', 'password'];
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), User::$fieldDescriptions
        );
        $result = User::editItem($id, $input['success']?$input['data']:$input, $_FILES);
        return Tools::jsonResponse($result);
    }

    public function signup(Request $request){
        $excludedFields = ['role'];
        $inputCheck = ValidateTools::validateData(
            $request->all(),
            User::$fieldDescriptions,
            [], $excludedFields);
        if(!$inputCheck["success"]){
            return response()->json($inputCheck["data"]);
        }
        $inputCheck['data']['password'] = md5($inputCheck['data']['password']);
        $inputCheck['data']['order_fee_factor'] = floatVal(\ConfigDb::get('phi-dat-hang'));
        $result = User::addItem($inputCheck["data"]);
        return response()->json($result);
    }

    public function assign(Request $request){
        $userId = ValidateTools::getRequestValue($request, 'user_id', 'int', null);
        $adminId = ValidateTools::getRequestValue($request, 'admin_id', 'int', null);
        $result = User::assign($userId, $adminId);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = $request->input('id', null);
        $result = User::removeItem($id);
        return response()->json($result);
    }

    public static function profile(Request $request){
        $result = User::obj($request->token->parent->id);
        return response()->json($result);
    }

    public static function updateProfile(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), User::$fieldDescriptions
        );
        $result = User::editItem($request->token->parent->id, $input['success']?$input['data']:$input, $_FILES);
        return response()->json($result);
    }

    public function authenticate(Request $request){
        $onlyFields = ['email', 'password'];
        $checkRules = ValidateTools::checkRules(
            $request->all(),
            User::$fieldDescriptions,
            $onlyFields
        );
        if($checkRules){
            return response()->json($checkRules);
        }

        $email = $request->input('email');
        $password = $request->input('password');
        $fingerprint = $request->header('Fingerprint');
        $result = User::authenticate($email, $password, $fingerprint);
        return response()->json($result);
    }

    public function logout(Request $request){
        $fingerprint = $request->header('Fingerprint');
        $result = User::logout($request->token, $fingerprint);
        return response()->json($result);
    }

    public static function resetPassword(Request $request){
        $onlyFields = ['email', 'password'];
        $input = ValidateTools::validateData(
            $request->all(), User::$fieldDescriptions, $onlyFields
        );
        $email = null;
        $password = null;
        $fingerprint = $request->header('Fingerprint');
        if($input['success']){
            $email = $input['data']['email'];
            $password = $input['data']['password'];
        }
        $result = User::resetPassword($email, $password, $fingerprint);
        return response()->json($result);
    }

    public static function resetPasswordConfirm(Request $request){
        $token = $request->input('token');
        $result = User::resetPasswordConfirm($token);
        return response()->json($result);
    }

    public static function changePassword(Request $request){
        $onlyFields = ['password'];
        $input = ValidateTools::validateData(
            $request->all(), User::$fieldDescriptions, $onlyFields
        );
        $result = User::changePassword(
            $input['success']?$input['data']['password']:null,
            $request->token->parent
        );
        return response()->json($result);
    }

    public static function changePasswordConfirm(Request $request){
        $token = $request->input('token');
        $result = User::changePasswordConfirm($token);
        return response()->json($result);
    }
}