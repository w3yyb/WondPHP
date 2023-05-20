<?php
namespace WondPHP;

use Error;
use Illuminate\Redis\RedisManager;

use WondPHP\Contracts\Redis as RedisContracts;

class Redis implements RedisContracts
{
    protected  $_manager = null;

     
    public  function __construct()
    {
       
        $config = config('database.redis');
        $this->_manager = new RedisManager('', 'phpredis', $config);
    }

    /**
     * @param string $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public  function connection($name = 'default') {
        return $this->_manager->connection($name);
    }

 
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public  function __call($name, $arguments)
    {
        return $this->_manager->connection('default')->{$name}(... $arguments);
    }
 
    
}
