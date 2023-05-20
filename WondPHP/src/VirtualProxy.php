<?php
namespace WondPHP;

/*
 * 虚代理，只有在被访问成员时才调用闭包函数生成目标对象。
 *
 * @author tonyseek
 */

class VirtualProxy
{
    private $holder = null;
    private $loader = null;
    /**
    * 虚代理，只有在被访问成员时才调用闭包函数生成目标对象。
    *
    * @param Closure $loader 生成被代理对象的闭包函数
    */

    public function __construct(Closure $loader)
    {
        $this->loader = $loader;
    }
    /**

    * 代理成员方法的调用
    *
    * @param string $method
    * @param array  $arguments
    * @throws BadMethodCallException
    * @return mixed
    */

    public function __call($method, array $arguments = null)
    {
        $this->check();
        if (!method_exists($this->holder, $method)) {

//  throw new BadMethodCallException();
            throw new Exception();
        }
        return \call_user_func_array(array(&$this->holder, $method), $arguments);
    }
    /**

    * 代理成员属性的读取

    *

    * @param string $property

    * @throws ErrorException

    * @return mixed

    */

    public function __get($property)
    {
        $this->check();
        if (!isset($this->holder->$property)) {
            throw new Exception();
        }
        return $this->holder->$property;
    }
    /**

    * 代理成员属性的赋值

    *

    * @param string $property

    * @param mixed  $value

    */

    public function __set($property, $value)
    {
        $this->check();
        $this->holder->$property = $value;
    }
    /**

    * 检查是否已经存在被代理对象，不存在则生成。

    */

    private function check()
    {
        if (null == $this->holder) {
            $loader = $this->loader;
            $this->holder = $loader();
        }
    }
}


// // 测试

// $v = new VirtualProxy(function () {
//     echo 'Now, Loading', "\n";
//     $a = new ArrayObject(range(1, 100));
//     $a->abc = 'a';
//     // 实际使用中，这里调用的是 DataMapper 的 findXXX 方法
//     // 返回的是领域对象集合
//     return $a;
// });

// // 代理对象直接当作原对象访问

// // 而此时构造方法传入的 callback 函数才被调用

// // 从而实现加载对象操作的延迟

// echo $v->abc . $v->offsetGet(50);
