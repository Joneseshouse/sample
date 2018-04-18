<?php

namespace App\Modules\BillOfLanding\Controllers;

use Validator;
use App\Modules\BillOfLanding\Models\BillOfLanding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class BillOfLandingController extends Controller{

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
        $result = BillOfLanding::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new BillOfLanding);
        if($request->token->role_type === 'user'){
            $input[0]['user_id'] = $request->token->parent->id;
            if($request->input('type', null) === 'missing'){
                unset($input[0]['user_id']);
            }
        }
        $landingStatusFilter = ValidateTools::toStr($request->input('landing_status_filter'));
        if($landingStatusFilter && $landingStatusFilter !== 'all'){
            $input[0]['landing_status_filter'] = $landingStatusFilter;
        }
        $woodenBoxFilter = ValidateTools::toStr($request->input('wooden_box_filter'));
        if($woodenBoxFilter && $woodenBoxFilter !== 'all'){
            $input[0]['wooden_box_filter'] = $woodenBoxFilter;
        }

        $input[0]['type'] = $request->input('type', null);
        $input[0]['date'] = $request->input('date', null);
        $input[0]['start_date'] = $request->input('start_date', null);
        $input[0]['end_date'] = $request->input('end_date', null);

        $userFilter = ValidateTools::toStr($request->input('user_filter'));
        if($userFilter && $userFilter !== 'all'){
            $input[0]['user_filter'] = $userFilter;
        }
        $result = BillOfLanding::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function listPure(Request $request){
        $input = ValidateTools::listInput($request->all(), new BillOfLanding);
        if(array_key_exists('address_uid', $request->all())){
            $input[0]['address_uid'] = $request->input('address_uid');
        }
        $result = BillOfLanding::listPure(...$input);
        return Tools::jsonResponse($result);
    }

    public function listCheckBill(Request $request){
        $input = ValidateTools::listInput($request->all(), new BillOfLanding);
        $result = BillOfLanding::listCheckBill(...$input);
        return Tools::jsonResponse($result);
    }

    public function checkDuplicateCode(Request $request){
        $code = $request->input('code', null);
        $result = BillOfLanding::checkDuplicateCode($code);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), BillOfLanding::$fieldDescriptions
        );
        $input['data']['rate'] = $request->token->parent->rate;
        if($request->token->role_type === 'user'){
            $user = $request->token->parent;
            $input['data']['user_id'] = $user->id;
        }
        if(!array_key_exists('sub_fee', $input['data'])){
            $input['data']['sub_fee'] = 0;
        }
        if(!array_key_exists('order_id', $input['data'])){
            $input['data']['order_id'] = null;
        }
        $input['data']['from_client'] = true;
        $result = BillOfLanding::addItem(
            $input['success']?$input['data']:$input,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), BillOfLanding::$fieldDescriptions
        );
        $input['data']['from_client'] = true;
        $result = BillOfLanding::editItem(
            $id,
            $input['success']?$input['data']:$input,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function resetComplain(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int', null);
        $result = BillOfLanding::resetComplain($id, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function editComplain(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int', null);
        $data = [
            'complain_amount' => ValidateTools::getRequestValue($request, 'complain_amount', 'int', null),
            'complain_type' => ValidateTools::getRequestValue($request, 'complain_type', 'str', null),
            'complain_resolve' => ValidateTools::getRequestValue($request, 'complain_resolve', 'bool', false),
            'complain_note_user' => ValidateTools::getRequestValue($request, 'complain_note_user', 'str', null),
            'complain_note_admin' => ValidateTools::getRequestValue($request, 'complain_note_admin', 'str', null)
        ];
        $result = BillOfLanding::editComplain($id, $data, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = BillOfLanding::removeItem($id, $request->token->parent);
        return Tools::jsonResponse($result);
    }
}