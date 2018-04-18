<?php

namespace App\Modules\Address\Controllers;

use Validator;
use App\Modules\Address\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class AddressController extends Controller{

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
        $result = Address::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Address);
        if($request->token->role_type === 'user'){
            $input[0]['user_id'] = $request->token->parent->id;
        }
        $result = Address::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Address::$fieldDescriptions
        );
        if($input['success']){
            $input['data']['user_id'] = $request->token->parent->id;
        }
        $result = Address::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), Address::$fieldDescriptions
        );
        $result = Address::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Address::removeItem($id, $request->token->parent->id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(Address::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}