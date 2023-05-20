<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class View extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}