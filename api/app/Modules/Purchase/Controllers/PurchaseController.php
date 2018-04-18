<?php

namespace App\Modules\Purchase\Controllers;

use Validator;
use App\Modules\Purchase\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class PurchaseController extends Controller{

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
        $result = Purchase::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Purchase);
        $result = Purchase::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function check(Request $request){
        $params = [];
        $params['date_range'] = ValidateTools::getRequestValue($request, 'date_range', 'str', null);
        $params['order_uid'] = ValidateTools::getRequestValue($request, 'order_uid', 'str', null);
        $params['purchase_code'] = ValidateTools::getRequestValue($request, 'purchase_code', 'str', null);
        $params['dathang_filter_admin_id'] = ValidateTools::getRequestValue($request, 'dathang_filter_admin_id', 'int', 0);
        if($request->token->role_type === 'user'){
            $params['user_id'] = $request->token->parent->id;
        }else{
            if($request->token->role !== config('app.sadmin')){
                if($request->token->role === config('app.chamsoc')){
                    $params['chamsoc_admin_id'] = $request->token->parent->id;
                }else{
                    $params['dathang_admin_id'] = $request->token->parent->id;
                }
            }
        }
        $result = Purchase::check($params);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Purchase::$fieldDescriptions
        );
        $result = Purchase::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), Purchase::$fieldDescriptions
        );
        $result = Purchase::editItem(
            $id,
            $input['success']?$input['data']:$input,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Purchase::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function upload(Request $request){
        $listData = json_decode($request->input('listData', '[]'), true);
        $result = Purchase::upload($listData, $request->token->parent);
        return Tools::jsonResponse($result);
    }
}