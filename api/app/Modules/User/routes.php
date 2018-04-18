<?php

$prefix = "api/v1/";  // URL prefix

$module = basename(__DIR__);
$namespace = "App\Modules\\{$module}\Controllers";

Route::group(["prefix" => $prefix, "namespace" => $namespace, "middleware" => "api"],
	function() use($module){
		Route::group(
			["prefix" => "user", "module"=>"User"],
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
				Route::get("statistics", [
					"middleware" => "token_required",
					"as" => "{$module}.userStatistics",
					"uses" => "{$module}Controller@statistics"
				]);
				Route::post("add", [
					"middleware" => "token_required",
					"as" => "{$module}.addItem",
					"uses" => "{$module}Controller@addItem"
				]);
				Route::post("signup", [
					"as" => "{$module}.signup",
					"uses" => "{$module}Controller@signup"
				]);
				Route::post("edit", [
					"middleware" => "token_required",
					"as" => "{$module}.editItem",
					"uses" => "{$module}Controller@editItem"
				]);
				Route::post("assign", [
					"middleware" => "token_required",
					"as" => "{$module}.assign",
					"uses" => "{$module}Controller@assign"
				]);
				Route::post("remove", [
					"middleware" => "token_required",
					"as" => "{$module}.removeItem",
					"uses" => "{$module}Controller@removeItem"
				]);
				Route::post("authenticate", [
					"as" => "{$module}.authenticate",
					"uses" => "{$module}Controller@authenticate"
				]);
				Route::post("reset-password", [
					"as" => "{$module}.resetPassword",
					"uses" => "{$module}Controller@resetPassword"
				]);
				Route::get("reset-password-confirm", [
					"as" => "{$module}.resetPasswordConfirm",
					"uses" => "{$module}Controller@resetPasswordConfirm"
				]);
				Route::post("change-password", [
					"middleware" => "token_required",
					"as" => "{$module}.changePassword",
					"uses" => "{$module}Controller@changePassword"
				]);
				Route::get("change-password-confirm", [
					"as" => "{$module}.changePasswordConfirm",
					"uses" => "{$module}Controller@changePasswordConfirm"
				]);
				Route::get("profile", [
					"middleware" => "token_required",
					"as" => "{$module}.getProfile",
					"uses" => "{$module}Controller@profile"
				]);
				Route::post("update-profile", [
					"middleware" => "token_required",
					"as" => "{$module}.updateProfile",
					"uses" => "{$module}Controller@updateProfile"
				]);
				Route::post("logout", [
					"middleware" => "token_required",
					"as" => "{$module}.logout",
					"uses" => "{$module}Controller@logout"
				]);
			}
		);
	}
);