<?php

namespace WondPHP\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Registrar;
use WondPHP\Contracts\Route;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubstituteBindings    implements \WondPHP\MiddlewareInterface 
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new bindings substitutor.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct( )
    {
        $this->router =  app('router');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    public function process($request, \Closure $next,...$params)

    {
        try {
            // $this->router->substituteBindings($route = $request->route()); //todo
            $this->router->substituteImplicitBindings($this->router);
        } catch (ModelNotFoundException $exception) {
            if ($this->router->getMissing()) {
                return $this->router->getMissing()($request);
            }

            throw $exception;
        }

        return $next($request);
    }
}
