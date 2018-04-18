<?php namespace App\Helpers;

use Mail;
use Request;
use Carbon\Carbon as Carbon;
use \Eventviva\ImageResize;
Use App\Helpers\AccessTokenAuthentication;
Use App\Helpers\HTTPTranslator;
Use App\Mail\CommonMail;
use App\Notifications\ErrorMessage;
use App\Helpers\SlackNotification;


class Tools {
    public static function move_uploaded_file($filename, $destination){
        return copy($filename, $destination);
    }

    public static function pre($input){
        echo '<pre>';
        print_r($input);
        echo '</pre>';
        die;
    }

    public static function consoleLog($input){
        $trace = debug_backtrace();
        $trace = $trace[0];
        $fileArr = explode('/', $trace['file']);
        $file = end($fileArr);
        $line = $trace['line'];
        $value = json_encode(print_r($input, true));
        $executeInfo = "\"$file: $line ->\"";
        try{
            $output = "<script>console.log($executeInfo, ".$value.")</script>";
            echo $output;
        }catch(\Error $e){
            $output = "<script>console.log($executeInfo, '".(string)$input."'')</script>";
            echo $output;
        }catch(\Exception $e){
            $output = "<script>console.log($executeInfo, '".(string)$input."'')</script>";
            echo $output;
        }
    }

    public static function nowDate(){
        return Carbon::today('Asia/Saigon');
    }

    public static function nowDateTime(){
        return Carbon::now('Asia/Saigon');
    }

    public static function yesterday(){
        return Carbon::yesterday('Asia/Saigon');
    }

    public static function parseRole($roles){
        /*
        'api/v1/address/list,api/v1/address/obj,api/v1/address/add,api/v1/address/edit,api/v1/address/remove,api/v1/article/list,api/v1/article/obj,api/v1/attach/list,api/v1/attach/obj,api/v1/attach/add,api/v1/bank/list,api/v1/bank/obj,api/v1/bank/add,api/v1/bank/edit,api/v1/bank/remove,api/v1/bill-of-landing/list,api/v1/bill-of-landing/obj,api/v1/bill-of-landing/add,api/v1/bill-of-landing/edit,api/v1/bill-of-landing/reset-complain,api/v1/bill-of-landing/edit-complain,api/v1/cart-item/list,api/v1/cart-item/obj,api/v1/cart-item/add,api/v1/cart-item/edit,api/v1/cart-item/remove,api/v1/chatlost/list,api/v1/chatlost/obj,api/v1/chatlost/add,api/v1/export-bill/list,api/v1/export-bill/obj,api/v1/lost/list,api/v1/lost/obj,api/v1/order/list,api/v1/order/obj,api/v1/order/add,api/v1/order/add-full,api/v1/order/edit,api/v1/order/draft-to-new,api/v1/order/remove,api/v1/order-item/list,api/v1/order-item/obj,api/v1/order-item/add,api/v1/order-item/edit,api/v1/order-item/remove,api/v1/purchase/list,api/v1/purchase/check,api/v1/purchase/obj,api/v1/user/list,api/v1/user/obj,api/v1/user/statistics,api/v1/user/change-password,api/v1/user/profile,api/v1/user/update-profile,api/v1/user/logout,api/v1/user-accounting/list,api/v1/user-accounting/obj,api/v1/user-transaction/list,api/v1/user-transaction/obj'
        */
        $result = [];
        $roleArr = explode(',', $roles);
        foreach ($roleArr as $roleItem) {
            $roleItem = explode('/', $roleItem);
            $length = count($roleItem);

            $lastPart = end($roleItem);
            $lastPartArr = explode('-', $lastPart);
            if($lastPart === 'list'){
                $result[] = $roleItem[$length - 2];
            }else if(end($lastPartArr) === 'list'){
                $result[] = $roleItem[$length - 2].'/'.$lastPartArr[0];
            }
        }
        return $result;
    }

