<?php

namespace App\Modules\RoleType\Controllers;

use Validator;
use App\Modules\RoleType\Models\RoleType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class RoleTypeController extends Controller{

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
        $result = RoleType::obj($id);
        return response()->json($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new RoleType);
        $result = RoleType::list(...$input);
        return response()->json($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), RoleType::$fieldDescriptions
        );
        $result = RoleType::addItem($input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $input = ValidateTools::validateData(
            $request->all(), RoleType::$fieldDescriptions
        );
        $result = RoleType::editItem($id, $input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'str');
        $result = RoleType::removeItem($id);
        return response()->json($result);
    }
}