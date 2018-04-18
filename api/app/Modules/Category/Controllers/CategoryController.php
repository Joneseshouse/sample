<?php

namespace App\Modules\Category\Controllers;

use Validator;
use App\Modules\Category\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class CategoryController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function obj(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $result = Category::obj($id);
        return response()->json($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new Category);
        $result = Category::list(...$input);
        return response()->json($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), Category::$fieldDescriptions
        );
        $result = Category::addItem($input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function editItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'int');
        $input = ValidateTools::validateData(
            $request->all(), Category::$fieldDescriptions
        );
        $result = Category::editItem($id, $input['success']?$input['data']:$input);
        return response()->json($result);
    }

    public function removeItem(Request $request){
        $id = ValidateTools::getRequestValue($request, 'id', 'str');
        $result = Category::removeItem($id);
        return response()->json($result);
    }
}