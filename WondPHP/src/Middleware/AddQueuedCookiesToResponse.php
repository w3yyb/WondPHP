<?php

namespace WondPHP\Middleware;

use Closure as Closure;
use WondPHP\Cookie as CookieJar;

class AddQueuedCookiesToResponse implements \WondPHP\MiddlewareInterface
{
    /**
     * The cookie jar instance.
     *
     * @var \Illuminate\Contracts\Cookie\QueueingFactory
     */
    protected $cookies;

    /**
     * Create a new CookieQueue instance.
     *
     * @param  \Illuminate\Contracts\Cookie\QueueingFactory  $cookies
     * @return void
     */
    public function __construct(CookieJar $cookies)
    {
        // $cookies = new CookieJar;
        $this->cookies = $cookies;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function  process($request, Closure $next,...$params)
    {

        $response = $next($request);

        foreach ($this->cookies->getQueuedCookies() as $cookie) {

            $name=$cookie->getName();
            $value=$cookie->getValue();
            $domain=$cookie->getDomain();
            $max_age=$cookie->getMaxAge();
            $path=$cookie->getPath();
            $secure=$cookie->isSecure();
            $http_only=$cookie->isHttpOnly();
            $sameSite=$cookie->getSameSite();
            $response->cookie($name, $value ,  $max_age , $path , $domain, $secure , $http_only );
        }

        return $response;
    }
}
