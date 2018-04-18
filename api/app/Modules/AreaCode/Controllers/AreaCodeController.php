<?php

namespace App\Modules\AreaCode\Controllers;

use Validator;
use App\Modules\AreaCode\Models\AreaCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class AreaCodeController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function obj(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $result = AreaCode::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new AreaCode);
        $result = AreaCode::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), AreaCode::$fieldDescriptions
        );
        $result = AreaCode::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), AreaCode::$fieldDescriptions
        );
        $result = AreaCode::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = AreaCode::removeItem($id);
        return Tools::jsonResponse($result);
    }
}