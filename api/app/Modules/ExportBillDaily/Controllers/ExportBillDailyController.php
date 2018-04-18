<?php

namespace App\Modules\ExportBillDaily\Controllers;

use Validator;
use App\Modules\ExportBillDaily\Models\ExportBillDaily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class ExportBillDailyController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function list(Request $request){
        $input = ValidateTools::listInput($request->all(), new ExportBillDaily);
        $result = ExportBillDaily::list(...$input);
        return Tools::jsonResponse($result);
    }
}