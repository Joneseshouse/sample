<?php

$prefix = "api/v1/";  // URL prefix

$module = basename(__DIR__);
$namespace = "App\Modules\\{$module}\Controllers";

Route::group(["prefix" => $prefix, "namespace" => $namespace, "middleware" => "api"],
	function() use($module){
		Route::group(
			["prefix" => "order-item-note"],
			function() use($module){
				Route::get("list", [
					"middleware" => "token_required",
					"as" => "{$module}.list",
					"uses" => "{$module}Controller@list"
				]);
				Route::get("obj", [
					"middleware" => "token_required",
					"as" => "{$module}.obj",
					"uses" => "{$module}Controller@obj"
				]);
				Route::post("add", [
					"middleware" => "token_required",
					"as" => "{$module}.addItem",
					"uses" => "{$module}Controller@addItem"
				]);
				Route::post("edit", [
					"middleware" => "token_required",
					"as" => "{$module}.editItem",
					"uses" => "{$module}Controller@editItem"
				]);
				Route::post("remove", [
					"middleware" => "token_required",
					"as" => "{$module}.removeItem",
					"uses" => "{$module}Controller@removeItem"
				]);
			}
		);
	}
);