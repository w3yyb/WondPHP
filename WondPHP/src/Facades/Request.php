<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}