<?php namespace App\Helpers;

class ResTools {
	const PER_PAGE = 15;

	public static $ERROR_CODES = [
		'OK' => 200,
		'BAD_REQUEST' => 400,
		'UNAUTHORIZED' => 401,
		'FORBIDEN' => 403,
		'NOT_FOUND' => 404,
		'METHOD_NOT_ALLOWED' => 405,
		'INTERNAL_SERVER_ERROR' => 500,
	];

	public static function lst($data, $extra=[]) {
		if($data === null){
			$data = [];
		}
		# var_dump(gettype($data));die;
		if(gettype($data) === 'object'){
			try{
				$data = $data->toArray();
			}catch(\Error $e){
				$data = [];
			}
		}
		if(array_key_exists('data', $data)){
			$result = [
				'success' => true,
				'status_code' => self::$ERROR_CODES['OK'],
				'message' => null,
				'data' => [
					'items' => $data['data'],
					'_meta' => [
						'current_page' => $data['current_page'],
						'last_page' => $data['last_page'],
						'from' => $data['from'],
						'to' => $data['to'],
						'per_page' => $data['per_page'],
						'page_count' => $data['total'],
					],
					'_links' => [
						'next_link' => $data['next_page_url'],
						'last_link' => $data['prev_page_url'],
					],
				],
				'extra' => $extra
			];
		}else{
			$result = [
				'success' => true,
				'status_code' => self::$ERROR_CODES['OK'],
				'message' => null,
				'data' => [
					'items' => $data,
					'_meta' => [
						'current_page' => 1,
						'last_page' => 1,
						'from' => 1,
						'to' => 1,
						'per_page' => count($data),
						'page_count' => 1,
					],
					'_links' => [
						'next_link' => '',
						'last_link' => '',
					],
				],
				'extra' => $extra
			];
		}

		return $result;
	}

	public static function obj($data, $message=null, $extra=[]) {
		if($data === null){
			$data = [];
		}
		$original = false;
		if($message ===  true){
			$original = true;
			$message = null;
		}

		if(gettype($data) === 'object' && !$original){
			try{
				$data = $data->toArray();
			}catch(\Error $e){
				$data = [];
			}
		}
		$result = [
			'success' => true,
			'status_code' => 200,
			'message' => $message,
			'extra' => $extra,
			'data' => null
		];
		if(!$original){
			$result['data'] = count($data) == 0 ? null : $data;
		}else{
			$result['data'] = $data;
		}

		return $result;
	}

	public static function err($errors, $statusCode=null) {
		if($statusCode == null){
			$statusCode = self::$ERROR_CODES['INTERNAL_SERVER_ERROR'];
		}
		$result = [
			'success' => false,
			'status_code' => $statusCode,
			'message' => '',
			'data' => null,
		];

		if(gettype($errors) ==  'string'){
			$result['message'] = [];
			$result['message']['common'] = $errors;
		}else{
			$result['message'] = $errors;
		}
		return $result;
	}

	public static function criticalErr($errors, $statusCode=null) {
		if($statusCode == null){
			$statusCode = self::$ERROR_CODES['INTERNAL_SERVER_ERROR'];
		}
		$result = [
			'success' => false,
			'status_code' => $statusCode,
			'message' => '',
			'data' => null,
		];

		if(gettype($errors) ==  'string'){
			$result['message'] = [];
			$result['message']['common'] = $errors;
		}else{
			$result['message'] = $errors;
		}

		if(config('app.app_env') === 'local'){
			if(gettype($result['message']) !== 'object'){
				abort(500, $result['message']['common']);die;
			}
		}

		return $result;
	}
}