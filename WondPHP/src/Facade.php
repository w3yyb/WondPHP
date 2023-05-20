<?php
namespace WondPHP;

/**
 * Facade管理类
 */
class Facade
{
    /**
     * 始终创建新的对象实例
     * @var bool
     */
    protected static $alwaysNewInstance;
    protected static $app;
    protected static $resolvedInstance;

    /**
     * 创建Facade实例
     * @static
     * @access protected
     * @param  string $class       类名或标识
     * @param  array  $args        变量
     * @param  bool   $newInstance 是否每次创建新的实例
     * @return object
     */
    protected static function createFacade(string $class = '', array $args = [], bool $newInstance = false)
    {
        $class = $class ?: static::class;

        $facadeClass = static::getFacadeAccessor();

        if ($facadeClass) {
            $class = $facadeClass;
        }

        if (static::$alwaysNewInstance) {
            $newInstance = true;
        }

        return Container::getInstance()->make($class, $args, $newInstance);
    }

    /**
     * 获取当前Facade对应类名
     * @access protected
     * @return string
     */
    protected static function getFacadeAccessor()
    {
    }


    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = [];
    }

    /**
     * Set the application instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }

    /**
     * 带参数实例化当前Facade类
     * @access public
     * @return object
     */
    public static function instance(...$args)
    {
        if (__CLASS__ != static::class) {
            return self::createFacade('', $args);
        }
    }

    /**
     * 调用类的实例
     * @access public
     * @param  string     $class       类名或者标识
     * @param  array|true $args        变量
     * @param  bool       $newInstance 是否每次创建新的实例
     * @return object
     */
    public static function make(string $class, $args = [], $newInstance = false)
    {
        if (__CLASS__ != static::class) {
            return self::__callStatic('make', \func_get_args());
        }

        if (true === $args) {
            // 总是创建新的实例化对象
            $newInstance = true;
            $args        = [];
        }

        return self::createFacade($class, $args, $newInstance);
    }

    // 调用实际类的方法
    public static function __callStatic($method, $params)
    {
        return \call_user_func_array([static::createFacade(), $method], $params);
    }
}
