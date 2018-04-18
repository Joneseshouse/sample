<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Helpers\Tools;

$factory->define(App\Modules\Config\Models\Config::class, function (Faker\Generator $faker) {
    $data = [
        'uid' => Tools::niceUrl($faker->name),
        'value' => $faker->name,
    ];
    return $data;
});


$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Modules\Admin\Models\Admin::class, function (Faker\Generator $faker) {
    $password = '1234567890';
    $data = [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt($password),
        'role' => 'admin',
    ];
    return $data;
});

$factory->define(App\Modules\User\Models\User::class, function (Faker\Generator $faker) {
    $password = '1234567890';
    $data = [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt($password),
        'role' => 'user',
    ];
    return $data;
});

$factory->define(App\Modules\Category\Models\Category::class, function (Faker\Generator $faker) {
    $title = $faker->name;
    $data = [
        'title' => $title,
        'uid' => Tools::niceUrl($title, false),
        'ascii_title' => Tools::niceUrl($title),
        'single' => true,
        'type' => 'dropdown',
    ];
    return $data;
});

$factory->define(App\Modules\Customer\Models\Customer::class, function (Faker\Generator $faker) {
    $title = $faker->name;
    $data = [
        'user_id' => User::first()->id,
        'title' => $title,
        'ascii_title' => Tools::niceUrl($title, false),
        'email' => $faker->email,
        'phone' => $faker->e164PhoneNumber,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'address' => $faker->address,
    ];
    return $data;
});

$factory->define(App\Modules\Dropdown\Models\Dropdown::class, function (Faker\Generator $faker) {
    $title = str_slug($faker->name);
    $data = [
        'title' => $title,
        'ascii_title' => Tools::niceUrl($title, false),
        'category_id' => Category::first()->id,
        'value' => $faker->name,
        'order' => $faker->randomNumber
    ];
    return $data;
});

$factory->define(App\Modules\Tax\Models\Tax::class, function (Faker\Generator $faker) {
    $title = str_slug($faker->name);
    $data = [
        'user_id' => User::first()->id,
        'title' => $title,
        'ascii_title' => Tools::niceUrl($title, false),
        'abbrevitation' => $faker->name,
        'description' => $faker->name,
        'tax_number' => $faker->randomNumber,
        'show_tax_number_on_invoice' => $faker->boolean,
        'recoverable' => $faker->boolean,
        'compound' => $faker->boolean,
        'rate' => $faker->numberBetween(1, 99),
    ];
    return $data;
});

$factory->define(App\Modules\ProductType\Models\ProductType::class, function (Faker\Generator $faker) {
    $title = str_slug($faker->name);
    $data = [
        'user_id' => User::first()->id,
        'title' => $title,
        'ascii_title' => Tools::niceUrl($title, false),
        'order' => $faker->randomNumber
    ];
    return $data;
});

$factory->define(App\Modules\Product\Models\Product::class, function (Faker\Generator $faker) {
    $title = str_slug($faker->name);
    $productType = ProductType::first();
    $listTax = Tax::take(3)->get();
    $taxes = [];
    foreach ($listTax as $tax) {
        array_push($taxes, (string)$tax->id);
    }
    $taxes = implode(',', $taxes);
    $data = [
        'user_id' => User::first()->id,
        'product_type_id' => $productType->id,
        'product_type_title' => $productType->title,
        'product_number' => $faker->randomNumber,
        'title' => $title,
        'ascii_title' => Tools::niceUrl($title, false),
        'description' => $faker->name,
        'price' => $faker->randomNumber,
        'sell' => $faker->boolean,
        'buy' => $faker->boolean,
        'income_account_id' => Account::first()->id,
        'expense_account_id' => Account::get()[1]->id,
        'taxes' => $taxes,
    ];
    return $data;
});