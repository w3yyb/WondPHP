<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Hash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hash';
    }
}