<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Redis extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redis';
    }
}