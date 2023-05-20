<?php
namespace WondPHP;

use Error;
use Illuminate\Cache\CacheManager;
use Illuminate\Redis\RedisManager;
// use WondPHP\App;
use Illuminate\Filesystem\Filesystem;

use WondPHP\Contracts\Cache as CacheContracts;

class Cache implements CacheContracts
{
    private $cache;

    public function __construct()
    {
        $container =app();
        $config =include BASE_PATH.'/config/cache.php';
 
        $container['config'] =$config;
    

        $container['files'] = new Filesystem;
        $container['redis'] = new RedisManager($container, 'predis', $container['config']['database.redis']);
        $container['memcached.connector'] = new \Illuminate\Cache\MemcachedConnector();


        $cacheManager = new CacheManager($container);
    
        // Get the default cache driver (redis in this case)
        $this->cache = $cacheManager->store();
        return $this->cache;
    }

    public function put($key, $value, $ttl = null)
    {
        return $this->cache->put($key, $value, $ttl = null);
    }
    public function get($key)
    {
        return $this->cache->get($key);
    }
}
