<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\Contact\Controllers';
	Route::group(
		['prefix' => 'contact', 'module'=>'Contact', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'ContactController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'ContactController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'ContactController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'ContactController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'ContactController@removeItem'
			]);
			Route::get('rate', [
				'middleware' => 'token_user_check',
				'as' => 'rate',
				'uses' => 'ContactController@rate'
			]);
		}
	);
});