    public static function dateForStatistics($dateType, $startDate=null, $endDate=null){
        $today = Tools::nowDate();
        switch($dateType){
            case 'today':
                $startDate = $endDate = $today;
            break;
            case 'this_week':
                $startDate = $today->startOfWeek();
                $endDate = Tools::nowDate();
            break;
            case 'last_7_days':
                $startDate = $today->subDay(7);
                $endDate = Tools::nowDate();
            break;
            case 'this_month':
                $startDate = $today->startOfMonth();
                $endDate = Tools::nowDate();
            break;
            case 'last_30_days':
                $startDate = $today->subDay(30);
                $endDate = Tools::nowDate();
            break;
            case 'custom':
                // Do nothing
            break;
            default:
                $startDate = $endDate = $today;
        }
        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }


    public static function randomStr($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    public static function getRate(){
        $result = [
            'rate' => 3.5
        ];
        $date = Carbon::now();
        $rateApi = 'https://www.techcombank.com.vn/customfield/findexchange?catId=234&date='.$date->format('d/m/Y');
        $rawHtml = file_get_contents($rateApi);
        $rawHtml = str_replace('</tr>', '', $rawHtml);
        $rawHtmlArr = explode('<tr>', $rawHtml);
        foreach ($rawHtmlArr as $paragraph) {
            if (strpos($paragraph, 'CNY') !== false) {
                $paragraph = str_replace('</strong>', '', $paragraph);
                $paragraphArr = explode('<strong>', $paragraph);
                $paragraphArr = explode(' ', $paragraphArr[count($paragraphArr) -1]);
                $rate = str_replace(',', '.', $paragraphArr[1]);
                $rate = floatval($rate);

                $result['rate'] = $rate;
                return $result;
            }
        }
        return $result;
    }

    public static function translate($inputStr, $from='vi', $to='zh_CN'){
        try {
            /*
            $outputStr = '';
            $inputStr = urlencode($inputStr);
            $authObj = new AccessTokenAuthentication();
            $accessToken = $authObj->getTokens(
                config('app.azure_app_grant_type'),
                config('app.azure_app_scope_url'),
                config('app.azure_app_client_id'),
                config('app.azure_app_client_secret'),
                config('app.azure_app_auth_url')
            );
            $authHeader = "Authorization: Bearer ". $accessToken;
            $translatorObj = new HTTPTranslator();
ấm trà
            $detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($inputStr);
            //Call the curlRequest.
            $strResponse = $translatorObj->curlRequest($detectMethodUrl, $authHeader);
            //Interprets a string of XML into an object.
            $xmlObj = simplexml_load_string($strResponse);
            foreach((array)$xmlObj[0] as $val){
                $from = $val;
            }

            $translateUrl = config('app.azure_app_translate_base_url')."$inputStr&from=$from&to=$to";
            $strResponse = $translatorObj->curlRequest($translateUrl, $authHeader);
            $xmlObj = simplexml_load_string($strResponse);
            foreach((array)$xmlObj[0] as $val){
                $outputStr = $val;
            }
            */

            $handle = curl_init();

            if (FALSE === $handle)
               throw new Exception('failed to initialize');
            curl_setopt($handle, CURLOPT_URL,'https://www.googleapis.com/language/translate/v2');
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_POSTFIELDS, array('key'=> 'AIzaSyAjrwmoxvDpz7gAp47hY7mu-t6Uxqy0aZQ', 'q' => $inputStr, 'source' => $from, 'target' => $to));
            curl_setopt($handle,CURLOPT_HTTPHEADER,array('X-HTTP-Method-Override: GET'));
            $response = json_decode(curl_exec($handle), true);

            if(isset($response['error'])){
                return $inputStr;
            }
            return $response['data']['translations'][0]['translatedText'];
        } catch (\Error $e) {
            return $inputStr;
        }
    }

    public static function getProp($input, string $key, $default=null){
        # TESTED
        $result = $default;
        if(gettype($input) === 'object'){
            if(property_exists($input, $key)){
                $result = $input->$key;
            }
        }else if(gettype($input) === 'array'){
            if(array_key_exists($key, $input)){
                $result = $input[$key];
            }
        }
        return $result;
    }

    public static function roundThousand($input){
        return round(($input)/1000)*1000;
    }

