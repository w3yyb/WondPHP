<?php
namespace WondPHP;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Container\Container as LApp;
use Illuminate\Support\Facades\Facade;

use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Support\ServiceProvider;
use WondPHP\Contracts\Event as EventContracts;

class Events extends ServiceProvider implements EventContracts
{
    /**
        * The event handler mappings for the application.
        *
        * @var array
        */
    protected $listen = [];
    protected $subscribe = [];
     
    /**
     * The subscriber classes to register.
     *
     * @var array
     */

    public function __construct()
    {
        $this->configs=include BASE_PATH.'/config/events.php';
        $this->listen=$this->configs['listen'];
        $this->subscribe=$this->configs['subscribe'];
        $app = new LApp;
        parent::__construct($app);
        Facade::setFacadeApplication($app);

        $app->singleton('events', function ($app) {
            return (new Dispatcher($app))->setQueueResolver(function () use ($app) {
                return $app->make(QueueFactoryContract::class);
            });
        });

        $this->register();
    }


    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function register()
    {
        $events = $this->getEvents();

        foreach ($events as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }

    /**
     * Get the discovered events and listeners for the application.
     *
     * @return array
     */
    public function getEvents()
    {
        if (0) {//yuan  if ($this->app->eventsAreCached()) {
            $cache = require $this->app->getCachedEventsPath();

            return $cache[get_class($this)] ?? [];
        } else {
            return array_merge_recursive(
                $this->discoveredEvents(),
                $this->listens()
            );
        }
    }

    /**
     * Get the discovered events for the application.
     *
     * @return array
     */
    protected function discoveredEvents()
    {
        return $this->shouldDiscoverEvents()
                    ? $this->discoverEvents()
                    : [];
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }

    /**
     * Discover the events and listeners for the application.
     *
     * @return array
     */
    public function discoverEvents()
    {
        return collect($this->discoverEventsWithin())
                    ->reject(function ($directory) {
                        return ! is_dir($directory);
                    })
                    ->reduce(function ($discovered, $directory) {
                        return array_merge_recursive(
                            $discovered,
                            DiscoverEvents::within($directory, $this->eventDiscoveryBasePath())
                        );
                    }, []);
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            BASE_PATH.'/app/listeners',
            // $this->app->path('Listeners'),
        ];
    }

    /**
     * Get the base path to be used during event discovery.
     *
     * @return string
     */
    protected function eventDiscoveryBasePath()
    {
        return BASE_PATH;
    }

    public function __call($method, $parameters)
    {
        Event::$method(...$parameters);
    }
}
