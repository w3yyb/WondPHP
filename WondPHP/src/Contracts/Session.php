<?php
namespace WondPHP\Contracts;

interface Session
{
     
    public function get($name, $default = null);
    public function set($name, $value);
    public function delete($name);
    public function pull($name, $default = null);
    public function put($key, $value = null);
    public function forget($name);
    public function all();
    public function flush();
    public function has($name);
    public function exists($name);
 





}