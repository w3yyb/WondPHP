<?php
namespace WondPHP;

use WondPHP\Route as Route;
use WondPHP\Contracts\App as AppContracts;
use WondPHP\Exceptions\NotFoundHttpException;
use WondPHP\Exceptions\HttpException;
use Illuminate\Support\Str;
class App extends Container implements AppContracts
{
    const VERSION = '1.0.0';
     


    /**
     * 应用根目录
     * @var string
     */
    protected $rootPath = '';
    protected $isRunningInConsole;

    /**
     * 框架目录
     * @var string
     */
    protected $WondPath = '';

    /**
     * 应用目录
     * @var string
     */
    protected $appPath = '';
    
    protected $publicPath = '';
    protected $resourcesPath = '';
    protected $langPath = '';

    /**
     * Runtime目录
     * @var string
     */
    protected $runtimePath = '';
    protected $environmentFile = '.env';
    protected $namespace;
    /**
     * 基础根目录
     * @var string
     */
    protected $basePath='';

    protected $bootstrappers = [
        DotEnv::class,
        LoadConfiguration::class,
        ErrorHandel::class,

        // \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        // \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        // \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];
    protected $providers ;
    /**
         * The application's global HTTP middleware stack.
         *
         * These middleware are run during every request to your application.
         *
         * @var array
         */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware =[];

     


    /**
         * 架构方法
         * @access public
         * @param string $rootPath 应用根目录
         */
    public function __construct(string $rootPath = '')
    {

        $this->WondPath   = __DIR__ . DIRECTORY_SEPARATOR;
        $this->rootPath    = $rootPath ? rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : $this->getDefaultRootPath();
        $this->appPath     = $this->rootPath . 'app' . DIRECTORY_SEPARATOR;
        $this->runtimePath = $this->rootPath . 'cache' . DIRECTORY_SEPARATOR;
        $this->publicPath = $this->rootPath . 'public' . DIRECTORY_SEPARATOR;
        $this->resourcesPath = $this->rootPath . 'resources' . DIRECTORY_SEPARATOR;
        $this->langPath = $this->rootPath . 'resources' . DIRECTORY_SEPARATOR.'lang'. DIRECTORY_SEPARATOR;


if ($rootPath) {//yuan
            // $this->setBasePath($basePath);
        }

       // if ($rootPath || BASE_PATH) {
             $this->setBasePath(BASE_PATH);
        //}
        // if (is_file($this->appPath . 'provider.php')) {
        //     // $this->bind(include $this->appPath . 'provider.php');
        // }


        $this->registerBaseBindings();
        $this->registerAlias();
        $this->bootstrap();
        // $this->registerBaseServiceProviders();
    }


    public function bootstrap()
    {
        // $this->make(LoadEnvironmentVariables::class);

        // $this->hasBeenBootstrapped = true;
        foreach ($this->bootstrappers as $bootstrapper) {
            $this->make($bootstrapper);
        }

    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        $this->container =$this;
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance('WondPHP\Container', $this);


        //自动创建request对象
        $request =   $this->app->make(\WondPHP\Request::class, []);
        $this->app->instance('request', $request);

    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
    }

    /**
         * Get the path to the configuration cache file.
         *
         * @return string
         */
    public function getCachedConfigPath()
    {
        return $this->normalizeCachePath('APP_CONFIG_CACHE', 'cache/config.php');
    }

    /**
      * Set the base path for the application.
      *
      * @param  string  $basePath
      * @return $this
      */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->bindPathsInContainer();
        return $this;
    }

    /**
     * Detect the application's current environment.
     *
     * @param  \Closure  $callback
     * @return string
     */
    public function detectEnvironment(\Closure $callback)
    {
        $args = $_SERVER['argv'] ?? null;

        return $this['env'] = (new EnvironmentDetector)->detect($callback, $args);
    }

