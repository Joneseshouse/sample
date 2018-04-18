<?php

namespace App\Modules\ChatLost\Controllers;

use Validator;
use App\Modules\ChatLost\Models\ChatLost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class ChatLostController extends Controller{

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
        $result = ChatLost::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new ChatLost);
        $result = ChatLost::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), ChatLost::$fieldDescriptions
        );
        $token = explode(" ", $request->header('Authorization'))[1];
        
        $result = ChatLost::addItem($input['success']?$input['data']:$input, $token);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), ChatLost::$fieldDescriptions
        );
        $result = ChatLost::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = ChatLost::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(ChatLost::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}