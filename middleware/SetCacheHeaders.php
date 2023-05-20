<?php


use Closure as Closure;
use Illuminate\Support\Carbon;

class SetCacheHeaders implements WondPHP\MiddlewareInterface 
{
    /**
     * Add cache related HTTP headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $options
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     */
    public function process($request, Closure $next, ...$options )
    {
        //cache.headers:public;max_age=2628000;etag
        // echo  'SetCacheHeaders    ';
        $options['etag'] =true;
        $options['private'] =true;
        $options['max_age'] =2628000;
        $options['last_modified'] =1621131790;
        $response = $next($request);

        // return $response;///////////
        // var_dump($response);exit;
        if (! $request->isMethodCacheable() || ! $response->rawBody()) {
            return $response;
        }

        if (is_string($options)) {
            $options = $this->parseOptions($options);
        }

        // var_dump($response);
        if (isset($options['etag']) && $options['etag'] === true) {
            $options['etag'] = md5($response->rawBody());
        }

        if (isset($options['last_modified'])) {
            if (is_numeric($options['last_modified'])) {
                $options['last_modified'] = Carbon::createFromTimestamp($options['last_modified']);
            } else {
                $options['last_modified'] = Carbon::parse($options['last_modified']);
            }
        }

        $response->setCache($options);
        $response->isNotModified($request);

        return $response;
    }

    /**
     * Parse the given header options.
     *
     * @param  string  $options
     * @return array
     */
    protected function parseOptions($options)
    {
        return collect(explode(';', $options))->mapWithKeys(function ($option) {
            $data = explode('=', $option, 2);

            return [$data[0] => $data[1] ?? true];
        })->all();
    }
}
