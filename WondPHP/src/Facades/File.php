<?php
namespace WondPHP\Facades;

use WondPHP\Facade;

class File extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'files';
    }
}