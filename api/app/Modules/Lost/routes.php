<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\Lost\Controllers';
	Route::group(
		['prefix' => 'lost', 'module'=>'Lost', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'LostController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'LostController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'LostController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'LostController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'LostController@removeItem'
			]);
			Route::get('rate', [
				'as' => 'rate',
				'uses' => 'LostController@rate'
			]);
		}
	);
});