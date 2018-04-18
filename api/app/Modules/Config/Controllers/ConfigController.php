<?php

namespace App\Modules\Config\Controllers;

use Validator;
use App\Modules\Config\Models\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class ConfigController extends Controller{
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
        $result = Config::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Config);
        $result = Config::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Config::$fieldDescriptions
        );
        $result = Config::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), Config::$fieldDescriptions
        );
        $result = Config::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = Config::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $executor = property_exists($request, 'token')?$request->token->parent:null;
        $result = [
            'rate' => ($executor&&$executor->rate)?$executor->rate:intVal(\ConfigDb::get('cny-vnd'))
        ];
        return response()->json(ResTools::obj($result));
    }
}