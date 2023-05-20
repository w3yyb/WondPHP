<?php
/**
 * 路由配置文件

 */

return [
    [
        // 指向首页
        [
            ['*'],
            '/',
            // '/{name:.+}',//
            'HomeController',
            'shouye'//命名路由
            // 'HomeController@test|eMiddleware|fMiddleware|pageCacheMiddleware:20'
            // 'HomeController@test'
            
            // function ($path){//闭包路由
            //     // var_dump($path);exit;
            //     // $vars='ssss';
            //     // $class = "App\\Http\\Controllers\\$module\\".ucfirst(strtolower($class)).'Controller';
            //     // echo    Ioc::make($class, $action, [$vars]);
         
            // },
        ],
        [
            ['POST'],
            '/test/store',
            'TestController@store',
        ],

        ['photos', 'PhotoController'],//rest
        ['photos.comments', 'PhotoCommentController'],//rest 资源嵌套
        [
            ['*', 'POST'],
            '/demo/{user}',   //  '/demo/{user-slug}',  路由与模型绑定时，自定义键名。 有时，您可能希望使用 id 以外的列来解析 Eloquent 模型。为此，您可以在路由参数定义中指定列
           'DemoController|a|b'
        
            // function (app\models\User $user) {
            //     return  $user->username;
            // },
        ],
        
        // 指向adminController
        [
            'GET',
            '/admin',
            ''
        ],
        
        // 指向错误页面
        [
            'GET',
            '/error/{code:\d+}',
            'demoController'
        ],
        
        [
            'GET',
            '/user[/{action}]',
            'userController'
        ],
        /*['GET','/user/{id:\d+}/{name}','userController'],*/
    ],
    // 后台路由
    '/admin|bMiddleware' => [
        
        // 指向adminUserController
        [
            'GET',
            '/user',
            'userController|!aMiddleware', //  'userController|!aMiddleware', 
            'adminuser',
        ],
        ['photos', 'PhotoController'],//rest
        // ['photos.comments', 'PhotoCommentController'],//rest 资源嵌套



        ],
        '/haha|aMiddleware' => [
        
            // 指向adminUserController
            [
                'GET',
                '/hahas',
                '',
                'aaaaa',
            ]
        ],



];
