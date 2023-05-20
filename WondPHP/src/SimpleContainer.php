<?php
namespace WondPHP;

// some methods ref php-di
class SimpleContainer {
 private static $s=array();
 function __set($k, $c) { self::$s[$k]=$c; }
 function set($k, $c) { self::$s[$k]=$c; }
 function __get($k) { return self::$s[$k]($this); }
 function get($k) { return self::$s[$k]($this); }


 public  function make($className, $methodName, $params = [])
 {
     if (empty($methodName)) return new $className(...$params);
     return  new $className->{$methodName}(...$params);
 }
 public function  call( callable $callable,$params=[])
 {
 return \call_user_func($callable,$params);

 }


}
