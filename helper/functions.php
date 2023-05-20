<?php
use WondPHP\App;

use WondPHP\Config as Config;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler as StreamHandler;
use WondPHP\Response as Response;
use WondPHP\Request as Request;
use WondPHP\Exceptions\HttpException;
use WondPHP\Exceptions\HttpResponseException;
use WondPHP\View as View;
use WondPHP\Route as Route;
use Illuminate\Translation\FileLoader;
use WondPHP\Facades\Event as Event;
use WondPHP\Container as Container;
use WondPHP\HigherOrderTapProxy as HigherOrderTapProxy;
use Illuminate\Support\HtmlString;

function error($errno = '', $errstr = '')
{
    $errstr = empty($errno)
        ? '系统繁忙，请稍后再试'
        : $errstr;
    // $_SESSION['errno'] = $errno;
    // $_SESSION['error'] = $errstr;
    try {
        // ErrorController::show($errno, $errstr);
        // throw new Exception('eeeeeeeeeeeee');
        http_response_code($errno);
        echo $errstr;
    } catch (ErrorException $e) {
    }
}

function config1111($name)
{
    $name =explode('.', $name);
    $config_name1 =$name[1] ?? '';
    $config_name2=  $name[2] ?? '';
    if (empty($config_name2)) {
        $config_names = $config_name1;
    } else {
        $config_names = $config_name1 .'.' .$config_name2;
    }
    $config= Config::getInstance();
    $config->load($name[0]);
    return $config->get($config_names);
}


 
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|\Illuminate\Config\Repository
     */
    function config($key = null, $default = null)
    {
        // debug_print_backtrace();
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
 



/**
 * Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
 *
 */
function logs($msg, $type='error')
{
    Log::$type($msg);
}

function response($body = '', $status = 200, $headers = array())
{
    // return new Response($status, $headers, $body);
    // return Response::getInstance($status, $headers, $body);

    return Container::getInstance()->invokeClass('WondPHP\Response', [$status, $headers, $body]);

    // return Response::getInstance($status, $headers, $body);
}

function cookie($key, $value, $age=0)
{
    return  response()->cookie($key, $value, $age);
}
/**
 * @param $data
 * @param int $options
 * @return Response
 */
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}

/**
 * @param $xml
 * @return Response
 */
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}

/**
 * @param $data
 * @param string $callback_name
 * @return Response
 */
function jsonp($data, $callback_name = 'callback')
{
    if (!\is_scalar($data) && null !== $data) {
        $data = json_encode($data);
    }
    return new Response(200, [], "$callback_name($data)");
}

function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location], '');
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}

/**
 * @param $template
 * @param array $vars
 * @param null $app
 * @return string
 */
function view($view = null, $data = [], $mergeData = [])
{
    // static $handler;    //TODO
    // if (null === $handler) {
    //     $handler =  View::class;// config('view.handler');
    // }
    // return new Response(200, [], $handler::make($template,$vars));


    $factory = app(View::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);


    
}

if (! function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     *
     * @return \Illuminate\Support\HtmlString
     */
    function csrf_field()
    {
        return new HtmlString('<input type="hidden" name="_token" value="'.csrf_token().'">');
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function csrf_token()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
    }
}


/**
 * @param $name
 * @param array $parameters
 * @return string
 */
function route($name, $parameters = [])
{
    $route = Route::getByRouteName($name);
    if (!$route) {
        return $name;
    }
    return $route->url($name,$parameters);
}
 
/**
 * @param null $key
 * @param null $default
 * @return mixed
 */
function session($key = null, $default = null)
{
    $session = request()->session();
    if (null === $key) {
        return $session;
    }
    if (\is_array($key)) {
        $session->put($key);
        return null;
    }
    return $session->get($key, $default);
}

/**
 * 读写大二进制文件，不必申请很大内存
 * 只有读取到内容才创建文件
 * 保证目录可写
 *
 * @param string $srcPath 源文件路径
 * @param string $dstPath 目标文件路径
 * @return bool
 */
function fetch_big_file($srcPath)
{
    set_time_limit(0); // 设置脚本执行时间无限长
 
    if (!$fpSrc = fopen($srcPath, "rb")) {
        return false;
    }
 
    $isWriteFileOpen = false; // 写文件 是否已打开？
    do {
        $data = fread($fpSrc, 8192); // 每次读取 8*1024个字节
        if (!$data) {
            break;
        }
    } while (true);
 
    fclose($fpSrc);
 
    return $data;
}

function download_file($filename, $downname='')
{
    //Check the file exists or not
    if (file_exists($filename)) {
        $savename=empty($downname) ?$filename:$downname;
        //Define header information
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header('Content-Disposition: attachment; filename="'.basename($savename).'"');
        header('Content-Length: ' . filesize($filename));
        header('Pragma: public');
    
        //Clear system output buffer
        flush();
        ob_end_flush();
        //Read the size of the file
        readfile($filename);
    
        //Terminate from the script
        die();
    } else {
        echo "File does not exist.";
    }
}


