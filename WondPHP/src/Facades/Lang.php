<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class Lang extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'translator';
    }
}