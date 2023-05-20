<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}