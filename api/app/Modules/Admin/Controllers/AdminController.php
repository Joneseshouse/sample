<?php

namespace App\Modules\Admin\Controllers;

use Validator;
use App\Modules\Admin\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;
use App\Helpers\SlackNotification;
use App\Notifications\ErrorMessage;


class AdminController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function obj(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $result = Admin::obj($id);
        return response()->json($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Admin);
        $result = Admin::list(...$input);
        return response()->json($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Admin::$fieldDescriptions
        );
        $result = Admin::addItem($input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $input = ValidateTools::validateData(
            $request->all(), Admin::$fieldDescriptions
        );
        $result = Admin::editItem($id, $input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'str');
        $result = Admin::removeItem($id);
        return response()->json($result);
    }

    public static function profile(Request $request){
        /*
        $notification = new SlackNotification();
        $message = [
            'message' => 'message',
            'url' => 'url',
            'input' => 'input'
        ];
        $notification->notify(new ErrorMessage($message));
        */

        $extra = [
            'extension_url_grabbing' => \ConfigDb::get('extension-url-grabbing')
        ];
        $result = ResTools::obj($request->token->parent, null, $extra);
        return response()->json($result);
    }

    public static function updateProfile(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Admin::$fieldDescriptions
        );
        $result = Admin::editItem($request->token->parent->id, $input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function authenticate(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $fingerprint = $request->header('Fingerprint');
        $result = Admin::authenticate($email, $password, $fingerprint);
        return response()->json($result);
    }

    public function logout(Request $request){
        $fingerprint = $request->header('Fingerprint');
        $result = Admin::logout($request->token, $fingerprint);
        return response()->json($result);
    }

    public static function resetPassword(Request $request){
        $onlyFields = ['email', 'password'];
        $input = ValidateTools::validateData(
            $request->all(), Admin::$fieldDescriptions, $onlyFields
        );
        $email = null;
        $password = null;
        $fingerprint = $request->header('Fingerprint');
        if($input['success']){
            $email = $input['data']['email'];
            $password = $input['data']['password'];
        }
        $result = Admin::resetPassword($email, $password, $fingerprint);
        return response()->json($result);
    }

    public static function resetPasswordConfirm(Request $request){
        $token = ValidateTools::getRequestValue($request, 'token', 'str');
        $result = Admin::resetPasswordConfirm($token);
        return response()->json($result);
    }

    public static function changePassword(Request $request){
        $password = ValidateTools::getRequestValue($request, 'password', 'str');
        $result = Admin::changePassword(
            $password,
            $request->token->parent
        );
        return response()->json($result);
    }

    public static function changePasswordConfirm(Request $request){
        $token = ValidateTools::getRequestValue($request, 'token', 'str');
        $result = Admin::changePasswordConfirm($token);
        return response()->json($result);
    }
}
