<?php

return [
     
    //最先执行:全局中间件
    'middleware'=>[
        // \WondPHP\Middleware\gMiddleware::class,
        // aMiddleware::class,
       // bMiddleware::class,
       // SetCacheHeaders::class,

        // //\App\Http\Middleware\TrustHosts::class,
        \WondPHP\Middleware\TrustProxies::class,
        \WondPHP\Middleware\Cors::class,
        // \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \WondPHP\Middleware\ValidatePostSize::class,
        \WondPHP\Middleware\TrimStrings::class,
        \WondPHP\Middleware\ConvertEmptyStringsToNull::class,
    ],

    //其次执行=> 全局路由中间件 :这里的可以被路由排除，但不可以被控制器中间件排除
    'middlewareGroups' => [
        'web' => [
            // \App\Http\Middleware\EncryptCookies::class, //todo 
            \WondPHP\Middleware\AddQueuedCookiesToResponse::class,
           \WondPHP\Middleware\StartSession::class,
            // // \Illuminate\Session\Middleware\AuthenticateSession::class,
            // \Illuminate\View\Middleware\ShareErrorsFromSession::class,  //todo
            \WondPHP\Middleware\VerifyCsrfToken::class,
            \WondPHP\Middleware\SubstituteBindings::class,
        ],

        //url 以api开头的
        'api' => [
            // 'throttle:api',
        \WondPHP\Middleware\SubstituteBindings::class,
        ],
    ],
     //路由中间件，此不会 自动执行，需要手动调用      laravel 的非全局 中间件是有顺序的
    'routeMiddleware' => [      //最后执行下面的
       'a'=>  aMiddleware::class,
       'b'=> bMiddleware::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ],


    
     
];
