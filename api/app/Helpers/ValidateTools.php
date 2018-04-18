<?php namespace App\Helpers;

use Validator;
use Carbon\Carbon;
use App\Helpers\Tools;
use App\Helpers\ResTools;


class ValidateTools {

    public static function toJson($data){
        foreach ($data as $key => $dataItem) {
            try{
                if($data[$key]){
                    $data[$key] = json_encode($dataItem);
                }else{
                    $data[$key] = '""';
                }
            }catch(\Error $e){
                $data[$key] = '""';
            }
        }
        return $data;
    }

    public static function toStr($input, $default='') {
        try{
            if(gettype($input) === 'string'){
                $input = trim($input, '"');
                $input = trim($input, "'");
                if(in_array($input, ['null', 'Null', 'NULL'])){
                    $input = '';
                }
            }else if(in_array(gettype($input), ['resource', 'object', 'array'])){
                $input = '';
            }
            return stripslashes($input);
        }catch(\Error $e){
            return $default;
        }
    }

    public static function toBool($input, $default=false) {
        try{
            $result = true;
            if(gettype($input) === 'string'){
                $input = trim($input, '"');
                $input = trim($input, "'");
                if(in_array($input, ['null', 'Null', 'NULL', '', 'false', 'False', 'FALSE'])){
                    $result = false;
                }
            }else if(in_array(gettype($input), ['resource', 'object', 'array'])){
                if(gettype($input) === 'array'){
                    if(!count($input)){
                        $result = false;
                    }
                }else{
                    $result = true;
                }
            }else{
                if(!$input){
                    $result = false;
                }
            }
            return $result;
        }catch(\Error $e){
            return $default;
        }
    }

    public static function toInt($input, $default=0) {
        try{
            if(gettype($input) === 'string'){
                $input = trim($input, '"');
                $input = trim($input, "'");
                if(in_array($input, ['null', 'Null', 'NULL', '', 'false', 'False', 'FALSE'])){
                    $input = 0;
                }
            }else if(in_array(gettype($input), ['resource', 'object', 'array'])){
                $input = 0;
            }else{
                if(!$input){
                    $input = 0;
                }
            }
            $result = intval($input);
            return $result;
        }catch(\Error $e){
            return $default;
        }
    }

    public static function toFloat($input, $default=0.0) {
        try{
            if(gettype($input) === 'string'){
                $input = trim($input, '"');
                $input = trim($input, "'");
                if(in_array($input, ['null', 'Null', 'NULL', '', 'false', 'False', 'FALSE'])){
                    $input = 0.0;
                }
            }else if(in_array(gettype($input), ['resource', 'object', 'array'])){
                $input = 0.0;
            }else{
                if(!$input){
                    $input = 0.0;
                }
            }
            $result = floatVal($input);
            return $result;
        }catch(\Error $e){
            return $default;
        }
    }

    public static function toDate($input, $format = 'yearMonthDay') {
        # echo Carbon::createFromFormat('Y-m-d H', '1975-05-21 22')->toDateTimeString();
        if(!$input){
            return null;
        }
        try{
            if(gettype($input) === 'string'){
                $input = trim($input, '"');
                $input = trim($input, "'");
                if(in_array($input, ['null', 'Null', 'NULL', '', 'false', 'False', 'FALSE'])){
                    return null;
                }
                if($format === 'yearMonthDay'){
                    $result = Carbon::parse($input)->timezone('Asia/Saigon');
                    $result->subHours(7);
                    return $result;
                    # return Carbon::parse($input)->timezone('Asia/Saigon');
                }
                $input = explode(' ', $input)[0];
                $result = Carbon::createFromFormat($format, $input)->timezone('Asia/Saigon');
                $result->subHours(7);
                return $result;
                # return Carbon::createFromFormat($format, $input)->timezone('Asia/Saigon');
            }else{
                return null;
            }
        }catch(\Error $e){
            return null;
        }
    }