    public static function camelToWords(string $input, $upper=false):string {
        # TESTED
        try{
            $result = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $input);
            if($upper){
                return ucwords($result);
            }
            return $result;
        }catch(\Error $e){
            return $input;
        }
    }

    public static function getListRoute():array {
        $result = [];
        $routeCollection = \Route::getRoutes();
        foreach ($routeCollection as $item) {
            # echo \Route::getCurrentRoute()->getPath();
            # return;
            $path = $item->uri();
            $name = $item->getName();
            if($name){
                $nameRead = Tools::camelToWords($name, true);

                if($path !== '/' && in_array('token_required', $item->middleware())){
                    # Only get route that middle ware can check
                    $module = explode('/', $path)[2];
                    array_push($result,[
                        'route' => $path,
                        'module' => ucwords($module),
                        'label' => $nameRead,
                    ]);
                }
            }
        }
        return $result;
    }

    public static function getListKey(array $input):array {
        # TESTED
        $result = [];
        foreach ($input as $field => $type) {
            array_push($result, $field);
        }
        return $result;
    }

    public static function sendSlackMessage($exception){
        $errorMessage = $exception->getMessage();
        $errorLine = $exception->getLine();
        $errorFile = $exception->getFile();

        $input = null;
        try{
            $input = json_encode(Request::all());
        }catch(\Exception $e){
            $input = null;
        }catch(\Error $e){
            $input = null;
        }
        $message = [
            'message' => $errorMessage,
            'file' => $errorFile." [$errorLine]",
            'url' => Request::url(),
            'input' => $input
        ];
        $notification = new SlackNotification();
        $notification->notify(new ErrorMessage($message));
    }

    public static function errMessage($err){
        # var_dump($err);die;
        $errorMessage = $err->getMessage();
        $errorLine = $err->getLine();
        $errorFile = $err->getFile();
        $message = $errorMessage.' => '.$errorFile.' [line '.$errorLine.']';
        if(\Config::get('app.debug')){
            return $message;
        }
        Tools::sendSlackMessage($err);
        # self::sendErrorReport($message);
        return trans('messages.common_error');
    }

    public static function sendEmail($to, $subject, $template, $params=[], $attach=null){
        if(config('app.app_env') === 'testing'){
            return;
        }
        Mail::to($to)->send(new CommonMail($template, $subject, $params));
        /*
        if(config('app.debug')){
            Mail::to($to)->send(new CommonMail($template, $subject, $params));
        }else{
            Mail::to($to)->queue(new CommonMail($template, $subject, $params));
        }
        */
    }

    public static function sendErrorReport($errors){
        $subject = config('app.app_name').' - Error report';
        $params = [
            'errors' => print_r($errors, true)
        ];
        self::sendEmail(config('app.admin_email'), $subject, 'emails.errorReport', $params);
    }

    public static function niceUrl(string $str, $useHyphen=true):string{
        # TESTED
        if(!$str) return false;
        $result = str_slug($str);
        if(!$useHyphen){
            $result =  trim(str_replace("-", " ", $result));
        }
        return $result;
    }

    public static function getDomainFromUrl($url){
        // https://shop123159106.taobao.com -> shop123159106.taobao.com
        $urlArr = explode('://', $url);
        if(count($urlArr) > 1){
            $url = $urlArr[1];
        }
        $urlArr = explode('/', $url);
        if(count($urlArr) > 1){
            $url = $urlArr[0];
        }
        return $url;
    }

    public static function getFileExtension(string $fileName):string{
        # TESTED
        $extension = 'tmp';
        if(!$fileName || gettype($fileName) !== 'string'){
            return $extension;
        }
        $fileArr = explode('.', $fileName);
        if(count($fileArr) > 1){
            $extension = $fileArr[count($fileArr) - 1];
        }
        return $extension;
    }

    public static function getThumbnail(string $filePath):string{
        # TESTED
        if(!$filePath || gettype($filePath) !== 'string'){
            return '';
        }
        $fileArr = explode('.', $filePath);
        if(count($fileArr) > 1){
            $fileArr[count($fileArr)-2].='-thumbnail';
            return implode('.', $fileArr);
        }else{
            return $filePath.'-thumbnail';
        }
    }

    public static function carbonToYearMonthDay($input):int{
        return $input->year*10000 + $input->month*100 + $input->day;
    }

    public static function scaleImage($filePath, $newWidth=null, $thumbnail=false, $golden=false){
        # Only scale down
        $thumbnailWidth = \Config::get('upload.thumbnail_width');
        $maxWidth = \Config::get('upload.max_width');
        $goldenRatio = \Config::get('upload.golden_ratio');
        try{
            $imageInfo = getimagesize($filePath);
            $width = $imageInfo[0];
            $height = $imageInfo[1];
        }catch(\Error $e){
            $width = 250;
            $height = 250;
        }

        if($height){
            $ratio = $width/$height;
        }else{
            $ratio = 1;
        }

        if(!$newWidth || $newWidth > $maxWidth){
            $newWidth = $maxWidth;
        }

        $thumbnailPath = self::getThumbnail($filePath);
        $image = new ImageResize($filePath);
        $image->quality_jpg = 100;
        if($thumbnail){
            $image->crop($thumbnailWidth, $thumbnailWidth);
            $image->save($thumbnailPath);
        }

        # Only resize when actual width large than newWidth
        if($width > $newWidth){
            $image->resizeToWidth($newWidth);
            $image->save($filePath);
        }

        if($golden){
            $goldenWidth = $newWidth;
            $goldenHeight = $newWidth / $goldenRatio;
            if($goldenHeight > $height){
                # Image too short -> keep width then recalculate height
                $goldenHeight = $height;
                $goldenWidth = $goldenHeight * $goldenRatio;
            }
            $image->crop($goldenWidth, $goldenHeight);
            $image->save($filePath);
            $ratio = $goldenRatio;
        }
        return $ratio;
    }

    public static function uploadHandler($file, $existFile, $fieldName, $destination, $width=0, $thumbnail=false, $golden=false){
        /*
            Only allow image and some sort of file (pdf/documents/archives)
            return [
                'url' => 'media/somedir/abc.png',
                'type' => 'image' # file/image
            ];

            $_FILES = array(
                'thumbnail' => array(
                    'name' => 'test.jpg',
                    'type' => 'image/jpeg',
                    'size' => 542,
                    'tmp_name' => __DIR__ . '/_files/source-test.jpg',
                    'error' => 0
                )
            );
        */
        $result = [
            'path' => '',
            'type' => '',
            'ratio' => '',
            'success' => '',
            'blank' => false,
            'message' => '',
        ];
        if(array_key_exists($fieldName, $file)){
            $file = $file[$fieldName];
        }else{
            $result['success'] = false;
            $result['blank'] = true;
            $result['message'] = trans('messages.upload_fail');
            return $result;
        }
        $maxSize = \Config::get('upload.max_size');
        $allowImages = \Config::get('upload.allow_images');
        $allowPdf = \Config::get('upload.allow_pdf');
        $allowText = \Config::get('upload.allow_text');
        $allowDocuments = \Config::get('upload.allow_documents');
        $allowSpreadsheets = \Config::get('upload.allow_spreadsheets');
        $allowArchives = \Config::get('upload.allow_archives');
        $name = $file['name'];
        $extension = self::getFileExtension($name);
        $name = str_random(config('app.random_size')).'.'.$extension;

        $mime = $file['type'];
        $tmpName = $file['tmp_name'];
        $size = $file['size'];

        # Check file zie
        if($size > $maxSize){
            $result['success'] = false;
            $result['message'] = trans('messages.upload_file_too_large');
            return $result;
        }

        # Check file type
        if(in_array($mime, $allowImages)){
            # Image
            $type = 'image';
        }else if(in_array($mime, $allowPdf)){
            # File
            $type = 'pdf';
        }else if(in_array($mime, $allowText)){
            # File
            $type = 'text';
        }else if(in_array($mime, $allowDocuments)){
            # File
            $type = 'document';
        }else if(in_array($mime, $allowSpreadsheets)){
            # File
            $type = 'spreadsheet';
        }else if(in_array($mime, $allowArchives)){
            # File
            $type = 'archive';
        }else{
            # Not permit file
            $result['success'] = false;
            $result['message'] = trans('messages.upload_not_supported_file');
            return $result;
        }
        $destinationForSave = $destination.'/'.$name;
        $destination = \Config::get('app.media_root').$destination.'/';
        if(!file_exists($destination)){
            mkdir($destination, 0744 , true);
        }
        $destination .= $name;

        if (self::move_uploaded_file($tmpName, $destination)) {
            # Success
            if($type === 'image'){
                $result['ratio'] = self::scaleImage($destination, $width, $thumbnail, $golden);
            }
            $result['success'] = true;
            $result['path'] = $destinationForSave;
            $result['type'] = $type;
            $result['message'] = '';

            # Check previous file exist for removing
            if($existFile){
                $existFile = \Config::get('app.media_root').$existFile;
                $existThumbnail = self::getThumbnail($existFile);
                if(file_exists($existFile)){
                    unlink($existFile);
                }
                if(file_exists($existThumbnail)){
                    unlink($existThumbnail);
                }
            }
        } else {
            # Fail
            $result['success'] = false;
            $result['message'] = trans('messages.upload_fail');
        }
        return $result;
    }

    public static function prepareBase64ToFile(string $input):array {
        # TESTED
        try{
            $ext = explode(';', explode('image/', $input)[1])[0];
        }catch(\Error $e){
            $ext = 'jpg';
        }
        $data = explode(',', $input);
        $data = $data[count($data) - 1];
        return [
            'ext' => $ext,
            'data' => $data
        ];
    }

    public static function getImgUrlInfo(string $input):array {
        # TESTED
        $urlArr = explode('/', $input);
        $fileName = $urlArr[count($urlArr) - 1];
        $fileNameArr = explode('.', $fileName);
        if(count($fileNameArr) > 1){
            $ext = $fileNameArr[count($fileNameArr) - 1];
        }else{
            $ext = null;
            $fileName = null;
        }
        return [
            'ext' => $ext?$ext:null,
            'name' => $fileNameArr[0]?$fileNameArr[0]:null
        ];
    }

    public static function base64ToImg($input, $destination='', $fileName=null, $thumbnail=false, $golden=false){
        if($fileName === null){
            $fileName = str_random(config('app.random_size'));
        }
        $data = self::prepareBase64ToFile($input);
        $ext = $data['ext'];
        $data = $data['data'];
        $filePath = \Config::get('app.media_root').$destination.'/'.$fileName.'.'.$ext;
        if(!file_exists(\Config::get('app.media_root').$destination)){
            mkdir(\Config::get('app.media_root').$destination, 0744 , true);
        }
        try{
            file_put_contents($filePath, base64_decode($data));
            $ratio = self::scaleImage($filePath, 0, $thumbnail, $golden);
            return [
                'path' => $destination.'/'.$fileName.'.'.$ext,
                'ratio' => $ratio
            ];
        }catch(\Error $e){
            return [
                'path' => null,
                'ratio' => 0
            ];
        }
    }

    public static function urlToImg($input, $destination='', $fileName=null, $thumbnail=false, $golden=false){
        if(strpos($input, '//') === 0){
            $input = config('app.protocol').':'.$input;
        }
        $imgUrlInfo = self::getImgUrlInfo($input);
        if($imgUrlInfo['name'] === null){
            # Url that no in form .../abc/my-image.jpg -> .../abc/def
            # Can not get extension
            if($fileName === null){
                $fileName = str_random(config('app.random_size')).'.jpg';
            }else{
                $fileName .= '.jpg';
            }
        }else{
            if($fileName === null){
                $fileName = str_random(config('app.random_size')).'.'.$imgUrlInfo['ext'];
            }else{
                $fileName .= $imgUrlInfo['ext'];
            }
        }
        $filePath = \Config::get('app.media_root').$destination.'/'.$fileName;
        if(!file_exists(\Config::get('app.media_root').$destination)){
            mkdir(\Config::get('app.media_root').$destination, 0744 , true);
        }
        try{
            $data = file_get_contents($input);
            file_put_contents($filePath, $data);
            $ratio = self::scaleImage($filePath, 0, $thumbnail, $golden);
            return [
                'path' => $destination.'/'.$fileName,
                'ratio' => $ratio
            ];
        }catch(\Error $e){
            return [
                'path' => null,
                'ratio' => 0
            ];
        }
    }

    public static function firstImgFromHtml($input, $destination=''){
        # <strong>abcdef</strong> <img src="https://abc.com/page.png"/> ...
        $inputArr = explode('<img', $input);
        if(count($inputArr) === 1){
            return [
                'path' => null,
                'ratio' => 0
            ];
        }
        $input = $inputArr[1];
        if(strpos($input, 'src="') !== false){
            $beginDelimiter = 'src="';
            $endDelimiter = '"';
        }elseif(strpos($input, "src='") !== false){
            $beginDelimiter = "src='";
            $endDelimiter = "'";
        }else{
            return [
                'path' => null,
                'ratio' => 0
            ];
        }
        $input = explode($beginDelimiter, $input)[1];
        $input = explode($endDelimiter, $input)[0];
        # var_dump($input);die;
        if(count(explode('data:image', $input)) > 1){
            # Base64
            return self::base64ToImg($input, $destination);
        }
        # Check the url is base64 or url
        return self::urlToImg($input, $destination);
    }

    public static function getPreview($input){
        $input = strip_tags($input);
        $input = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $input);
        $input = substr($input, 0, \Config::get('app.preview_length'));
        $inputArr = explode(' ', $input);
        array_pop($inputArr);
        $input = trim(implode(' ', $inputArr));
        return $input;
        /*
        # $input = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $input);
        $input = strip_tags($input, "<div><span><p><a><strong>");
        $input = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $input);
        $input = substr($input, 0, \Config::get('app.preview_length'));
        $inputArr = explode(' ', $input);
        array_pop($inputArr);
        $input = trim(implode(' ', $inputArr));
        # var_dump($input);die;
        # return \Config::get('app.preview_length');
        $tidy = new \tidy;
        $input = $tidy->repairString($input);
        $input = explode('</body>', explode('<body>', $input)[1])[0];
        return $input;
        */
    }

    public static function parseOrderBy($input){
        // -ascii_title, ascii_title
        $order = 'desc';
        $field = 'id';
        if(!$input){
            return [$field, $order];
        }
        if(in_array($input[0], ['-', '+'])){
            if($input[0] === '-'){
                $order = 'desc';
            }else{
                $order = 'asc';
            }
            $field = substr($input, 1);
        }else{
            $order = 'asc';
            $field = $input;
        }
        return [$field, $order];
    }

    public static function getKeyValuePair($key, $input){
        if(!array_key_exists($key, $input)){
            return ['default' => null];
        }
        $result = [];
        $result[$key] = $input[$key];
        return $result;
    }

    public static function ignoreKeys($input, $keys=[]){
        $result = [];
        foreach ($input as $key => $value) {
            if(!in_array($key, $keys)){
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function jsonResponse($data){
        if(config('app.app_env') === 'testing'){
            return $data;
        }
        return response()->json($data);
    }

    public static function monthToChar(int $month):string {
        $ref = [
            1 => 'a',
            2 => 'b',
            3 => 'c',
            4 => 'd',
            5 => 'e',
            6 => 'f',
            7 => 'g',
            8 => 'h',
            9 => 'i',
            10 => 'j',
            11 => 'k',
            12 => 'l',
        ];
        return strtoupper($ref[$month]);
    }

    public static function removeNullKeys($input){
        $result = [];
        foreach ($input as $key => $value) {
            if($value !== null){
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function isInt($input){
        $result = filter_var($input, FILTER_VALIDATE_INT);
        if(gettype($result) === 'boolean' && !$result){
            return false;
        }
        return true;
    }

    public static function isFloat($input){
        $result = filter_var($input, FILTER_VALIDATE_INT);
        if(gettype($result) === 'boolean' && !$result){
            return false;
        }
        return true;
    }

    public static function isNumber($input){
        $result = filter_var($input, FILTER_VALIDATE_INT);
        if(gettype($result) === 'boolean' && !$result){
            return false;
        }
        return true;
    }

    public static function getVendorFromUrl($url){
        if(preg_match('/taobao.com/', $url)){
            return "TAOBAO";
        }
        if(preg_match('/tmall.com|tmall.hk|yao.95095.com/', $url)){
            return "TMALL";
        }
        if(preg_match('/1688.com|alibaba/', $url)){
            return "1688";
        }
        return null;
    }
}
