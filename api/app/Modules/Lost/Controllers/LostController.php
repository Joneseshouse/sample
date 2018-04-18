<?php

namespace App\Modules\Lost\Controllers;

use Validator;
use App\Modules\Lost\Models\Lost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class LostController extends Controller{

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
        $result = Lost::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Lost);
        $result = Lost::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Lost::$fieldDescriptions
        );
        $result = Lost::addItem($input['success']?$input['data']:$input, $request->token->parent);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), Lost::$fieldDescriptions
        );
        $result = Lost::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Lost::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(Lost::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}