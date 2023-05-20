<?php

namespace WondPHP\Middleware;

use Closure;
use WondPHP\Http\ParameterBag;

class TransformsRequest implements \WondPHP\MiddlewareInterface
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function process($request, \Closure $next,...$params)
    {
        $this->clean($request);

        return $next($request);
    }

    /**
     * Clean the request's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clean($request)
    {

        $this->cleanParameterBag($request->get(),$request,'get');
        if ($request->isJson()) {
            $this->cleanParameterBag($request->post(),$request,'post');
        } elseif ($request->post() !== $request->get()) {
            $this->cleanParameterBag($request->post() ,$request,'post');
        }
    }

    /**
     * Clean the data in the parameter bag.
     *
     * @param  \Symfony\Component\HttpFoundation\ParameterBag  $bag
     * @return void
     */
    protected function cleanParameterBag( $request,$requestobject,$type)
    {
        $bag = new ParameterBag($request);
        $bag->replace($this->cleanArray($bag->all()));
        foreach ($bag->all() as $key => $value) {
            $requestobject->modify($type, $key, $value);
        }

        // var_dump($bag->all());
    }

    /**
     * Clean the data in the given array.
     *
     * @param  array  $data
     * @param  string  $keyPrefix
     * @return array
     */
    protected function cleanArray(array $data, $keyPrefix = '')
    {
        return collect($data)->map(function ($value, $key) use ($keyPrefix) {
            return $this->cleanValue($keyPrefix.$key, $value);
        })->all();
    }

    /**
     * Clean the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function cleanValue($key, $value)
    {
        if (is_array($value)) {
            return $this->cleanArray($value, $key.'.');
        }

        return $this->transform($key, $value);
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        return $value;
    }
}
