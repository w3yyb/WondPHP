<?php
namespace WondPHP;

use WondPHP\Contracts\Route as RouteContracts;

/**
 * 核心路由查找器
 */

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use function FastRoute\cachedDispatcher;
use Symfony\Component\String\Inflector\EnglishInflector ;

class Route implements RouteContracts
{
    // use SingletonTrait;

    protected static $_nameList = [];
    protected $_path = [];
    protected $routeInfos;
    protected $group_middlewares=[];
    protected $middlewareGroups=[];
    protected $bindingFields = [];
    protected $middlewarePriority = [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,

    ];
    protected $parameters=[];
    public function __construct()
    {
        $middleware =require BASE_PATH.'/config/middleware.php';
        $this->middlewareGroups =$middleware['middlewareGroups'];
    }
    public function run()
    {
/** @var object $dispatcher 导入配置中的路由规则 */
        // $dispatcher = simpleDispatcher(function (RouteCollector $r) {
        $dispatcher = cachedDispatcher(function (RouteCollector $r) {
            $routeConfig=include BASE_PATH. '/config/route.php';
            $EnglishInflector= new EnglishInflector();

            foreach ($routeConfig as $key => $value) {
                if ($key) {
                    if (strstr($key, '|')) {
                        $keys= explode('|', $key);
                        $key=$keys[0];
                        $this->group_middlewares[$key]=\array_slice($keys, 1);
                    }

                    $r->addGroup($key, function (RouteCollector $r) use ($key, $value, $EnglishInflector) {
                        foreach ($value as $k => $v) {
                            if (!isset($v[2])) {//rest
                                $this->resource($EnglishInflector, $v[0], $v[1], $r, true, $key);
                            }
                            if (isset($v[2]) &&$v[2] instanceof \Closure) {
                                $r->addRoute($v[0], $v[1], $v[2]);
                            } else {
                                // 如果控制器配置项为空时，默认根据路由获取控制器
                                $r->addRoute($v[0], $v[1], substr($key, 1) . '\\'.ucfirst(empty($v[2])
                                ? $v[2] = substr($v[1], 1) . 'Controller'
                                : $v[2]));
                            }

                            if (isset($v[3])) {
                                $this->name($v[3]);
                                $this->_path[$v[3]]= $key.$v[1];
                            } else {
                                $this->name($v[1]);
                                $this->_path[$v[1]]=$v[1];
                            }
                        }
                    });
                } else {
                    foreach ($value as $k => $v) {
                        if (!isset($v[2])) {//rest
                            $this->resource($EnglishInflector, $v[0], $v[1], $r);
                        }
                        if (isset($v[2]) && $v[2] instanceof \Closure) {
                            $r->addRoute($v[0], $v[1], $v[2]);
                        } else {
                            $r->addRoute($v[0], $v[1], substr($v[2] ?? '', 0, 1) === ''
                            ? substr(empty($v[2])
                                     ? $v[2] = substr($v[1], 0) . 'Controller'
                                     : $v[2], 1)
                        : $v[2]);
                        }

                        if (isset($v[3])) {
                            $this->name($v[3]);
                            $this->_path[$v[3]]=$v[1];
                        } else {
                            $this->name($v[1]);
                            $this->_path[$v[1]]=$v[1];
                        }
                    }
                }
            }
        }, [
        'cacheFile' => BASE_PATH . '/cache/route/route.cache', /* required */
        'cacheDisabled' => env('APP_DEBUG'),     /* optional, enabled by default */
    ]);
        // 获取http传参方式和资源URI
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri =$rawuri= $_SERVER['REQUEST_URI'];
        if ($uri !== '/') {//去除url尾部斜杠
            while ($uri !== $uri = rtrim($uri, '/'));
        }
        $rawuri=$uri;
        // 将url中的get传参方式（?foo=bar）剥离并对URI进行解析
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
    
        $uri = rawurldecode($uri);
        $rawuri = rawurldecode($rawuri);

        $host =$_SERVER['HTTP_HOST'];
        $fullUrl =$host.$uri;
        if (strstr($fullUrl, $host.'/api/')) {  //以api开头的网址
            $uri =str_replace('/api/', '/', $uri);
        }
       $this->routeInfos=  $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
        // 使用未定义格式路由
        case \FastRoute\Dispatcher::NOT_FOUND:

                //自动路由
                if (env('APP_AUTOROUTE')) {
                    $requris=explode('/', $uri);
                    $reqClass=   '\app\controllers\\' .   ucfirst($requris[1]).'Controller';
                    $reqMethod=$requris[2] ?? 'Index';
                    $reqVars=[];
                    foreach ($requris as $rk => $rv) {
                        if ($rk>=3) {
                            $reqVars[]=$rv;
                        }
                    }


                   $this->routeInfos[0] =0;
                   $this->routeInfos[1] =$reqClass .'@'.$reqMethod;
                   $this->routeInfos[2] =$reqVars;

                    $container = app();

                    
                    //控制器中间件
                    if (class_exists($reqClass)) {
                        $classref = new \ReflectionClass($reqClass);
                        if ($classref->hasProperty('middleware')) {
                            $reflectionProperty=  $classref->getProperty('middleware');
                            $reflectionProperty->setAccessible(true);
                            $md=  $reflectionProperty->getValue();
                            $md=$this->middleware($md, $reqMethod);
                        }
                    }

                    if (!class_exists($reqClass) || !method_exists($reqClass, $reqMethod)) {//回退路由
                        error(404, '404');
                        return '';
                    }

                    if (!empty($md)) {
                        return $this->runMiddleware($md, $container, $reqClass, $reqMethod, $reqVars);
                    } else {
                        return $this->runMiddleware([], $container, $reqClass, $reqMethod, $reqVars);

                        // return   $container::getInstance()->invoke([$reqClass,$reqMethod], ...[$reqVars]);
                    }
                    exit;
                }
                if (!env('APP_DEBUG')) {
                    error(404, '404');
                } else {
                    throw new \Whoops\Exception\ErrorException('未定义此路由或未在新建文件后使用composer dump-autoload');
                }
                break;
        /**
         * 请求的HTTP⽅法与配置的不符合
         * HTTP规范要求405 Method Not Allowed响应包含“Allow：”头，
         * 用以详细说明所请求资源的可用方法。
         * 使用FastRoute的应用程序在返回405响应时，
         * 应使用数组的第二个元素添加此标头。
         */
        case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                header('HTTP/1.1 405 Method Not Allowed');
                $allow = implode(',', $allowedMethods);
                header('Allow:' . $allow);
                $errorMsg = "请求方式非法，可使用的请求方式为： $allow";
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    error(405, $errorMsg);
                } else {
                   // error($errorMsg);
                    exit($errorMsg);
                }
                break;
        
