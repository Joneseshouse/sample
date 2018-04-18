<?php

namespace App\Modules\VnBillOfLanding\Controllers;

use Validator;
use App\Modules\VnBillOfLanding\Models\VnBillOfLanding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class VnBillOfLandingController extends Controller{

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
        $result = VnBillOfLanding::obj($id);
        return Tools::jsonResponse($result);
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new VnBillOfLanding);
        $input[0]['start_date'] = $request->input('start_date', null);
        $input[0]['end_date'] = $request->input('end_date', null);
        $result = VnBillOfLanding::list(...$input);
        return Tools::jsonResponse($result);
    }

    public function addItem(Request $request){
        $input = ValidateTools::validateData(
            $request->all(), VnBillOfLanding::$fieldDescriptions
        );
        $input['data']['admin_id'] = $request->token->parent->id;
        $result = VnBillOfLanding::addItem($input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function editItem(Request $request){
        $id = intval(Tools::getProp($request->all(), 'id'));
        $input = ValidateTools::validateData(
            $request->all(), VnBillOfLanding::$fieldDescriptions
        );
        $result = VnBillOfLanding::editItem($id, $input['success']?$input['data']:$input);
        return Tools::jsonResponse($result);
    }

    public function removeItem(Request $request){
        $id = (string)Tools::getProp($request->all(), 'id');
        $result = VnBillOfLanding::removeItem($id);
        return Tools::jsonResponse($result);
    }

    public static function upload(Request $request){
        $result = VnBillOfLanding::upload($request->token->parent->id, $_FILES);
        return Tools::jsonResponse($result);
    }
}
