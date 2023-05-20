<?php
namespace WondPHP;

// namespace service;
use Illuminate\Container\Container as LApp;
 
use WondPHP\Contracts\View as ViewContracts;

/**
* \View
*/
class View implements ViewContracts
{
    const VIEW_BASE_PATH = '/app/views/';
     
    public function __construct($view)
    {
        $container=new LApp;
        $pathsToTemplates = [BASE_PATH.self::VIEW_BASE_PATH];
        $pathToCompiledTemplates = BASE_PATH . '/cache/compiled';
        // Dependencies
        $filesystem = new \Illuminate\Filesystem\Filesystem;
        $eventDispatcher = new \Illuminate\Events\Dispatcher($container);

        // Create View Factory capable of rendering PHP and Blade templates
        $viewResolver = new \Illuminate\View\Engines\EngineResolver;
        // $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler($filesystem, $pathToCompiledTemplates);


        $container->singleton('blade.compiler', function ($app)  use ($filesystem,$pathToCompiledTemplates) {
            return tap(new \Illuminate\View\Compilers\BladeCompiler($filesystem, $pathToCompiledTemplates), function ($blade) {
                $blade->component('dynamic-component', DynamicComponent::class);
            });
        });



        $viewResolver->register('blade', function () use ($container) {
            return new \Illuminate\View\Engines\CompilerEngine($container['blade.compiler']);
        });

        $viewFinder = new \Illuminate\View\FileViewFinder($filesystem, $pathsToTemplates);
        $this->viewFactory = new \Illuminate\View\Factory($viewResolver, $viewFinder, $eventDispatcher);



        $this->viewFactory->setContainer($container);
        $this->viewFactory->share('app', $container);

    }
 
     

    public function __call($method, $parameters)
    {
        return $this->viewFactory->$method(...$parameters);
    }

    // public static function __callStatic($method, $parameters)
    // {
    //        return $this->viewFactory::$method(...$parameters);

    // }
}
