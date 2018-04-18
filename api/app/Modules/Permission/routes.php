<?php
Route::group(['prefix' => 'api/v1/', 'middleware' => 'api'], function(){
	$namespace = 'App\Modules\Permission\Controllers';
	Route::group(
		['prefix' => 'permission', 'module'=>'Permission', 'namespace' => $namespace],
		function() {
			Route::get('list', [
				'middleware' => 'token_required',
				'as' => 'list',
				'uses' => 'PermissionController@list'
			]);
			Route::get('obj', [
				'middleware' => 'token_required',
				'as' => 'obj',
				'uses' => 'PermissionController@obj'
			]);
			Route::post('edit', [
				'middleware' => 'token_required',
				'as' => 'editItem',
				'uses' => 'PermissionController@editItem'
			]);
			Route::get('sync-list', [
				'middleware' => 'token_required',
				'as' => 'syncList',
				'uses' => 'PermissionController@syncList'
			]);
		}
	);
});