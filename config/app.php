<?php
return [

    'debug' => env('APP_DEBUG', false),

     



    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

/*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',


    'aliases' => [///////////此无用了，放到App.php中了




        // thinkphp: 门面
         // -rwxrwxrwx 1 lenix lenix 2606 1月  25 22:48 Console.php
        // -rwxrwxrwx 1 lenix lenix 1714 1月  25 22:48 Env.php
        // -rwxrwxrwx 1 lenix lenix 1786 1月  25 22:48 Middleware.php
         // -rwxrwxrwx 1 lenix lenix 6441 1月  25 22:48 Validate.php
        




        //leravel 门面
        'App' => WondPHP\Facades\App::class,
        'Request' => WondPHP\Facades\Request::class,
        'Lang' => WondPHP\Facades\Lang::class,
        'Session' => WondPHP\Facades\Session::class,
        'Config' => WondPHP\Facades\Config::class,
        'DB' => WondPHP\Facades\DB::class,
        'Cache' => WondPHP\Facades\Cache::class,
        'File' => WondPHP\Facades\File::class,
        'Redis' => WondPHP\Facades\Redis::class,
        'Log' => WondPHP\Facades\Log::class,
        'Event' => WondPHP\Facades\Event::class,
        'Route' => WondPHP\Facades\Route::class,
        'Response' => WondPHP\Facades\Response::class,
        'View' => WondPHP\Facades\View::class,
        'Cookie' => WondPHP\Facades\Cookie::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Str' => Illuminate\Support\Str::class,
        'Hash' => WondPHP\Facades\Hash::class,
        'Crypt' => WondPHP\Facades\Crypt::class,
       'Blade' => Illuminate\Support\Facades\Blade::class,



        // 'Artisan' => Illuminate\Support\Facades\Artisan::class,
        // 'Auth' => Illuminate\Support\Facades\Auth::class,
        // 'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        // 'Bus' => Illuminate\Support\Facades\Bus::class,
         // 'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        // 'Gate' => Illuminate\Support\Facades\Gate::class,
         // 'Http' => Illuminate\Support\Facades\Http::class,
        // 'Mail' => Illuminate\Support\Facades\Mail::class,
        // 'Notification' => Illuminate\Support\Facades\Notification::class,
        // 'Password' => Illuminate\Support\Facades\Password::class,
        // 'Queue' => Illuminate\Support\Facades\Queue::class,
        // 'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Schema' => Illuminate\Support\Facades\Schema::class,
        // 'Storage' => Illuminate\Support\Facades\Storage::class,------
        // 'URL' => Illuminate\Support\Facades\URL::class,
        // 'Validator' => Illuminate\Support\Facades\Validator::class,
        



    /*
Facade	                Class	             Service Container Binding
Artisan	Illuminate\Contracts\Console\Kernel	artisan
Auth	Illuminate\Auth\AuthManager	auth
Auth (Instance)	Illuminate\Contracts\Auth\Guard	auth.driver
Blade	Illuminate\View\Compilers\BladeCompiler	blade.compiler
Broadcast	Illuminate\Contracts\Broadcasting\Factory	 
Broadcast (Instance)	Illuminate\Contracts\Broadcasting\Broadcaster	 
Bus	Illuminate\Contracts\Bus\Dispatcher	 
Cache (Instance)	Illuminate\Cache\Repository	cache.store
Crypt	Illuminate\Encryption\Encrypter	encrypter
Date	Illuminate\Support\DateFactory	date
DB (Instance)	Illuminate\Database\Connection	db.connection
Gate	Illuminate\Contracts\Auth\Access\Gate	 
Hash	Illuminate\Contracts\Hashing\Hasher	hash
Http	Illuminate\Http\Client\Factory	 
Mail	Illuminate\Mail\Mailer	mailer
Notification	Illuminate\Notifications\ChannelManager	 
Password	Illuminate\Auth\Passwords\PasswordBrokerManager	auth.password
Password (Instance)	Illuminate\Auth\Passwords\PasswordBroker	auth.password.broker
Queue	Illuminate\Queue\QueueManager	queue
Queue (Instance)	Illuminate\Contracts\Queue\Queue	queue.connection
Queue (Base Class)	Illuminate\Queue\Queue	 
Redirect	Illuminate\Routing\Redirector	redirect
Redis (Instance)	Illuminate\Redis\Connections\Connection	redis.connection
Response (Instance)	Illuminate\Http\Response	 
Schema	Illuminate\Database\Schema\Builder	 
Session (Instance)	Illuminate\Session\Store	session.store
Storage	Illuminate\Filesystem\FilesystemManager	filesystem
Storage (Instance)	Illuminate\Contracts\Filesystem\Filesystem	filesystem.disk
URL	Illuminate\Routing\UrlGenerator	url
Validator	Illuminate\Validation\Factory	validator
Validator (Instance)	Illuminate\Validation\Validator	 
View (Instance)	Illuminate\View\View	 
    */




        
        ],

        //契约与实现绑定 //////////此无用了，放到App.php中了
        'core_aliases' => [
        //   'cache66'          => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class, \Psr\SimpleCache\CacheInterface::class],

            'request'                => [WondPHP\Request::class, WondPHP\Contracts\Request::class],
            'translator'=>[WondPHP\Translation::class,WondPHP\Contracts\Translation::class],
            'session'=>[WondPHP\Session::class,WondPHP\Contracts\Session::class],
            'config'=>[WondPHP\ConfigRepository::class,WondPHP\Contracts\Config::class],
            'db'=>[WondPHP\Db::class,WondPHP\Contracts\DB::class],
            'cache'=>[WondPHP\Cache::class,WondPHP\Contracts\Cache::class],
            'files'=>[WondPHP\File::class,WondPHP\Contracts\File::class],
            'redis'=>[WondPHP\Redis::class,WondPHP\Contracts\Redis::class],
            'log'=>[WondPHP\Log::class,WondPHP\Contracts\Log::class],
            'events'=>[WondPHP\Events::class,WondPHP\Contracts\Event::class],
            'app'=>[WondPHP\App::class,WondPHP\Contracts\App::class],
            'router'=>[WondPHP\Route::class,WondPHP\Contracts\Route::class],
            'response'=>[WondPHP\Response::class,WondPHP\Contracts\Response::class],// to测试是否对原来的response有影响
            'view'=>[WondPHP\View::class,WondPHP\Contracts\View::class], 
            //  'view'                 => [\Illuminate\View\Factory::class, \Illuminate\Contracts\View\Factory::class],
            'cookie'=>[WondPHP\Cookie::class,WondPHP\Contracts\Cookie::class],
            'validator'=>[WondPHP\Validator::class,WondPHP\Contracts\Validator::class],
            'hash'=>[WondPHP\Hashing\HashManager::class,WondPHP\Contracts\Hash::class],
            'encrypter'            => [WondPHP\Encrypter::class, WondPHP\Contracts\Encrypter::class],
            'blade.compiler'       => [\Illuminate\View\Compilers\BladeCompiler::class],



        ],

        //leravel
        'core_aliases11' => [
            // 'app'                  => [self::class, \Illuminate\Contracts\Container\Container::class, \Illuminate\Contracts\Foundation\Application::class, \Psr\Container\ContainerInterface::class],
            'auth'                 => [\Illuminate\Auth\AuthManager::class, \Illuminate\Contracts\Auth\Factory::class],
            'auth.driver'          => [\Illuminate\Contracts\Auth\Guard::class],
            // 'blade.compiler'       => [\Illuminate\View\Compilers\BladeCompiler::class],
            // 'cache'                => [\Illuminate\Cache\CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
            // 'cache.store'          => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class, \Psr\SimpleCache\CacheInterface::class],
            // 'cache.psr6'           => [\Symfony\Component\Cache\Adapter\Psr16Adapter::class, \Symfony\Component\Cache\Adapter\AdapterInterface::class, \Psr\Cache\CacheItemPoolInterface::class],
            // 'config'               => [\Illuminate\Config\Repository::class, \Illuminate\Contracts\Config\Repository::class],
            // 'cookie'               => [\Illuminate\Cookie\CookieJar::class, \Illuminate\Contracts\Cookie\Factory::class, \Illuminate\Contracts\Cookie\QueueingFactory::class],
            // 'encrypter'            => [\Illuminate\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\Encrypter::class],
            // 'db'                   => [\Illuminate\Database\DatabaseManager::class, \Illuminate\Database\ConnectionResolverInterface::class],
            // 'db.connection'        => [\Illuminate\Database\Connection::class, \Illuminate\Database\ConnectionInterface::class],
            // 'events'               => [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
            // 'files'                => [\Illuminate\Filesystem\Filesystem::class],
            // 'filesystem'           => [\Illuminate\Filesystem\FilesystemManager::class, \Illuminate\Contracts\Filesystem\Factory::class],
            // 'filesystem.disk'      => [\Illuminate\Contracts\Filesystem\Filesystem::class],
            // 'filesystem.cloud'     => [\Illuminate\Contracts\Filesystem\Cloud::class],
            // 'hash'                 => [\Illuminate\Hashing\HashManager::class],
            'hash.driver'          => [\Illuminate\Contracts\Hashing\Hasher::class],
            // 'translator'           => [\Illuminate\Translation\Translator::class, \Illuminate\Contracts\Translation\Translator::class],
            // 'log'                  => [\Illuminate\Log\LogManager::class, \Psr\Log\LoggerInterface::class],
            'mail.manager'         => [\Illuminate\Mail\MailManager::class, \Illuminate\Contracts\Mail\Factory::class],
            'mailer'               => [\Illuminate\Mail\Mailer::class, \Illuminate\Contracts\Mail\Mailer::class, \Illuminate\Contracts\Mail\MailQueue::class],
            'auth.password'        => [\Illuminate\Auth\Passwords\PasswordBrokerManager::class, \Illuminate\Contracts\Auth\PasswordBrokerFactory::class],
            'auth.password.broker' => [\Illuminate\Auth\Passwords\PasswordBroker::class, \Illuminate\Contracts\Auth\PasswordBroker::class],
            'queue'                => [\Illuminate\Queue\QueueManager::class, \Illuminate\Contracts\Queue\Factory::class, \Illuminate\Contracts\Queue\Monitor::class],
            'queue.connection'     => [\Illuminate\Contracts\Queue\Queue::class],
            'queue.failer'         => [\Illuminate\Queue\Failed\FailedJobProviderInterface::class],
            'redirect'             => [\Illuminate\Routing\Redirector::class],
            // 'redis'                => [\Illuminate\Redis\RedisManager::class, \Illuminate\Contracts\Redis\Factory::class],
            // 'redis.connection'     => [\Illuminate\Redis\Connections\Connection::class, \Illuminate\Contracts\Redis\Connection::class],
            // 'request'              => [\Illuminate\Http\Request::class, \Symfony\Component\HttpFoundation\Request::class],
            // 'router'               => [\Illuminate\Routing\Router::class, \Illuminate\Contracts\Routing\Registrar::class, \Illuminate\Contracts\Routing\BindingRegistrar::class],
            // 'session'              => [\Illuminate\Session\SessionManager::class],
            // 'session.store'        => [\Illuminate\Session\Store::class, \Illuminate\Contracts\Session\Session::class],
            'url'                  => [\Illuminate\Routing\UrlGenerator::class, \Illuminate\Contracts\Routing\UrlGenerator::class],
            'validator'            => [\Illuminate\Validation\Factory::class, \Illuminate\Contracts\Validation\Factory::class],
            // 'view'                 => [\Illuminate\View\Factory::class, \Illuminate\Contracts\View\Factory::class],
        ],
   

        /*

        Contract                	References Facade
Illuminate\Contracts\Auth\Access\Authorizable	  
Illuminate\Contracts\Auth\Access\Gate	Gate
Illuminate\Contracts\Auth\Authenticatable	  
Illuminate\Contracts\Auth\CanResetPassword	 
Illuminate\Contracts\Auth\Factory	Auth
Illuminate\Contracts\Auth\Guard	Auth::guard()
Illuminate\Contracts\Auth\PasswordBroker	Password::broker()
Illuminate\Contracts\Auth\PasswordBrokerFactory	Password
Illuminate\Contracts\Auth\StatefulGuard	 
Illuminate\Contracts\Auth\SupportsBasicAuth	 
Illuminate\Contracts\Auth\UserProvider	 
Illuminate\Contracts\Bus\Dispatcher	Bus
Illuminate\Contracts\Bus\QueueingDispatcher	Bus::dispatchToQueue()
Illuminate\Contracts\Broadcasting\Factory	Broadcast
Illuminate\Contracts\Broadcasting\Broadcaster	Broadcast::connection()
Illuminate\Contracts\Broadcasting\ShouldBroadcast	 
Illuminate\Contracts\Broadcasting\ShouldBroadcastNow	 
Illuminate\Contracts\Cache\Factory	Cache
Illuminate\Contracts\Cache\Lock	 
Illuminate\Contracts\Cache\LockProvider	 
Illuminate\Contracts\Cache\Repository	Cache::driver()
Illuminate\Contracts\Cache\Store	 
Illuminate\Contracts\Config\Repository	Config
Illuminate\Contracts\Console\Application	 
Illuminate\Contracts\Console\Kernel	Artisan
Illuminate\Contracts\Container\Container	App
Illuminate\Contracts\Cookie\Factory	Cookie
Illuminate\Contracts\Cookie\QueueingFactory	Cookie::queue()
Illuminate\Contracts\Database\ModelIdentifier	 
Illuminate\Contracts\Debug\ExceptionHandler	 
Illuminate\Contracts\Encryption\Encrypter	Crypt
Illuminate\Contracts\Events\Dispatcher	Event
Illuminate\Contracts\Filesystem\Cloud	Storage::cloud()
Illuminate\Contracts\Filesystem\Factory	Storage
Illuminate\Contracts\Filesystem\Filesystem	Storage::disk()
Illuminate\Contracts\Foundation\Application	App
Illuminate\Contracts\Hashing\Hasher	Hash
Illuminate\Contracts\Http\Kernel	 
Illuminate\Contracts\Mail\MailQueue	Mail::queue()
Illuminate\Contracts\Mail\Mailable	 
Illuminate\Contracts\Mail\Mailer	Mail
Illuminate\Contracts\Notifications\Dispatcher	Notification
Illuminate\Contracts\Notifications\Factory	Notification
Illuminate\Contracts\Pagination\LengthAwarePaginator	 
Illuminate\Contracts\Pagination\Paginator	 
Illuminate\Contracts\Pipeline\Hub	 
Illuminate\Contracts\Pipeline\Pipeline	 
Illuminate\Contracts\Queue\EntityResolver	 
Illuminate\Contracts\Queue\Factory	Queue
Illuminate\Contracts\Queue\Job	 
Illuminate\Contracts\Queue\Monitor	Queue
Illuminate\Contracts\Queue\Queue	Queue::connection()
Illuminate\Contracts\Queue\QueueableCollection	 
Illuminate\Contracts\Queue\QueueableEntity	 
Illuminate\Contracts\Queue\ShouldQueue	 
Illuminate\Contracts\Redis\Factory	Redis
Illuminate\Contracts\Routing\BindingRegistrar	Route
Illuminate\Contracts\Routing\Registrar	Route
Illuminate\Contracts\Routing\ResponseFactory	Response
Illuminate\Contracts\Routing\UrlGenerator	URL
Illuminate\Contracts\Routing\UrlRoutable	 
Illuminate\Contracts\Session\Session	Session::driver()
Illuminate\Contracts\Support\Arrayable	 
Illuminate\Contracts\Support\Htmlable	 
Illuminate\Contracts\Support\Jsonable	 
Illuminate\Contracts\Support\MessageBag	 
Illuminate\Contracts\Support\MessageProvider	 
Illuminate\Contracts\Support\Renderable	 
Illuminate\Contracts\Support\Responsable	 
Illuminate\Contracts\Translation\Loader	 
Illuminate\Contracts\Translation\Translator	Lang
Illuminate\Contracts\Validation\Factory	Validator
Illuminate\Contracts\Validation\ImplicitRule	 
Illuminate\Contracts\Validation\Rule	 
Illuminate\Contracts\Validation\ValidatesWhenResolved	 
Illuminate\Contracts\Validation\Validator	Validator::make()
Illuminate\Contracts\View\Engine	 
Illuminate\Contracts\View\Factory	View
Illuminate\Contracts\View\View	View::make()
        */
 
    ];
