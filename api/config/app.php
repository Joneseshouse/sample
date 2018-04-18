<?php
$listCheckItemStatus = [
    ['id' => '', 'title' => '--- Chọn trạng thái ---'],
    ['id' => 'ok', 'title' => 'Đã kiểm đủ'],
    ['id' => 'missing', 'title' => 'Thiếu số lượng'],
    ['id' => 'wrong_item', 'title' => 'Nhầm mẫu'],
    ['id' => 'wrong_color', 'title' => 'Nhầm màu'],
    ['id' => 'wrong_size', 'title' => 'Nhầm size']
];
$listCheckItemStatusRef = [];
foreach ($listCheckItemStatus as $checkItemStatus) {
    $listCheckItemStatusRef[$checkItemStatus['id']] = $checkItemStatus['id']?$checkItemStatus['title']:'';
}
return [
    'admin_email' => 'tbson87@gmail.com',
    'SLACK_WEBHOOK_URL' => env('SLACK_WEBHOOK_URL', null),
    'app_name' => env('APP_NAME', 'RIASoft'),
    'app_env' => env('APP_ENV', 'local'),
    'testing' => env('TESTING', false),
    'protocol' => explode('://', env('APP_URL'))[0],
    'random_size' => 24,
    'max_address' => 9,
    'max_fail' => 5, # Number of fails to block login
    'max_fail_record' => 100000, # Number of record can save to db
    'waiting_on_max_fail' => 1, # Number minutes that user must to waiting
    'page_size' => env('APP_ENV', 'local') === 'testing' ? 2 : 25, # 25 rows per page
    'page_size_test' => 5, # 12 rows per page
    'token_life' => 1440, # One day
    'password_token_life' => 30, # Haft an hour (in minute)
    'preview_length' => 300,
    'admin_roles' => ['admin', 'sadmin'],
    'user_roles' => ['user', 'suser'],
    'from_email'  => env('MAIL_USERNAME', 'info@project.com'),
    'base_url' => env('APP_URL', '').'/',
    'static_url' => env('APP_URL', '').'/public/static/',
    'media_url' => env('APP_URL', '').'/public/media/',
    'client_url' => env('APP_URL', '').'/public/clients/source/app.js',
    'client_summernote_url' => env('APP_URL', '').'/public/clients/source/summernote-vendor.js',
    'client_user_url' => env('APP_URL', '').'/user/',
    'client_admin_url' => env('APP_URL', '').'/admin/',
    'base_dir' => base_path(),
    'static_root' => base_path().'/public/static/',
    'media_root' => base_path().'/public/media/',
    'extension_url' => 'https://chrome.google.com/webstore/detail/order-t%C3%A0u-nhanh/pdjhnjdjjfhkdlnholngkglholhmholc?hl=en-US&gl=VN',
    'extension_url_grab' => 'https://chrome.google.com/webstore/detail/rdathang-grabbing/pnkcjmkfjpnaboaecahfkkeelebahlbl?hl=en-US',
    'azure_app_client_id' => env('AZURE_APP_CLIENT_ID', ''),
    'azure_app_client_secret' => env('AZURE_APP_CLIENT_SECRET', ''),
    'azure_app_auth_url' => env('AZURE_APP_AUTH_URL', ''),
    'azure_app_scope_url' => env('AZURE_APP_SCOPE_URL', ''),
    'azure_app_grant_type' => env('AZURE_APP_GRANT_TYPE', ''),
    'azure_app_translate_base_url' => env('AZURE_APP_TRANSLATE_BASE_URL', ''),
    'vnd' => '₫',
    'cny' => '¥',
    'sadmin' => 'quan-tri-vien',
    'quanly' => 'quan-ly-dat-hang',
    'chamsoc' => 'cham-soc-khach-hang',
    'dathang' => 'nhan-vien-dat-hang',
    'ketoan' => 'ke-toan',
    'ketoantong' => 'ke-toan-tong',
    # Đang giao dịch: purchasing
    'list_user_transaction_type' => [
        [
            'id' => 'NT',
            'title' => 'NT: Nạp tiền'
        ],
        [
            'id' => 'CK',
            'title' => 'CK: Tiền chiết khấu'
        ],
        [
            'id' => 'KN',
            'title' => 'KN: Trả tiền khiếu nại'
        ],
        [
            'id' => 'RT',
            'title' => 'RT: Rút tiền'
        ],
        [
            'id' => 'GD',
            'title' => 'GD: Tiền hàng của shop khi duyệt mua'
        ],
        [
            'id' => 'VC',
            'title' => 'VC: Tiền vận chuyển'
        ],
        [
            'id' => 'TH',
            'title' => 'TH: Tiền hàng của khi xuất hết shop'
        ],
        [
            'id' => 'XH',
            'title' => 'XH: Xuất hàng tính phí VC'
        ]
    ],
    'list_admin_transaction_type' => [
        [
            'id' => 'NT',
            'title' => 'NT: Nạp tiền'
        ],
        [
            'id' => 'RT',
            'title' => 'RT: Rút tiền'
        ],
    ],

    'list_order_status' => [
        'new',
        'confirm',
        'purchasing',
        'purchased',
        'complain',
        'done'
    ],
    'list_order_status_ref' => [
        'new' => 'Chờ duyệt',
        'confirm' => 'Đã duyệt mua',
        'purchasing' => 'Đang g.dịch',
        'purchased' => 'G.dịch xong',
        'complain' => 'K.nại',
        'done' => 'Hoàn thành'
    ],
    'list_check_item_status' => $listCheckItemStatus,
    'list_check_item_status_ref' => $listCheckItemStatusRef,
    'default_transform_factor' => 6000,
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => 'Laravel',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'vi',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'single'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        //

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        'Folklore\GraphQL\ServiceProvider',
        'App\Modules\ServiceProvider',
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'PDF'       => Nitmedia\Wkhtml2pdf\Facades\Wkhtml2pdf::class,
        'ConfigDb'  => App\Modules\Config\Models\Config::class,
        'Excel'     => Maatwebsite\Excel\Facades\Excel::class,
        'GraphQL' => 'Folklore\GraphQL\Support\Facades\GraphQL',
    ],

];
