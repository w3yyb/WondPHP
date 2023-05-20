<?php
namespace WondPHP\Contracts;

interface Config
{
    public function has($key);
    public function get($key, $default = null);
    public function getMany($keys);
    public function set($key, $value = null);
    public function prepend($key, $value);
    public function push($key, $value);
    public function all();
}
