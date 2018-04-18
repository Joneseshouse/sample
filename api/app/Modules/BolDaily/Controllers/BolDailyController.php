<?php

namespace App\Modules\BolDaily\Controllers;

use Validator;
use App\Modules\BolDaily\Models\BolDaily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class BolDailyController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new BolDaily);
        $input[0]['date_range'] = ValidateTools::getRequestValue($request, 'date_range', 'str', null);
        $input[0]['last_updated'] = ValidateTools::getRequestValue($request, 'last_updated', 'date', null);
        $input[0]['bol'] = ValidateTools::getRequestValue($request, 'bol', 'str', null);
        $result = BolDaily::list(...$input);
        return Tools::jsonResponse($result);
    }
}