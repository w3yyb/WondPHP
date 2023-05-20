<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Response extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'response';
    }
}