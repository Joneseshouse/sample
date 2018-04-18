<?php

namespace App\Modules\CnBillOfLandingFail\Controllers;

use Validator;
use App\Modules\CnBillOfLandingFail\Models\CnBillOfLandingFail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CnBillOfLandingFailController extends Controller{

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
        $result = CnBillOfLandingFail::obj($id);
        return Tools::jsonResponse($result);
    }

    public function objFilter(Request $request){
        $input = ValidateTools::listInput($request->all(), new CnBillOfLandingFail);
        $result = CnBillOfLandingFail::objFilter($input[0]);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new CnBillOfLandingFail);
        $result = CnBillOfLandingFail::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), CnBillOfLandingFail::$fieldDescriptions
        );
        $result = CnBillOfLandingFail::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $result = CnBillOfLandingFail::editItem(
            $id,
            $request->all(),
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = CnBillOfLandingFail::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(CnBillOfLandingFail::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}