<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Session extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'session';
    }
}