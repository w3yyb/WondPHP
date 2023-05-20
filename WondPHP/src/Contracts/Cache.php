<?php
namespace WondPHP\Contracts;

interface Cache
{
    public function put($key, $value, $ttl = null);
    public function get($key);

      
//todo




}