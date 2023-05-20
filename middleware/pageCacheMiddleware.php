<?php

class pageCacheMiddleware implements WondPHP\MiddlewareInterface {

    public function process($object, Closure $next,...$params)
    {
        WondPHP\PageCache::getInstance($params[0]);

          return $next($object);

    }

}
?>
