<?php
namespace WondPHP;

use Illuminate\Database\Capsule\Manager as Capsule;
 
class Orm //extends Capsule
{
    use SingletonTrait;

    public function __construct()
    {

        // Eloquent ORM
        $capsule = new Capsule;
        $db_configs=require BASE_PATH.'/config/database.php';
        $capsule->addConnection($db_configs['connections']['mongodb'], 'mongodb');
        $capsule->addConnection($db_configs['connections']['mysql'], 'default');

        $capsule->setAsGlobal();  //this is important

//Mongodb
     $capsule->getDatabaseManager()->extend('mongodb', function ($config, $name) {
    $config['name'] = $name;

    return new \Jenssegers\Mongodb\Connection($config);
});

        $capsule->bootEloquent();
    }
}
