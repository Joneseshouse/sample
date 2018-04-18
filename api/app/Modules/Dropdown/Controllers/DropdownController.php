<?php

namespace App\Modules\Dropdown\Controllers;

use Validator;
use App\Modules\Dropdown\Models\Dropdown;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class DropdownController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function list(Request $request){
        $params = json_decode($request->input('params', '{}'), true);
        $keyword = $request->input('keyword', null);
        $result = Dropdown::list($params, $keyword);
        return response()->json($result);
    }

    public function obj(Request $request){
        $result = Dropdown::obj((int)$request->input('id', null));
        return response()->json($result);
    }

    public function addItem(Request $request){
        $inputCheck = ValidateTools::validateData(
            $request->all(),
            Dropdown::$fieldDescriptions);
        if(!$inputCheck["success"]){
            return response()->json($inputCheck["data"]);
        }
        $result = Dropdown::addItem($inputCheck["data"]);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = $request->input('id', null);
        $inputCheck = ValidateTools::validateData(
            $request->all(),
            Dropdown::$fieldDescriptions);
        if(!$inputCheck["success"]){
            return response()->json($inputCheck["data"]);
        }
        $result = Dropdown::editItem($id, $inputCheck["data"]);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = $request->input('id', null);
        $result = Dropdown::removeItem($id);
        return response()->json($result);
    }
}