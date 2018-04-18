<?php

namespace App\Modules\OrderItemNote\Controllers;

use Validator;
use App\Modules\OrderItemNote\Models\OrderItemNote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class OrderItemNoteController extends Controller{

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
        $result = OrderItemNote::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new OrderItemNote);
        $result = OrderItemNote::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), OrderItemNote::$fieldDescriptions
        );
        if($request->token->role_type === 'user'){
            $input['data']['user_id'] = $request->token->parent->id;
        }else{
            $input['data']['admin_id'] = $request->token->parent->id;
        }
        $result = OrderItemNote::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), OrderItemNote::$fieldDescriptions
        );
        $result = OrderItemNote::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = OrderItemNote::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $executor = property_exists($request, 'token')?$request->token->parent:null;
        $result = [
            'rate' => ($executor&&$executor->rate)?$executor->rate:intVal(\OrderItemNoteDb::get('cny-vnd'))
        ];
        return response()->json(ResTools::obj($result));
    }
}