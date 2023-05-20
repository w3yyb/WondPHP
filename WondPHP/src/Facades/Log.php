<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Log extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}