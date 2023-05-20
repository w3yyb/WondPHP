<?php

class aMiddleware implements WondPHP\MiddlewareInterface {

    public function process($object, Closure $next,...$params)
    {
        echo ' <b>after</b> ';

        $response = $next($object);

        // $object->runs[] = 'after';
        $response1 = response('后中间件');
// var_dump($response1);
        $response1->cookie('foo111', 'value');
 $response1->modify('header','one1111','fffffffffff');

        return $response1;
    }

}
?>