        // 正常
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // var_dump($this->parameters,$vars);
                $this->parameters =$vars;
                if (!$handler instanceof \Closure) {
                    if (strstr($handler, '@', true)) {
                        list($handler, $method) = explode('@', $handler, 2);
                        if (strstr($method, '|')) {
                            $methods = explode('|', $method);
                            $method=$methods[0];
                            $middlewares=\array_slice($methods, 1);
                        }
                    }
        
                    if (strstr($handler, '|')) {
                        $handlers = explode('|', $handler);
                        $handler=$handlers[0];
                        $middlewares=\array_slice($handlers, 1);
                    }
                }

                if ($handler instanceof \Closure) {
                    $container = app();
                    return $this->runMiddleware([], $container, $handler, '', $vars);
                    // return     \call_user_func_array($handler, $vars);
                } else {
                    $class = '\app\controllers\\' . ucfirst($handler);
                    if (isset($method)) {
                        $action=$method;
                    } else {
                        $action =  ucfirst(isset($vars['action'])
                        ? $vars['action']
                        : 'index');
                    }
                
                    unset($vars['action']);
                    request()->controller =$class;
                    request()->action =$action;
                    $container = app();



                    //控制器中间件
                    $classref = new \ReflectionClass($class);
                    if ($classref->hasProperty('middleware')) {
                        $reflectionProperty=  $classref->getProperty('middleware');
                        $reflectionProperty->setAccessible(true);
                        $md=  $reflectionProperty->getValue();
                        $md=$this->middleware($md, $action);
                    }

                    $group_key= '/'.explode('\\', $routeInfo[1])[0];

                    if (isset($this->group_middlewares[$group_key])) {//分组路由中间件
                      
                        $group_middleware_array=[];
                        foreach ($this->group_middlewares[$group_key] as $key => $value) {
                            $group_middleware_array[]=$value;
                        }

                        if (isset($middlewares)) {//路由中间件
                            $middleware_array=[];
                            foreach ($middlewares as $key => $value) {
                                // if (strstr($value, '!')) {
                                //     foreach ($group_middleware_array as $gk => $gv) {
                                //         $valuename=explode('!', $value)[1];
                                //         if ($valuename ===$gv) {
                                //             unset($group_middleware_array[$gk]);
                                //         }
                                //     }
                                //     continue;
                                // }

                                $middleware_array[]=$value;
                            }
                        }

                        $group_middleware_all= array_unique(array_merge($group_middleware_array, $middleware_array ?? [], $md ?? []));
                        return $this->runMiddleware($group_middleware_all, $container, $class, $action, $vars);
                    }


                    if (isset($middlewares)) {//路由中间件
                        $middleware_array=[];
                        foreach ($middlewares as $key => $value) {
                            $middleware_array[]=$value;
                        }
                    
                        $middleware_all= array_unique(array_merge($middleware_array, $md ?? []));
                        return $this->runMiddleware($middleware_all, $container, $class, $action, $vars);
                    } else {
                        if (!empty($md)) {
                            return $this->runMiddleware($md, $container, $class, $action, $vars);
                        } else {
                        return $this->runMiddleware([], $container, $class, $action, $vars);

                            // if (empty($vars)) {
                            //     return   $container::getInstance()->invoke([$class,$action]);
                            // } else {
                            //     return    $container::getInstance()->invoke([$class,$action], $vars);
                            // }
                        }
                    }
                }
           
