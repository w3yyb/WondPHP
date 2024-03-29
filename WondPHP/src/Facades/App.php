<?php
 
declare (strict_types = 1);

namespace  WondPHP\Facades;

use WondPHP\Facade;
 
class App extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'app';
    }
}
