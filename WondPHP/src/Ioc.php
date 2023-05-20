<?php
namespace WondPHP;

/**
*
* 工具类，使用该类来实现自动依赖注入。
*
*/
class Ioc
{
 
    // 获得类的对象实例
    public static function getInstance($className)
    {
        $paramArr = self::getMethodParams($className);
 
        return (new ReflectionClass($className))->newInstanceArgs($paramArr);
    }
 
    /**
     * 执行类的方法
     * @param  [type] $className  [类名]
     * @param  [type] $methodName [方法名称]
     * @param  [type] $params     [额外的参数]
     * @return [type]             [description]
     */
    public static function make($className, $methodName, $params = [])
    {
        $class = new ReflectionClass($className);
        if ($class->hasProperty('staticProperty')) {
            $class->getProperty('staticProperty')->setValue($methodName);//用于控制器中间件
        }
        // 获取类的实例
        $instance = self::getInstance($className);
 
        // 获取该方法所需要依赖注入的参数
        $paramArr = self::getMethodParams($className, $methodName);
 
        return $instance->{$methodName}(...array_merge($paramArr, $params));
    }
 
    /**
     * 获得类的方法参数，只获得有类型的参数
     * @param  [type] $className   [description]
     * @param  [type] $methodsName [description]
     * @return [type]              [description]
     */
    protected static function getMethodParams($className, $methodsName = '__construct')
    {
 
        // 通过反射获得该类
        $class = new ReflectionClass($className);
        $paramArr = []; // 记录参数，和参数类型
 
        // 判断该类是否有构造函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            $construct = $class->getMethod($methodsName);
 
            // 判断构造函数是否有参数
            $params = $construct->getParameters();
 
            if (\count($params) > 0) {
 
                // 判断参数类型
                foreach ($params as $key => $param) {
                    if ($paramClass = $param->getType()) {
 
                        // 获得参数类型名称
                        $paramClassName = $paramClass->getName();
 
                        // 获得参数类型
                        $args = self::getMethodParams($paramClassName);
                        $paramArr[] = (new ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                    }
                }
            }
        }
 
        return $paramArr;
    }
}
