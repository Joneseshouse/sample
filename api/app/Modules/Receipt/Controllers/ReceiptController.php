<?php

namespace App\Modules\Receipt\Controllers;

use Validator;
use App\Modules\Receipt\Models\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class ReceiptController extends Controller{

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
        $result = Receipt::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Receipt);

        $input[0]['admin_id'] = ValidateTools::getRequestValue($request, 'admin_id', 'int', null);
        $input[0]['user_id'] = ValidateTools::getRequestValue($request, 'user_id', 'int', null);
        $input[0]['money_type'] = ValidateTools::getRequestValue($request, 'money_type', 'str', null);
        $input[0]['have_transaction'] = ValidateTools::getRequestValue($request, 'have_transaction', 'str', null);
        $input[0]['uid'] = ValidateTools::getRequestValue($request, 'uid', 'str', null);
        $input[0]['note'] = ValidateTools::getRequestValue($request, 'note', 'str', null);
        $input[0]['from_amount'] = ValidateTools::getRequestValue($request, 'from_amount', 'int', null);
        $input[0]['to_amount'] = ValidateTools::getRequestValue($request, 'to_amount', 'int', null);
        $input[0]['date_range'] = ValidateTools::getRequestValue($request, 'date_range', 'str', null);

        $result = Receipt::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Receipt::$fieldDescriptions
        );
        $result = Receipt::addItem($input['success']?$input['data']:$input, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), Receipt::$fieldDescriptions
        );
        $result = Receipt::editItem($id, $input['success']?$input['data']:$input, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Receipt::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $executor = property_exists($request, 'token')?$request->token->parent:null;
        $result = [
            'rate' => ($executor&&$executor->rate)?$executor->rate:intVal(\ReceiptDb::get('cny-vnd'))
        ];
        return response()->json(ResTools::obj($result));
    }
}