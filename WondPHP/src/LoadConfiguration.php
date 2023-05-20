<?php

namespace WondPHP;

use Exception;
// use Illuminate\Config\Repository;
use WondPHP\Contracts\Config  as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct( )
    {
        $apcu_key="config";
        $items = [];
        $app=app();


        if (apcu_exists($apcu_key)) {
            $items= apcu_fetch($apcu_key);
             $loadedFromCache = true;

        }

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        // if (is_file($cached = $app->getCachedConfigPath())) {
        //     $items = require $cached;

        //     $loadedFromCache = true;
        // }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance('config', $config = new ConfigRepository($items));

        if (! isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
           apcu_store($apcu_key, $config->all(), 6);

        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        date_default_timezone_set($config->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $repository
     * @return void
     *
     * @throws \Exception
     */
    protected function loadConfigurationFiles( $app, RepositoryContract $repository)
    {
        $files = $this->getConfigurationFiles($app);

        if (! isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return array
     */
    protected function getConfigurationFiles( $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach ( glob($configPath.'/*.php') as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file, '.php')] = $file;
        }

        ksort($files, SORT_NATURAL);
        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getNestedDirectory( $file, $configPath)
    {
        $directory = dirname($file);

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }
}
