<?php

namespace App\Modules\Order\Controllers;

use Validator;
use App\Modules\Order\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class OrderController extends Controller{

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
        $result = Order::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        # $input = ValidateTools::listInput($request->all(), new Order);
        $input = ValidateTools::listInput($request->all());
        if($request->token->role_type === 'user'){
            $input[0]['user_id'] = $request->token->parent->id;
        }else{
            # if($request->token->role !== config('app.sadmin')){
                if($request->token->role === config('app.chamsoc')){
                    $input[0]['chamsoc_admin_id'] = $request->token->parent->id;
                }else if($request->token->role === config('app.dathang')){
                    $input[0]['dathang_admin_id'] = $request->token->parent->id;
                }
            # }
        }
        $result = Order::list(...$input);
        return Tools::jsonResponse($result);
    }

    public static function uploadCart(Request $request){
        $executor = property_exists($request, 'token')?$request->token->parent:null;
        $result = Order::uploadCart(
            $_FILES,
            $executor
        );
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Order::$fieldDescriptions
        );
        if($input['success']){
            $input['data']['user_id'] = $request->token->parent->id;
            $input['data']['uid'] = 'default';
            $input['data']['order'] = 0;
        }
        $result = Order::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function addItemFull(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Order::$fieldDescriptions
        );
        // Check list item
        $listOrderItem = json_decode($request->input('listOrderItem'), true);
        $draft = ValidateTools::getRequestValue($request, 'draft', 'bool', false);
        $result = Order::addItemFull($listOrderItem, $draft, $request->token->parent->id);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), Order::$fieldDescriptions
        );
        $result = Order::editItem(
            $id,
            $input['success']?$input['data']:$input,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function updateDeliveryFeeUnit(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $result = Order::updateDeliveryFeeUnit($id, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function draftToNew(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $result = Order::draftToNew($id, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function massConfirm(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Order::massConfirm(
            $id,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Order::removeItem(
            $id,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function download(Request $request, $id, $uid){
        Order::download($id, $uid);
    }
}