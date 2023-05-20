<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Config extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}