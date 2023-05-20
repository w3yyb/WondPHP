<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Event extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'events';
    }
}