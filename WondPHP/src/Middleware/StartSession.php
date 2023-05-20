<?php
namespace WondPHP\Middleware;

class StartSession implements \WondPHP\MiddlewareInterface
{
    public function process($object, \Closure $next, ...$params)
    {
        $session =  $object->session();

        if (! $session->has('_token')) {
            $session->regenerateToken();
        }

        $response = $next($object);
        $this->storeCurrentUrl($object, $session);
        return $response;
    }

    /**
    * Store the current URL for the request if necessary.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Illuminate\Contracts\Session\Session  $session
    * @return void
    */
    protected function storeCurrentUrl($request, $session)
    {
        if ($request->method() === 'GET' &&
            // $request->route() instanceof Route &&
            
            ! $request->ajax() &&
            ! $request->prefetch()) {
            $session->setPreviousUrl($request->fullUrl());
        }
    }
}
