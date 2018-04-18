<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\AdminTransaction\Controllers';
	Route::group(
		['prefix' => 'admin-transaction', 'module'=>'AdminTransaction', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'AdminTransactionController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'AdminTransactionController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'AdminTransactionController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'AdminTransactionController@editItem'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'AdminTransactionController@removeItem'
			]);
			Route::get('rate', [
				'as' => 'rate',
				'uses' => 'AdminTransactionController@rate'
			]);
			Route::get('test-amazon', [
				'as' => 'testAmazon',
				'uses' => 'AdminTransactionController@testAmazon'
			]);
		}
	);
});