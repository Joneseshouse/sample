<?php

namespace App\Modules\ExportBill\Controllers;

use Validator;
use App\Modules\ExportBill\Models\ExportBill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class ExportBillController extends Controller{

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
        $result = ExportBill::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $params = [];
        if($request->token->role_type === 'user'){
            $params['user_id'] = $request->token->parent->id;
        }
        $params['date_range'] = ValidateTools::getRequestValue($request, 'date_range', 'str', null);
        $params['uid'] = ValidateTools::getRequestValue($request, 'uid', 'str', null);
        $params['address_code'] = ValidateTools::getRequestValue($request, 'address_code', 'str', null);
        $params['admin_id'] = ValidateTools::getRequestValue($request, 'admin_id', 'int', 0);

        $result = ExportBill::list($params);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), ExportBill::$fieldDescriptions
        );
        $input['data']['admin_id'] = $request->token->parent->id;
        $result = ExportBill::addItem($input['success']?$input['data']:$input, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), ExportBill::$fieldDescriptions
        );
        $result = ExportBill::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editContact(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $contact_id = intval(Tools::getProp($request->all(), 'contact_id'));
        $result = ExportBill::editContact($id, $contact_id);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = ExportBill::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(ExportBill::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}