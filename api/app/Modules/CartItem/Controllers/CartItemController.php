<?php

namespace App\Modules\CartItem\Controllers;

use Validator;
use App\Modules\CartItem\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CartItemController extends Controller{

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
        $result = CartItem::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new CartItem);
        $input[0]['user_id'] = $request->token->parent->id;
        $input[0]['date_range'] = ValidateTools::getRequestValue($request, 'date_range', 'str', null);
        $input[0]['link'] = ValidateTools::getRequestValue($request, 'link', 'str', null);
        $input[0]['shop'] = ValidateTools::getRequestValue($request, 'shop', 'str', null);

        $result = CartItem::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $listItem = json_decode($request->input('data', '[]'), true);
        $result = CartItem::addItem($listItem, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), CartItem::$fieldDescriptions
        );
        $result = CartItem::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = CartItem::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $executor = property_exists($request, 'token')?$request->token->parent:null;
        $result = [
            'rate' => ($executor&&$executor->rate)?$executor->rate:intVal(\CartItemDb::get('cny-vnd'))
        ];
        return response()->json(ResTools::obj($result));
    }
}