    /**
     * Bind all of the application paths in the container.    :  laravel
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.lang', $this->langPath());

        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());

        $this->instance('path.storage', $this->storagePath());
        $this->instance('path.database', $this->databasePath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());

    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param  string  $path
     * @return string
     */
    public function path($path = '')
    {
        $appPath = $this->appPath ?: $this->basePath.DIRECTORY_SEPARATOR.'app';

        return $appPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Set the application directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useAppPath($path)
    {
        $this->appPath = $path;

        $this->instance('path', $path);

        return $this;
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param  string  $path Optionally, a path to append to the base path
     * @return string
     */
    public function basePath($path = '')
    {
        if($path){
            $path = DIRECTORY_SEPARATOR.$path;
        }
        return $this->basePath.$path;
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param  string  $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        if($path){
            $path = DIRECTORY_SEPARATOR.$path;
        }
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.$path;
    }

    public function runningInConsole()
    {
        if ($this->isRunningInConsole === null) {
            $this->isRunningInConsole = env('APP_RUNNING_IN_CONSOLE') ?? (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg');
        }

        return $this->isRunningInConsole;
    }
    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        if($path){
            $path = DIRECTORY_SEPARATOR.$path;
        }
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.$path;
    }

    /**
     * Get the path to the database directory.
     *
     * @param  string  $path Optionally, a path to append to the database path
     * @return string
     */
    public function databasePath($path = '')
    {
        if($path){
            $path = DIRECTORY_SEPARATOR.$path;
        }
        return ($this->databasePath ?? $this->basePath.DIRECTORY_SEPARATOR.'database').$path;
    }

    /**
     * Set the database directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useDatabasePath($path)
    {
        $this->databasePath = $path;

        $this->instance('path.database', $path);

        return $this;
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public function langPath()
    {
        return $this->resourcePath().DIRECTORY_SEPARATOR.'lang';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?? $this->basePath.DIRECTORY_SEPARATOR.'cache';
    }

    /**
     * Set the storage directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function useStoragePath($path)
    {
        $this->storagePath = $path;

        $this->instance('path.storage', $path);

        return $this;
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string  $path
     * @return string
     */
    public function resourcePath($path = '')
    {
        if($path){
            $path = DIRECTORY_SEPARATOR.$path;
        }

        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.$path;
    }

    /**
     * Get the path to the environment file directory.
     *
     * @return string
     */
    public function environmentPath()
    {
        return $this->environmentPath ?? $this->basePath;
    }

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public function getLocale()
    {
        $this['config']->load('app');

        return $this['config']->get('locale');
    }

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public function currentLocale()
    {
        return $this->getLocale();
    }

    /**
    * Get the version number of the application.
    *
    * @return string
    */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * Get the current application fallback locale.
     *
     * @return string
     */
    public function getFallbackLocale()
    {
        $this['config']->load('app');

        return $this['config']->get('fallback_locale');
    }

    /**
     * Set the current application locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this['config']->set(['locale' => $locale], 'app');

        $this['translator']->setLocale($locale);

        // $this['events']->dispatch(new LocaleUpdated($locale)); /TODO
    }

    /**
     * Set the current application fallback locale.
     *
     * @param  string  $fallbackLocale
     * @return void
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this['config']->set('app.fallback_locale', $fallbackLocale);

        $this['translator']->setFallback($fallbackLocale);
    }

    /**
     * Determine if application locale is the given locale.
     *
     * @param  string  $locale
     * @return bool
     */
    public function isLocale($locale)
    {
        return $this->getLocale() == $locale;
    }

      /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: '.env';
    }

    public function registerAlias()
    {
        //bind core aliases

        $aliases =[


        //leravel 门面
        'App' => \WondPHP\Facades\App::class,
        'Request' => \WondPHP\Facades\Request::class,
        'Lang' => \WondPHP\Facades\Lang::class,
        'Session' => \WondPHP\Facades\Session::class,
        'Config' => \WondPHP\Facades\Config::class,
        'DB' => \WondPHP\Facades\DB::class,
        'Cache' => \WondPHP\Facades\Cache::class,
        'File' => \WondPHP\Facades\File::class,
        'Redis' => \WondPHP\Facades\Redis::class,
        'Log' => \WondPHP\Facades\Log::class,
        'Event' => \WondPHP\Facades\Event::class,
        'Route' => \WondPHP\Facades\Route::class,
        'Response' => \WondPHP\Facades\Response::class,
        'View' => \WondPHP\Facades\View::class,
        'Cookie' => \WondPHP\Facades\Cookie::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Str' => Illuminate\Support\Str::class,
        'Hash' => \WondPHP\Facades\Hash::class,
        'Crypt' => \WondPHP\Facades\Crypt::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
         'Http' => \WondPHP\Facades\Http::class,



        ];

        $core_aliases= [
            //   'cache66'          => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class, \Psr\SimpleCache\CacheInterface::class],
    //第3个参数true是单例
                'request'                => [\WondPHP\Request::class, \WondPHP\Contracts\Request::class],
                'translator'=>[\WondPHP\Translation::class,\WondPHP\Contracts\Translation::class,true],
                'session'=>[\WondPHP\Session::class,\WondPHP\Contracts\Session::class,true],
                'config'=>[\WondPHP\Config::class,\WondPHP\Contracts\Config::class],
                'db'=>[\WondPHP\Db::class,\WondPHP\Contracts\DB::class,true],
                'cache'=>[\WondPHP\Cache::class,\WondPHP\Contracts\Cache::class,true],
                'files'=>[\WondPHP\File::class,\WondPHP\Contracts\File::class,true],
                'redis'=>[\WondPHP\Redis::class,\WondPHP\Contracts\Redis::class,true],
                'log'=>[\WondPHP\Log::class,\WondPHP\Contracts\Log::class,true],
                'events'=>[\WondPHP\Events::class,\WondPHP\Contracts\Event::class,true],
                'app'=>[\WondPHP\App::class,\WondPHP\Contracts\App::class],
                'router'=>[\WondPHP\Route::class,\WondPHP\Contracts\Route::class,true],
                'response'=>[\WondPHP\Response::class,\WondPHP\Contracts\Response::class],// to测试是否对原来的response有影响
                'view'=>[\WondPHP\View::class,\WondPHP\Contracts\View::class,true],
                'cookie'=>[\WondPHP\Cookie::class,\WondPHP\Contracts\Cookie::class,true],
                'validator'=>[\WondPHP\Validator::class,\WondPHP\Contracts\Validator::class,true],
                'hash'=>[\WondPHP\Hashing\HashManager::class,\WondPHP\Contracts\Hash::class],
                'encrypter'            => [\WondPHP\Encrypter::class, \WondPHP\Contracts\Encrypter::class,true],
                'http'            => [\WondPHP\Http::class, \WondPHP\Contracts\Http::class,true],
                'blade.compiler'       => [\Illuminate\View\Compilers\BladeCompiler::class],

    
    
    
        ];

        foreach ($core_aliases as $k => $v) {

          



            // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the application, which
        // serves as a convenience layer while registering a lot of bindings.

        // if (property_exists($provider, 'bindings')) {
        //     foreach ($provider->bindings as $key => $value) {
        //         $this->bind($key, $value);
        //     }
        // }

        // if (property_exists($v[0], 'singletons')) {//此会造成非异步加载 TODO 1
        //     foreach ($v[0]->singletons as $key => $value) {
        //         $this->singleton($key, $value);
        //     }
        // }





            $implement=$v[0];
            $contracts =$v[1] ?? null;

            // var_dump($v);

            if (isset($v[2]) && $v[2]===true) {
            $this::getInstance()->bind($k, $contracts,true);  //给这个接口一个别名
            } else {
            $this::getInstance()->bind($k, $contracts);  //给这个接口一个别名
            }



            if ($contracts) {
                if (isset($v[2]) && $v[2]===true) {
                $this::getInstance()->bind($contracts, $implement,true);
                    } else {
                        $this::getInstance()->bind($contracts, $implement);
                    }


            }  //将Contract接口和它的实现类绑定
        }
        //异步加载 facade aliases
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this);
        AliasLoader::getInstance(array_merge(
            $aliases
        ))->register();
    }


