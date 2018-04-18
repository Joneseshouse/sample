<?php

namespace App\Modules\Permission\Controllers;

use Validator;
use App\Modules\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class PermissionController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function syncList(Request $request){
        $result = Permission::syncList();
        return response()->json($result);
    }

    public function obj(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $result = Permission::obj($id);
        return response()->json($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Permission);
        $result = Permission::list(...$input);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $input = ValidateTools::validateData(
            $request->all(), Permission::$fieldDescriptions
        );
        $result = Permission::editItem($id, $input['success']?$input['data']:$input);
        return response()->json($result);
    }
}