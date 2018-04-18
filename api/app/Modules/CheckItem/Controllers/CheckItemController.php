<?php

namespace App\Modules\CheckItem\Controllers;

use Validator;
use App\Modules\CheckItem\Models\CheckItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CheckItemController extends Controller{

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
        $result = CheckItem::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new CheckItem);
        $result = CheckItem::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), CheckItem::$fieldDescriptions
        );
        $result = CheckItem::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), CheckItem::$fieldDescriptions
        );
        $result = CheckItem::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = CheckItem::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $executor = property_exists($request, 'token')?$request->token->parent:null;
        $result = [
            'rate' => ($executor&&$executor->rate)?$executor->rate:intVal(\CheckItemDb::get('cny-vnd'))
        ];
        return response()->json(ResTools::obj($result));
    }
}