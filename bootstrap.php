<?php
use WondPHP\App;
define('BASE_PATH', __DIR__);
define('PAGE_CACHE', false);
require BASE_PATH.'/helper/env.php';
require BASE_PATH.'/vendor/autoload.php';

// //宏response  影响 middleware TOTO
// response()::macro('share', function ($value) {
//     return response($value);
// });
$app =new App();
$response = tap($app->handle())->send();
