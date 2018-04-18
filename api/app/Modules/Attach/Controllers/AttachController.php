<?php

namespace App\Modules\Attach\Controllers;

use Validator;
use App\Modules\Attach\Models\Attach;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class AttachController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function list(Request $request){
        $model = new Attach();
        $params = ValidateTools::getFetchParams($request->all(), $model->getFillable());
        $keyword = $params['keyword'];
        $orderBy = $params['orderBy'];
        $params = $params['params'];

        $result = Attach::list($params, $keyword, $orderBy);
        return response()->json($result);
    }

    public function obj(Request $request){
        $result = Attach::obj((int)$request->input('id', null));
        return response()->json($result);
    }

    public function addItem(Request $request){
        # var_dump($request->all());die;
        $inputCheck = ValidateTools::validateData(
            $request->all(),
            Attach::$fieldDescriptions);
        if(!$inputCheck["success"]){
            return response()->json($inputCheck["data"]);
        }
        # var_dump($inputCheck["data"]);die;
        $result = Attach::addItem($inputCheck["data"], $_FILES);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = $request->input('id', null);
        $inputCheck = ValidateTools::validateData(
            $request->all(),
            Attach::$fieldDescriptions);
        if(!$inputCheck["success"]){
            return response()->json($inputCheck["data"]);
        }
        $result = Attach::editItem($id, $inputCheck["data"], $_FILES);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = $request->input('id', null);
        $result = Attach::removeItem($id);
        return response()->json($result);
    }
}