                break;
        }
    }

    //全局路由中间件 & 路由中间件
    public function runMiddleware($middlewares, $container, $class, $action, $vars)
    {

        $uri = $_SERVER['REQUEST_URI'];
        $host =$_SERVER['HTTP_HOST'];
        $fullUrl =$host.$uri;

        if (strstr($fullUrl, $host.'/api/')) { //api 全局路由中间件
            $type='api';
        } else {//web全局路由中间件
            $type='web';
        }
        $middleware =require BASE_PATH.'/config/middleware.php';

        $routeMiddleware =$middleware['routeMiddleware'];
         $middlewareGroups =$middleware['middlewareGroups'][$type];

        //路由中间件去重
        foreach ($middlewares as $key => &$value) {
            if (!class_exists($value)){
                if (strstr($value,'!')) {
                    $value=explode('!', $value)[1];
                    unset($middlewares[$key]);
                    foreach ($middlewareGroups as $k => $v) { //中间件排除
                        if (str_replace('::class','',$value) ===$v){
                            unset($middlewareGroups[$k]);
                        }
                    }
                }
             $value =$routeMiddleware[$value] ?? [];
             
            }
            if (array_search($value, $this->middlewareGroups[$type]) !==false) {
                unset($middlewares[$key]);
            }
        }


        //中间件排序
        $middlewaresSorted=  array_merge(array_flip($this->middlewarePriority), array_flip($middlewares));
        foreach ($middlewaresSorted as $k => $v) {
            if (array_search($k, $middlewares) ===false) {
                unset($middlewaresSorted[$k]);
            }
        }

        $middlewaresSorted=array_flip($middlewaresSorted);
        $middlewaresSorted =  array_merge($middlewareGroups ,$middlewaresSorted);

        $Pipeline = new Pipeline(app());
        $object = app('request');//Request::getInstance();
        $end=  $Pipeline->send($object)->through($middlewaresSorted) ->then(function ($object) use ($container, $class, $action, $vars) {

            foreach ($this->parameters as $pkey => $pvalue) {
                $this->parameters[$pkey.'model']=$pvalue;
                unset($this->parameters[$pkey]);
            }
            $vars =array_merge($this->parameters,$vars);
            if (empty($vars)) {
                if (empty($action)) {
                    $end=   $container::getInstance()->invoke($class);
                }else{
                    $end=   $container::getInstance()->invoke([$class,$action]);
                }
                if ($end instanceof Response) {
                    return   $end->toResponse($object, $end);
                } else {
                    return   $end;
                }
            } else {//

                if (empty($action)) {
                    $end=   $container::getInstance()->invoke($class,$vars);
                }else{
                    $end=   $container::getInstance()->invoke([$class,$action], $vars);
                }

                if ($end instanceof Response) {
                    return   $end->toResponse($object, $end);
                } else {
                    return   $end;
                }
            }
        });
        return $end;
    }
    
    public function middleware($middleware, $action)
    {
        foreach ((array) $middleware as $mkey => $mvalue) {
            $this->middleware[] = [
                'middleware' => $mkey,
               'options' => $mvalue,
            ];
        }

        $this->middleware_array=$this->getMiddlewareForMethod($action);
        return $this->middleware_array;
    }

    
    /**
     * Get the middleware for a given method.
     *
     * @param  string  $method
     * @return array
     */
    public function getMiddlewareForMethod($method)
    {
        $middleware = [];
        foreach ($this->middleware as $key => $value) {
            $options=$value['options'];
            $name=$value['middleware'];
            if (isset($options['only']) && ! \in_array($method, (array) $options['only'])) {
                continue;
            }
            if (isset($options['except']) && \in_array($method, (array) $options['except'])) {
                continue;
            }
            $middleware[] = $name;
        }

        return $middleware;
    }


     /**
     * Substitute the implicit Eloquent model bindings for the route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function substituteImplicitBindings($route)
    {
        // $route=[];
        $container = app();
        ImplicitRouteBinding::resolveForRoute($container, $route);
    }

    public function name($name)
    {
        self::setByName($name, $this);
        return $this;
    }


    /**
     * Get the parameters that are listed in the route / controller signature.
     *
     * @param  string|null  $subClass
     * @return array
     */
    public function signatureParameters($subClass = null)
    {
        return RouteSignatureParameters::fromAction($this->action, $subClass);
    }
    public function routeInfo()
    {
        return $this->routeInfos;
    }

       /**
     * Get the binding fields for the route.
     *
     * @return array
     */
    public function bindingFields()
    {
        return $this->bindingFields ?? [];
    }


    /**
     * Get the value of the action that should be taken on a missing model exception.
     *
     * @return \Closure|null
     */
    public function getMissing()
    {
        $missing = $this->action['missing'] ?? null;

        return is_string($missing) &&
            Str::startsWith($missing, 'C:32:"Opis\\Closure\\SerializableClosure')
                ? unserialize($missing)
                : $missing;
    }

      /**
     * Get the parent parameter of the given parameter.
     *
     * @param  string  $parameter
     * @return string
     */
    public function parentOfParameter($parameter)
    {
        $key = array_search($parameter, array_keys($this->parameters));

        if ($key === 0) {
            return;
        }

        return array_values($this->parameters)[$key - 1];
    }

    /**
     * Get the binding field for the given parameter.
     *
     * @param  string|int  $parameter
     * @return string|null
     */
    public function bindingFieldFor($parameter)
    {
        $fields = is_int($parameter) ? array_values($this->bindingFields) : $this->bindingFields;

        return $fields[$parameter] ?? null;
    }

     /**
     * Set a parameter to the given value.
     *
     * @param  string  $name
     * @param  string|object|null  $value
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters();

        $this->parameters[$name] = $value;
    }

    public function setParameterAll( $parameters)
    {
        // $this->parameters();

        $this->parameters  = $parameters;
    }
    
    /**
     * @param $name
     * @param RouteObject $instance
     */
    public static function setByName($name, $instance)
    {
        static::$_nameList[$name] = $instance;
    }
    /**
     * @param $name
     * @return null|RouteObject
     */
    public static function getByRouteName($name)
    {
        return static::$_nameList[$name] ?? null;
    }

    public function parameters()
    {
        if (isset($this->parameters)) {
            return $this->parameters;
        }

        throw new LogicException('Route is not bound.');
    }


    /**
     * @param $parameters
     * @return string
     */
    public function url($name, $parameters = [])
    {
        $path=$this->_path[$name] ?? $this->_path[0];
        if (empty($parameters)) {
            return $path;
        }
        return preg_replace_callback('/\{(.*?)(?:\:[^\}]*?)*?\}/', function ($matches) use ($parameters) {
            if (isset($parameters[$matches[1]])) {
                return $parameters[$matches[1]];
            }
            return $matches[0];
        }, $path);
    }
    public function resource($EnglishInflector, $name, $callable, $r, $isGroup=false, $groupName=null)
    {
        $name_arr=explode('.', $name);
        $id=   $EnglishInflector->singularize($name_arr[0])[0]; //将单词复数变单数
        $routeName=$name;
        $routePath=$name;
        if (strstr($name, '.')) {//资源嵌套
            $aname=    $id;
            $id =  $EnglishInflector->singularize($name_arr[1])[0]; //将单词复数变单数

            $routePath =$name_arr[0]."/{".$aname."}/".$name_arr[1].'';
            $name =$name_arr[0]."/{".$aname."}/".$name_arr[1].'';
        }

        if (!empty($groupName)) {
            $groupName =str_replace('/', '', $groupName);
            $routeName =$groupName.'.'.$name;
            $routePath =$groupName.'/'.$name;
            $callable =$groupName.'\\'.$callable;
        }
         
        $r->addRoute('GET', '/'.$name, $callable);
        $this->name($routeName.'.index');
        $this->_path[$routeName.'.index']=  '/'.$routePath;

        $r->addRoute('GET', '/'.$name.'/create', $callable.'@create');
        $this->name($routeName.'.create');
        $this->_path[$routeName.'.create']=  '/'.$routePath.'/create';

        $r->addRoute('POST', '/'.$name, $callable.'@save');
        $this->name($routeName.'.save');
        $this->_path[$routeName.'.save']=  '/'.$routePath;

        $r->addRoute('GET', '/'.$name.'/{'.$id.'}', $callable.'@show');
        $this->name($routeName.'.show');
        $this->_path[$routeName.'.show']= '/'.$routePath.'/{'.$id.'}';

        $r->addRoute('GET', '/'.$name.'/{'.$id.'}/edit', $callable.'@edit');
        $this->name($routeName.'.edit');
        $this->_path[$routeName.'.edit']= '/'.$routePath.'/{'.$id.'}/edit';

        $r->addRoute('PUT', '/'.$name.'/{'.$id.'}', $callable.'@update');
        $r->addRoute('PATCH', '/'.$name.'/{'.$id.'}', $callable.'@update');
        $this->name($routeName.'.update');
        $this->_path[$routeName.'.update']= '/'.$routePath.'/{'.$id.'}';

        $r->addRoute('DELETE', '/'.$name.'/{'.$id.'}', $callable.'@delete');
        $this->name($routeName.'.delete');
        $this->_path[$routeName.'.delete']= '/'.$routePath.'/{'.$id.'}';
    }
}
