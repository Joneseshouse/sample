<?php

namespace App\Modules\CollectBol\Controllers;

use Validator;
use App\Modules\CollectBol\Models\CollectBol;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CollectBolController extends Controller{

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
        $result = CollectBol::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new CollectBol);
        $input[0]['start_date'] = $request->input('start_date', null);
        $input[0]['end_date'] = $request->input('end_date', null);

        if($request->token->role !== config('app.sadmin')){
            $input[0]['admin_id'] = $request->token->parent->id;
        }
        $result = CollectBol::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), CollectBol::$fieldDescriptions
        );
        $input['data']['admin_id'] = $request->token->parent->id;
        $input['data']['from_client'] = true;
        $result = CollectBol::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), CollectBol::$fieldDescriptions
        );
        $input['data']['from_client'] = true;
        $result = CollectBol::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = CollectBol::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function rate(Request $request){
        $result = [
            'rate' => intVal(CollectBol::get('cny-vnd', 3500))
        ];
        return response()->json(ResTools::obj($result));
    }
}