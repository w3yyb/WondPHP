<?php
namespace WondPHP\Middleware;
class gMiddleware implements \WondPHP\MiddlewareInterface {

    public function process($object, \Closure $next,...$params)
    {
        echo 999999;
        $response = $next($object);

        // $object->runs[] = 'after';
        echo ' <b>after</b> ';
        $response1 = response('后中间件9999');
// var_dump($response1);
        $response1->cookie('foo111', 'value');
 $response1->modify('header','one1111','fffffffffff');

        return $response1;
    }

}
?>
