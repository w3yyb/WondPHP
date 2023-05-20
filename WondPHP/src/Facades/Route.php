<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}