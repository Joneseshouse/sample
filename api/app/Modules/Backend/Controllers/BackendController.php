<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Tools;
use App\Helpers\ValidateTools;
use App\Modules\Admin\Models\Admin;
use App\Modules\Config\Models\Config;
use App\Modules\Permission\Models\Permission;
use App\Modules\RoleType\Models\RoleType;
use App\Modules\Role\Models\Role;
use App\Modules\Category\Models\Category;
use App\Modules\Banner\Models\Banner;
use App\Modules\Article\Models\Article;

class BackendController extends Controller{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

    public function login(Request $request){
        $data = [];
        $data["initData"] = Config::list();
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function profile(Request $request){
        $data = [];
        $data["initData"] = null;
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function admin(Request $request){
        $data = [];
        $data["initData"] = Admin::list();
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function config(Request $request){
        $data = [];
        $data["initData"] = Config::list();
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function permission(Request $request){
        $data = [];
        $data["initData"] = Permission::list();
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function roleType(Request $request){
        $data = [];
        $data["initData"] = RoleType::list();
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function role(Request $request, $id){
        $data = [];
        $data["initData"] = Role::list(['role_type_id' => $id]);
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function category(Request $request, $type=null){
        $data = [];
        if($type){
            $data["initData"] = Category::list(['type' => $type]);
        }else{
            $data["initData"] = Category::list();
        }
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function banner(Request $request, $category_id){
        $data = [];
        $data["initData"] = Banner::list(['category_id' => $category_id]);
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function article(Request $request, $category_id){
        $data = [];
        $data["initData"] = Article::list(['category_id' => $category_id]);
        return view('Backend::main', ValidateTools::toJson($data));
    }

    public function articleDetail(Request $request, $id){
        $data = [];
        $data["initData"] = Article::obj(intVal($id));
        return view('Backend::main', ValidateTools::toJson($data));
    }
}
