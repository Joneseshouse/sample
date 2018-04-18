<?php

use App\Modules\Category\Models\Category;
use App\Modules\Article\Models\Article;

$prefix = "";  // URL prefix

$module = basename(__DIR__);
$namespace = "App\Modules\\{$module}\Controllers";

Route::group(
    ["prefix" => $prefix, "module" => $module , "namespace" => $namespace],
    function() use($module){
        Route::get('/', [
            # middle here
            "as" => "{$module}.index",
            "uses" => "{$module}Controller@index"
        ]);
        Route::get('item-receiver', [
            # middle here
            "as" => "{$module}.itemReceiver",
            "uses" => "{$module}Controller@itemReceiver"
        ]);
        Route::get('grabbing-receiver', [
            # middle here
            "as" => "{$module}.grabbingReceiver",
            "uses" => "{$module}Controller@grabbingReceiver"
        ]);
        Route::get('cart', [
            # middle here
            "as" => "{$module}.cart",
            "uses" => "{$module}Controller@cart"
        ]);
        /*
        Route::get('googlecfb9267ccd245179.html', [
            # middle here
            "as" => "{$module}.webmasterConfirm",
            "uses" => "{$module}Controller@webmasterConfirm"
        ]);
        Route::get('contact', [
            # middle here
            "as" => "{$module}.contact",
            "uses" => "{$module}Controller@contact"
        ]);
        Route::get('lien-he-dang-ky', [
            # middle here
            "as" => "{$module}.register",
            "uses" => "{$module}Controller@register"
        ]);
        Route::post('signup', [
            # middle here
            "as" => "{$module}.signup",
            "uses" => "{$module}Controller@signup"
        ]);
        Route::get('rate', [
            # middle here
            "as" => "{$module}.rate",
            "uses" => "{$module}Controller@rate"
        ]);
        Route::get('services', [
            # middle here
            "as" => "{$module}.services",
            "uses" => "{$module}Controller@services"
        ]);
        Route::get('commitments', [
            # middle here
            "as" => "{$module}.commitments",
            "uses" => "{$module}Controller@commitments"
        ]);
        Route::get('templates', [
            # middle here
            "as" => "{$module}.templates",
            "uses" => "{$module}Controller@templates"
        ]);
        Route::post('contact/confirm', [
            # middle here
            "as" => "{$module}.contactConfirm",
            "uses" => "{$module}Controller@contactConfirm"
        ]);
        Route::get('danh-sach/{category_uid}', [
            # middle here
            "as" => "{$module}.list",
            "uses" => "{$module}Controller@list"
        ]);
        Route::get('bai-viet/{id}/{slug}', [
            # middle here
            "as" => "{$module}.detail",
            "uses" => "{$module}Controller@detail"
        ]);

        Route::get('item-receiver', [
            # middle here
            "as" => "{$module}.itemReceiver",
            "uses" => "{$module}Controller@itemReceiver"
        ]);
        Route::get('grabbing-receiver', [
            # middle here
            "as" => "{$module}.grabbingReceiver",
            "uses" => "{$module}Controller@grabbingReceiver"
        ]);
        Route::get('cart', [
            # middle here
            "as" => "{$module}.cart",
            "uses" => "{$module}Controller@cart"
        ]);

        Route::get('test-translate', [
            # middle here
            "as" => "{$module}.testTranslate",
            "uses" => "{$module}Controller@testTranslate"
        ]);
        Route::post('test-translate', [
            # middle here
            "as" => "{$module}.testTranslate",
            "uses" => "{$module}Controller@testTranslate"
        ]);
        Route::get('sitemap.xml', function(){
            $sitemap = App::make("sitemap");
            $sitemap->setCache('laravel.sitemap', 60);
            if (!$sitemap->isCached()){
                $sitemap->add(URL::to('/'), null, 1, 'daily');
                $sitemap->add(URL::to('lien-he-dang-ky'), null, 1, 'daily');

                $listArticleCategory = Category::where('type', 'article')->get();
                foreach ($listArticleCategory as $articleCategory) {
                    $sitemap->add(URL::to('danh-sach/'.$articleCategory->uid), $articleCategory->updated_at, 1, 'daily');
                }
                $listArticle = Article::all();
                foreach ($listArticle as $article) {
                    $sitemap->add(URL::to('bai-viet/'.$article->id.'/'.$article->slug), $article->updated_at, 1, 'daily');
                }
            }
            return $sitemap->render('xml');
        });
        */
    }
);