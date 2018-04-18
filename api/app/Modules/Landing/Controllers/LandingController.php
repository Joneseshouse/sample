<?php
namespace App\Modules\Landing\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Tools;
use App\Helpers\ResTools;

use App\Modules\Landing\Models\Landing;
use App\Modules\Category\Models\Category;
use App\Modules\Config\Models\Config;
use App\Modules\Article\Models\Article;
use App\Modules\Banner\Models\Banner;
use App\Modules\Recruiment\Models\Recruiment;
use App\Modules\Enquiry\Models\Enquiry;
use App\Modules\User\Models\User;

Use App\Helpers\AccessTokenAuthentication;
Use App\Helpers\HTTPTranslator;

class LandingController extends Controller{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(){
        # parent::__construct();
    }

	public function sharedData(){
        $data = [];
        $data['listCategory'] = Category::with(['articles' => function($q){
            $q->orderBy('id', 'desc');
        }])->where(['type' => 'article', 'single' => false])->orderBy('id', 'desc')->get();
        $data['listOrderhang'] = Article::where('category_uid', 'order-hang')->orderBy('id', 'desc')->get();
        $data['listImpressedArticle'] = Article::where('home', true)->orderBy('id', 'desc')->get();
        $data['listBanner'] = Banner::where('category_uid', 'main-banner')->orderBy('id', 'asc')->get();
        return $data;
    }

    public function webmasterConfirm(Request $request){
        return "google-site-verification: googlecfb9267ccd245179.html";
    }