    //全局中间件
    public function handle()
    {
        $middleware =require BASE_PATH.'/config/middleware.php';
        $this->middleware =$middleware['middleware'];
        // $this->middlewareGroups =$middleware['middlewareGroups'];
        // $this->routeMiddleware =$middleware['routeMiddleware'];
        $app=$this;
        return    (new Pipeline($app))->send($app->request)->through($this->middleware)
        ->then(function ($request) use ($app) {
            return $app->run($request);
        });
    }


    public function run($request)
    {
        $this->providers =[[app('router'), 'run']];
        $providers =    array_merge($this->providers, include BASE_PATH.'/config/providers.php');
        
        foreach ($providers as $key => $value) {
            if ($value instanceof \Closure) {
                if (\is_string($key)) {
                    $value = $this::getInstance()->bind($key, $value); // delay
                } else {
                    $value = $this::getInstance()->invokeFunction($value);
                }
            } else {
                if (\is_array($value)) {
                    if ($value[0] instanceof Route) {
                        $response =$this::getInstance()->invokeMethod($value);
                        return  response()->toResponse($request, $response);// $response;
                    } else {
                        $this::getInstance()->invokeMethod($value);
                    }
                } else {
                    $this::getInstance()->make($value);
                }
            }
        }
    }
 
    /**
     * 获取应用根目录
     * @access protected
     * @return string
     */
    protected function getDefaultRootPath(): string
    {
        return dirname($this->WondPath, 2) . DIRECTORY_SEPARATOR;
    }

    //todo
    public function getEnvironment()
    {
        return 'dev';
    }

  /**
     * Determine if the application configuration is cached.
     *
     * @return bool
     */
    public function configurationIsCached()
    {
        return is_file($this->getCachedConfigPath());
    }
 

    public function getContainer()
    {
        return $this->container;
    }

    public function boot()
    {
    }
    public function isDebug()
    {
        return DEBUG;
    }

     /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }
        $composer = json_decode(file_get_contents($this->basePath('composer.json')), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath($this->path()) === realpath($this->basePath($pathChoice))) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }

/**
     * Get or check the current application environment.
     *
     * @param  string|array  $environments
     * @return string|bool
     */
    public function environment(...$environments)
    {
        if (count($environments) > 0) {
            $patterns = is_array($environments[0]) ? $environments[0] : $environments;

            return Str::is($patterns, env("APP_ENV"));
        }

        return $this['env'];
    }

    public function isProduction()
    {
        return $this['env'] === 'production';
    }

 /**
     * Normalize a relative or absolute path to a cache file.
     *
     * @param  string  $key
     * @param  string  $default
     * @return string
     */
    protected function normalizeCachePath($key, $default)
    {
        if (is_null($env = env($key))) {
            return $this->bootstrapPath($default);
        }

        return Str::startsWith($env, $this->absoluteCachePathPrefixes)
                ? $env
                : $this->basePath($env);
    }

    /**
    * Throw an HttpException with the given data.
    *
    * @param  int  $code
    * @param  string  $message
    * @param  array  $headers
    * @return void
    *
    * @throws \Symfony\Component\HttpKernel\Exception\HttpException
    * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
    */
    public function abort($code, $message = '', array $headers = [])
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }
}
