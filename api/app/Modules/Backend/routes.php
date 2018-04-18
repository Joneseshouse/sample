<?php

Route::group(["prefix" => "admin"],
    function() {
        $module = "Backend";
        $controller = "\App\Modules\\{$module}\Controllers\\{$module}Controller";

        Route::get("/", ["uses" => "{$controller}@profile"]);
        Route::get("login", ["uses" => "{$controller}@login"]);
        Route::get("admin", ["uses" => "{$controller}@admin"]);
        Route::get("config", ["uses" => "{$controller}@config"]);
        Route::get("permission", ["uses" => "{$controller}@permission"]);
        Route::get("role-type", ["uses" => "{$controller}@roleType"]);
        Route::get("role/{id}", ["uses" => "{$controller}@role"]);
        Route::get("category/{type?}", ["uses" => "{$controller}@category"]);
        Route::get("banner/{category_id}", ["uses" => "{$controller}@banner"]);
        Route::get("article/{category_id}", ["uses" => "{$controller}@article"]);
        Route::get("article/detail/{id}", ["uses" => "{$controller}@articleDetail"]);
    }
);