    public function index(Request $request){
        return "Home page";
        return redirect()->away('https://24horder.com/');
        $data = [];
        $data['activeMenu'] = 'home-menu';
        $data['title'] = Config::get('default-title');
        $data['description'] = Config::get('default-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data['listService'] = Article::where('category_uid', 'dich-vu')->take(4)->get();

        $data['listHomeArticle'] = Article::where('home', true)->take(6)->get();

        $data['homeTaobao'] = null;
        $homeTaobao = Article::where('category_uid', 'home')->first();
        if($homeTaobao){
            $data['homeTaobao'] = $homeTaobao->content;
        }

        $data = array_merge($data, $this->sharedData());
        return view('Landing::index', $data);
    }

    public function services(Request $request){
        $data = [];
        $data['activeMenu'] = 'services-menu';
        $data['title'] = 'Bảng giá dịch vụ';
        $data['description'] = Config::get('template-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data = array_merge($data, $this->sharedData());
        return view('Landing::services', $data);
    }

    public function commitments(Request $request){
        $data = [];
        $data['activeMenu'] = 'commitments-menu';
        $data['title'] = 'Cam kết';
        $data['description'] = Config::get('template-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data = array_merge($data, $this->sharedData());
        return view('Landing::commitments', $data);
    }

    public function register(Request $request){
        $data = [];
        $data['activeMenu'] = 'register-menu';
        $data['title'] = 'Đăng ký';
        $data['description'] = Config::get('contact-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data['status'] = 'new';
        $data['message'] = null;

        $data = array_merge($data, $this->sharedData());
        return view('Landing::register', $data);
    }

    public function itemReceiver(Request $request){
        $data = [];
        return view('Landing::itemReceiver', $data);
    }

    public function grabbingReceiver(Request $request){
        $data = [];
        return view('Landing::grabbingReceiver', $data);
    }

    public function cart(Request $request){
        $data = [];
        $data['activeMenu'] = 'register-menu';
        $data['title'] = 'Giỏ hàng';
        $data['description'] = Config::get('contact-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data['status'] = 'new';
        $data['message'] = null;

        $data = array_merge($data, $this->sharedData());
        return view('Landing::cart', $data);
    }

    public function signup(Request $request){
        $data = [];
        $data['activeMenu'] = 'register-menu';
        $data['title'] = 'Đăng ký';
        $data['description'] = Config::get('contact-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $inputData = [
            'first_name' => $request->input('first_name', null),
            'last_name' => $request->input('last_name', null),
            'email' => $request->input('email', null),
            'password' => $request->input('password', null),
            'phone' => $request->input('phone', null),
            'address' => $request->input('address', null),
            'company' => $request->input('company', null),
        ];
        # var_dump($inputData);
        if(!$inputData['first_name'] || !$inputData['last_name'] || !$inputData['email'] || !$inputData['password'] || !$inputData['phone'] || !$inputData['address']){
            # var_dump('case 1');die;
            $data['status'] = 'error';
            $data['message'] = 'Tạo tài khoản thất bại! Bạn vui lòng điền đầy đủ thông tin.';

            $data = array_merge($data, $this->sharedData());
            return view('Landing::register', $data);
        }else{
            if(User::where('email', $inputData['email'])->count()){
                # var_dump('case 2');die;
                $data['status'] = 'error';
                $data['message'] = 'Tạo tài khoản thất bại! Email của bạn đã được sử dụng trước đây.';

                $data = array_merge($data, $this->sharedData());
                return view('Landing::register', $data);
            }
        }
        $inputData['password'] = md5($inputData['password']);
        # $item = User::create($inputData);
        $item = User::addItem($inputData, $_FILES, null);

        if($item['success']){
            # var_dump('case 3');die;
            $data['status'] = 'success';
            $data['message'] = 'Tạo tài khoản thành công! Bạn vui lòng kiểm tra email để kích hoạt tài khoản.';
        }else{
            # var_dump('case 4');die;
            $data['status'] = 'error';
            $data['message'] = 'Tạo tài khoản thất bại! Bạn vui lòng điền đầy đủ thông tin.';
        }

        $data = array_merge($data, $this->sharedData());
        return view('Landing::register', $data);
    }

    public function rate(Request $request){
        $result = [
            'rate' => 20
        ];
        return ResTools::obj(Tools::getRate());
    }

    public function contact(Request $request){
        $data = [];
        $data['activeMenu'] = 'contact-menu';
        $data['title'] = 'Liên hệ';
        $data['description'] = Config::get('contact-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data = array_merge($data, $this->sharedData());
        return view('Landing::contact', $data);
    }

    public function contactConfirm(Request $request){
        $input = [
            'name' => $request->input("name"),
            'email' => $request->input("email"),
            'subject' => $request->input("subject"),
            'message' => $request->input("message")
        ];

        Enquiry::addItem($input);

        $data = [];
        $data['activeMenu'] = 'contact-menu';
        $data['title'] = 'Gửi thông tin liên hệ thành công';
        $data['description'] = Config::get('contact-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data = array_merge($data, $this->sharedData());
        return view('Landing::contactConfirm', $data);
    }

    public function list(Request $request, $category_uid){
        $data = [];
        $category = Category::where('uid', $category_uid)->first();

        if(!$category){
            abort(404);
            return;
        }
        $data['title'] = $category->title;

        $data['listItem'] = Article::where(['category_uid' => $category_uid])->paginate(15);

        $data['activeMenu'] = $category_uid."-menu";

        $data['description'] = Config::get('default-description');
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";

        $data = array_merge($data, $this->sharedData());
        return view('Landing::list', $data);
    }

    public function detail(Request $request, $id, $slug=''){
        $data = [];
        $item = Article::find($id);
        if($item){
            $data['title'] = $item->title;
            $data['item'] = $item;
            $data['description'] = $item->preview;
            $data['ogImage'] = config('app.media_url').$item->thumbnail;
        }else{
            abort(404);
            $data['title'] = Config::get('app.default_title');
            $data['item'] = null;
            $data['description'] = Config::get('default-description');
            $data['ogImage'] = config('app.static_url')."images/default-og.jpg";
        }
        $data['parentTitle'] = 'Tin tức / chia sẻ công nghệ';
        $data['activeMenu'] = $item->uid."-menu";

        $data = array_merge($data, $this->sharedData());
        return view('Landing::detail', $data);
    }

    public function testTranslate(Request $request){
        // echo Tools::translate('giày nữ');
        $data = [];

        $data['title'] = Config::get('app.default_title');
        $data['description'] = Config::get('default-description');
        $data['activeMenu'] = "translate-menu";
        $data['ogImage'] = config('app.static_url')."images/default-og.jpg";
        if($request->isMethod('get')){
            $data = array_merge($data, $this->sharedData());
            return view('Landing::testTranslate', $data);
        }
        $keyword = Tools::translate($request->input('keyword'));
        $keyword = urlencode($keyword);
        $targetUrl = "https://world.taobao.com/search/search.htm?q=$keyword&from=tbsearch&_input_charset=utf-8";
        return redirect($targetUrl);
    }
}
