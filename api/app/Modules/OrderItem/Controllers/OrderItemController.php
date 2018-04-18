<?php

namespace App\Modules\OrderItem\Controllers;

use Validator;
use App\Modules\OrderItem\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class OrderItemController extends Controller{

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
        $result = OrderItem::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new OrderItem);
        $result = OrderItem::list(...$input);
        return Tools::jsonResponse($result);
    }

    public static function addCart(Request $request){
        $listItem = json_decode($request->input('data', '[]'), true);
        $result = OrderItem::addCart($listItem);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), OrderItem::$fieldDescriptions
        );
        $result = OrderItem::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), OrderItem::$fieldDescriptions
        );
        $result = OrderItem::editItem(
            $id,
            $input['success']?$input['data']:$input,
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function editUnitPrice(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $unitPrice = floatval(Tools::getProp($request->all(), 'unit_price'));

        $result = OrderItem::editItem(
            $id,
            ['unit_price' => $unitPrice],
            $request->token->parent
        );
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = OrderItem::removeItem($id, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function empty(Request $request){
        $orderId = intval(Tools::getProp($request->all(), 'order_id'));
        $result = OrderItem::empty($orderId, $request->token->parent);
        return Tools::jsonResponse($result);
    }
}