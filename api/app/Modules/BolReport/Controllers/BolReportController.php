<?php

namespace App\Modules\BolReport\Controllers;

use Validator;
use App\Modules\BolReport\Models\BolReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResTools;
use App\Helpers\ValidateTools;
use App\Helpers\Tools;

class BolReportController extends Controller{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function list(Request $request){
        $result = BolReport::list($request->all());
        return Tools::jsonResponse($result);
    }
}