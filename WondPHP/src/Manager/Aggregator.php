<?php

namespace WondPHP\Manager;

use WondPHP\Manager\Exceptions\DriverOverwrittenException;

final class Aggregator
{
    /**
     * @var array
     */
    protected $drivers;

    /**
     * Welcome
     */
    public function __construct()
    {
        $this->drivers = [];
    }

    /**
     * Set driver instance
     *
     * @param string $name
     * @param mixed $driver
     * @throws \WondPHP\Manager\Exceptions\DriverOverwrittenException
     *
     * @return void
     */
    public function set(string $name, $driver)
    {
        if ($this->has($name)) {
            throw new DriverOverwrittenException('Driver [' . $name . '] already registered.');
        }

        $this->drivers[$name] = $driver;
    }

    /**
     * Get driver instance
     *
     * @param  string  $name
     * 
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->drivers[$name];
    }

    /**
     * Check if driver instance exists
     *
     * @param  string  $name
     * 
     * @return boolean
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->drivers);
    }   

    /**
     * Get all drivers instance
     *
     * @return array
     */
    public function all(): array
    {
        return $this->drivers;
    }           
}
