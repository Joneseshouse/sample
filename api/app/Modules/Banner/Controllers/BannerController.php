<?php

namespace App\Modules\Banner\Controllers;

use Validator;
use App\Modules\Banner\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class BannerController extends Controller{

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
        $result = Banner::obj($id);
        return response()->json($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Banner);
        $result = Banner::list(...$input);
        return response()->json($result);
    }

    public function addItem(Request $request){

        $input = ValidateTools::validateData(
            $request->all(), Banner::$fieldDescriptions
        );
        $result = Banner::addItem($input['success']?$input['data']:$input, $_FILES);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $input = ValidateTools::validateData(
            $request->all(), Banner::$fieldDescriptions
        );
        $result = Banner::editItem($id, $input['success']?$input['data']:$input, $_FILES);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'str');
        $result = Banner::removeItem($id);
        return response()->json($result);
    }
}