    public static function validateInput($input, $dataRules, $acceptedFields=[], $excludedFields=[]){
        $result = [];
        $compactResult = [];
        foreach ($input as $field => $value) {
            foreach ($dataRules as $key => $type) {
                if($key === $field){
                    switch($type){
                        case 'str':
                            $result[$field] = self::toStr($value);
                        break;
                        case 'int':
                            $result[$field] = self::toInt($value);
                        break;
                        case 'float':
                            $result[$field] = self::toFloat($value);
                        break;
                        case 'bool':
                            $result[$field] = self::toBool($value);
                        break;
                        case 'date':
                            $result[$field] = self::toDate($value);
                        break;
                        default:
                            $result[$field] = self::toStr($value);
                    }
                }
            }
        }
        if(count($acceptedFields) === 0){
            foreach ($result as $key => $value) {
                if(!in_array($key, $excludedFields)){
                    $compactResult[$field] = $value;
                }
            }
            return $compactResult;
        }
        foreach ($result as $key => $value) {
            foreach ($acceptedFields as $field) {
                if($field === $key && !in_array($field, $excludedFields)){
                    $compactResult[$field] = $value;
                }
            }
        }
        return $compactResult;
    }

    public static function parseRules(array $fieldDescriptions){
        $result = [
            "rules" => [],
            "dataRules" => []
        ];
        foreach ($fieldDescriptions as $field => $rule) {
            $ruleArr = explode(",", $rule);
            $result["dataRules"][$field] = $ruleArr[0];
            if(count($ruleArr) === 2){
                $result["rules"][$field] = $ruleArr[1];
            }
        }
        return $result;
    }

    public static function getRules($rules, $listKey=[]){
        $resultKey = [];
        if(count($listKey)){
            foreach ($listKey as $key) {
                if(array_key_exists($key, $rules)){
                    $resultKey[$key] = $rules[$key];
                }
            }
        }
        if(count($resultKey)){
            return $resultKey;
        }
        return $rules;
    }

    public static function checkRules($input, $fieldDescriptions, $onlyFields=[], $excludedFields=[]){
        $rules = self::parseRules($fieldDescriptions)["rules"];
        $dataRules = self::parseRules($fieldDescriptions)["dataRules"];
        $finalRules = [];
        $fields = Tools::getListKey($dataRules);
        if(count($onlyFields)){
            $fields = $onlyFields;
        }
        foreach ($fields as $field) {
            if(!in_array($field, $excludedFields)){
                array_push($finalRules, $field);
            }
        }
        $validator = Validator::make($input, self::getRules($rules, $finalRules));
        if ($validator->fails()) {
            return ResTools::err(
                $validator->errors(),
                ResTools::$ERROR_CODES['INTERNAL_SERVER_ERROR']
            );
        }
        return false;
    }

    public static function validateData($input, $fieldDescriptions, $onlyFields=[], $excludedFields=[]){
        $dataRules = self::parseRules($fieldDescriptions)["dataRules"];
        $result = ["success" => true, "data" => null];
        $errorsCheck = self::checkRules($input, $fieldDescriptions, $onlyFields, $excludedFields);
        if($errorsCheck){
            $result = $errorsCheck;
        }else{
            $result["success"] = true;
            $result["data"] = self::validateInput(
                $input,
                $dataRules,
                Tools::getListKey($dataRules),
                $excludedFields
            );
        }
        return $result;
    }

    public static function getFetchParams($allParams, $fillable=[]){
        $keyword = null;
        $orderBy = '-id';
        $params = [];
        if(array_key_exists('keyword', $allParams)){
            $keyword = $allParams['keyword'];
        }

        if(array_key_exists('orderBy', $allParams)){
            $orderBy = $allParams['orderBy'];
        }
        foreach ($allParams as $field => $param) {
            if(count($fillable)){
                if(in_array($field, $fillable)){
                    $params[$field] = $param;
                }
            }else{
                $params[$field] = $param;
            }
        }
        $result = [
            'params' => $params,
            'keyword' => $keyword,
            'orderBy' => $orderBy
        ];

        return $result;
    }

    public static function getRequestValue($request, $key, $type, $default=null){
        switch($type){
            case 'str':
                return self::toStr($request->input($key, $default), $default);
            break;
            case 'bool':
                return self::toBool($request->input($key, $default), $default);
            break;
            case 'int':
                return self::toInt($request->input($key, $default), $default);
            break;
            case 'float':
                return self::toFloat($request->input($key, $default), $default);
            break;
            case 'date':
                return self::toDate($request->input($key, $default));
            break;
            default:
                return self::toStr($request->input($key, $default), $default);
        }
    }

    public static function listInput($allParams, $model=null){
        $result = self::getFetchParams($allParams, !$model?[]:$model->getFillable());
        return [
            $result['params'],
            $result['keyword'],
            $result['orderBy']
        ];
    }
}
