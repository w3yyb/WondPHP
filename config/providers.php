<?php
//注意不要更改顺序，要不要会有问题  带'key'=>function(){}的为延迟加载 调用时需要先用app()->get("key"); 实例化
/*
 laravel 中以下3个为核心服务
 $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));

*/
return [
  // 'WondPHP\PageCache',

  // function () {
  //   (new \Whoops\Run)->pushHandler(new \Whoops\Handler\PrettyPageHandler)->register();
  // },
  // 'GetEnv',
//   'WondPHP\DotEnv',
  // 'WondPHP\Config',
//    function () {
//     WondPHP\Http\Protocols\Session::handlerClass(config('session')['handler'], config('session')['config'][config('session')['type']]);
// },
// 'Events'=>function(){//
//    return (new WondPHP\Events);
// },//比较费性能

// [new WondPHP\Route(), 'run'],

];
