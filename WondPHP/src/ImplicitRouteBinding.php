<?php

namespace WondPHP;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;

class ImplicitRouteBinding
{
    /**
     * Resolve the implicit route bindings for the given route.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Illuminate\Routing\Route  $route
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function resolveForRoute($container, $route)
    {
        $parameters =  $route->parameters();  //done
        if (empty($parameters)) {
            $parameters = $_SERVER['REQUEST_URI'];
            $requris=explode('/', $parameters);

            foreach ($requris as $rk => $rv) {
                if ($rk>=3) {
                    $reqVars[]=$rv;
                }
            }

            for ($i=0; $i <count($reqVars ?? []) ; $i++) {
                if ($i % 2 ==0) {
                    $reqVarss[$reqVars[$i]] =$reqVars[$i+1];
                }
            }
            $parameters =$reqVarss ?? [];
            $route->setParameterAll($parameters);
        }

        // var_dump($parameters);exit;
        $routeinfo=$route->routeinfo();
        $call =$routeinfo[1];
        if ($call  instanceof \Closure) {
            $reflector = new \ReflectionFunction($call);
            $args= $container->bindParams($reflector);
        } else {
            $call=strstr($call, "|", true) ? strstr($call, "|", true) : $call;
            $class=strstr($call, "@", true) ? strstr($call, "@", true): $call;
            $method=strstr($call, "@") ?  strstr($call, "@") :'Index';
            $method =str_replace('@', '', $method);
            // $reflact = new \ReflectionMethod('\\app\controllers\\'.$class, $method);
            if (!strstr($class, 'app\controllers')) {
                $class ='\\app\controllers\\'.$class;
            }

            $reflact = new \ReflectionMethod($class, $method);

            $args= $container->bindParams($reflact);
        }
       

        $i=0;
        // foreach ($route->signatureParameters(UrlRoutable::class) as $parameter) {
        if ($args) {
            foreach ($parameters as $parameterName=>$parameter) {
                $modelsName= get_class($args[$i]);
                $i++;
                // if (! $parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                //     continue;
                // }

                if (strstr($parameterName, '-')) {
                    $parameterNameSlug =explode('-', $parameterName)[1];
                } else {
                    $parameterNameSlug =null;
                }

                $parameterValue = $parameters[$parameterName];  //done

                if ($parameterValue instanceof UrlRoutable) {
                    continue;
                }

                // $instance = $container->make(Reflector::getParameterClassName($parameter)); //为App\Models\User
                $instance = $container->make($modelsName);

                if (!$instance instanceof \WondPHP\Model) {
                    continue;
                }
                $parent = $route->parentOfParameter($parameterName);

                if ($parent instanceof UrlRoutable && in_array($parameterName, array_keys($route->bindingFields()))) {
                    if (! $model = $parent->resolveChildRouteBinding(
                        $parameterName,
                        $parameterValue,
                        $route->bindingFieldFor($parameterName)
                    )) {
                        throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
                    }
                } elseif (! $model = $instance->resolveRouteBinding($parameterValue, $parameterNameSlug??$route->bindingFieldFor($parameterName))) {//resolveRouteBinding 第2个参数是数据库的字段名
                    throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
                }

                $route->setParameter($parameterName, $model);
            }
        }
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return string|null
     */
    protected static function getParameterName($name, $parameters)
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }
    }
}
