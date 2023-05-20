<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Cache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cache';
    }
}