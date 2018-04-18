<?php

namespace App\Modules\Role\Controllers;

use Validator;
use App\Modules\Role\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class RoleController extends Controller{

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
        $result = Role::obj($id);
        return response()->json($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Role);
        $result = Role::list(...$input);
        return response()->json($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Role::$fieldDescriptions
        );
        $result = Role::addItem($input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $input = ValidateTools::validateData(
            $request->all(), Role::$fieldDescriptions
        );
        $result = Role::editItem($id, $input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'str');
        $result = Role::removeItem($id);
        return response()->json($result);
    }
}