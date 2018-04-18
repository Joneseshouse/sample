<?php

namespace App\Modules\CnBillOfLanding\Controllers;

use Validator;
use App\Modules\CnBillOfLanding\Models\CnBillOfLanding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CnBillOfLandingController extends Controller{

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
        $result = CnBillOfLanding::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new CnBillOfLanding);
        $input[0]['start_date'] = $request->input('start_date', null);
        $input[0]['end_date'] = $request->input('end_date', null);
        $result = CnBillOfLanding::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), CnBillOfLanding::$fieldDescriptions
        );
        $input['data']['address_uid'] = ValidateTools::getRequestValue($request, 'address_uid', 'str', null);
        $input['data']['admin_id'] = $request->token->parent->id;
        $result = CnBillOfLanding::addItem($input['success']?$input['data']:$input, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), CnBillOfLanding::$fieldDescriptions
        );
        $result = CnBillOfLanding::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = CnBillOfLanding::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function upload(Request $request){
        $result = CnBillOfLanding::upload(
            $_FILES,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }
}