if (!function_exists('envs')) {
    /**
     * @param $key
     * @param null $default
     * @return array|bool|false|mixed|string
     */
    function envs($key, $default = null)
    {
        $apcu_key="env$key";
        if (apcu_exists($apcu_key)) {
            $apcu_value= apcu_fetch($apcu_key);

            switch (strtolower($apcu_value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
                    default:
                    return $apcu_value;
            }
        }
       
        $value = getenv($key);
        apcu_store($apcu_key, $value, 60);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = \strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
/**
* 获取HTTP请求原文
* @return string
*/
function get_http_raw()
{
    $raw = '';
    // (1) 请求行
    $raw .= $_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'].' '.$_SERVER['SERVER_PROTOCOL']."\r\n";
    // (2) 请求Headers
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $key = substr($key, 5);
            $key = str_replace('_', '-', $key);
            $raw .= $key.': '.$value."\r\n";
        }
    }
    // (3) 空行
    $raw .= "\r\n";
    // (4) 请求Body  如果请求header里Contente-Type是 multipart/form-data,或application/x-www-form-urlencoded或application/octet-stream则需要用 $_POST($_GET)或$_FILES来接收.
    $raw .= file_get_contents('php://input');
    // var_dump($raw);exit;
    return $raw;
}

/**
 * @return Request
 */
function request()
{
    return app('request'); 

    return Request::getInstance();
    // return new Request();
}


function in_multiarray($elem, $array)
{
    $top = \sizeof($array) - 1;
    $bottom = 0;
    while ($bottom <= $top) {
        if ($array[$bottom] == $elem) {
            return true;
        } elseif (\is_array($array[$bottom])) {
            if (in_multiarray($elem, ($array[$bottom]))) {
                return true;
            }
        }
                   
        $bottom++;
    }
    return false;
}

    /*
 * This file is part of the php-defer/php-defer package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function delay(?SplStack &$context, callable $callback): void
{
    $context = $context ?? new SplStack();

    $context->push(
        new class($callback) {
            private $callback;

            public function __construct(callable $callback)
            {
                $this->callback = $callback;
            }

            public function __destruct()
            {
                \call_user_func($this->callback);
            }
        }
    );
}

function __($key = null, $replace = [], $locale = null)
{
    if (\is_null($key)) {
        return $key;
    }

    return trans($key, $replace, $locale);
}

    function trans($key = null, $replace = [], $locale = null)
    {
        if (apcu_exists($key)) {
            // apcu_delete($key);
            return apcu_fetch($key);
        }
        if (\is_null($key)) {
            return app('translator');
        }
        $result = app('translator')->get($key, $replace, $locale);
        apcu_store($key, $result, 60);
        return $result;
    }

    function app($abstract = null, array $parameters = [])
    {
        if (\is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }

    if (!function_exists('app')) {
        /**
         * 快速获取容器中的实例 支持依赖注入
         * @param string $name        类名或标识 默认获取当前应用实例
         * @param array  $args        参数
         * @param bool   $newInstance 是否每次创建新的实例
         * @return object|App
         */
        function app_new(string $name = '', array $args = [], bool $newInstance = false)
        {
            return Container::getInstance()->make($name ?: App::class, $args, $newInstance);
        }
    }

    if (! function_exists('method_field')) {
        /**
         * Generate a form field to spoof the HTTP verb used by forms.
         *
         * @param  string  $method
         * @return \Illuminate\Support\HtmlString
         */
        function method_field($method)
        {
            return new HtmlString('<input type="hidden" name="_method" value="'.$method.'">');
        }
    }
    

    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    function event(...$args)
    {
        // [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
        // app()->get("Events");
        return    Event::dispatch(...$args);
        // return app('Events')->dispatch(...$args);
    }

    if (! function_exists('tap')) {
        /**
         * Call the given Closure with the given value then return the value.
         *
         * @param  mixed  $value
         * @param  callable|null  $callback
         * @return mixed
         */
        function tap($value, $callback = null)
        {
            if (\is_null($callback)) {
                return new HigherOrderTapProxy($value);
            }
    
            $callback($value);
    
            return $value;
        }
    }



if (!function_exists('abort1')) {
    /**
     * 抛出HTTP异常
     * @param integer|Response $code    状态码 或者 Response对象实例
     * @param string           $message 错误信息
     * @param array            $header  参数
     */
    function abort1($code, string $message = '', array $header = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } else {
            throw new HttpException($code, $message, null, $header);
        }
    }
}

if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string  $path
     * @return string
     */
    function storage_path($path = '')
    {
        return app('path.storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}



if (! function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort($code, $message = '', array $headers = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } elseif ($code instanceof Responsable) {
            throw new HttpResponseException($code->toResponse(request()));
        }

        app()->abort($code, $message, $headers);
    }
}


