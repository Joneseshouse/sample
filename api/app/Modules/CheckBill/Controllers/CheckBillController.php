<?php

namespace App\Modules\CheckBill\Controllers;

use Validator;
use App\Modules\CheckBill\Models\CheckBill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CheckBillController extends Controller{

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
        $result = CheckBill::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new CheckBill);
        $result = CheckBill::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function checkFull(Request $request){
        $data = $request->input('data', '[]');
        $result = CheckBill::checkFull($data, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), CheckBill::$fieldDescriptions
        );
        $result = CheckBill::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), CheckBill::$fieldDescriptions
        );
        $result = CheckBill::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = CheckBill::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(CheckBill::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}