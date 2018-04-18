<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\ExportBill\Controllers';
	Route::group(
		['prefix' => 'export-bill', 'module'=>'ExportBill', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'ExportBillController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'ExportBillController@obj'
			]);
			Route::post('add', [
				'middleware' => 'token_required',
				'as' => 'addItem',
				'uses' => 'ExportBillController@addItem'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'ExportBillController@editItem'
			]);
			Route::post('edit-contact', [
				'middleware' => 'token_required',
				'as' => 'editContact',
				'uses' => 'ExportBillController@editContact'
			]);
			Route::post('remove', [
				'middleware' => 'token_required',
				'as' => 'removeItem',
				'uses' => 'ExportBillController@removeItem'
			]);
			Route::get('rate', [
				'as' => 'rate',
				'uses' => 'ExportBillController@rate'
			]);
		}
	);
});