<?php

use Faker\Generator as Faker;
use GetCandy\Api\Core\Baskets\Models\Basket;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Basket::class, function (Faker $faker) {
    return [
        'currency' => 'GBP',
    ];
});
