<?php
namespace WondPHP;

use Illuminate\Database\Capsule\Manager as Capsule;
use WondPHP\Contracts\DB as DBContracts;
use Illuminate\Database\Query\Expression;

// Orm::getInstance();
/**
 * Class Db
 *
 */
class Db extends Capsule  implements DBContracts
{
    // use SingletonTrait;

    public function __construct()
    {

        // Eloquent ORM
        // var_dump(app());exit;
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
        $this->manager =$capsule->manager;
    }
        /**
     * Get a new raw query expression.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Query\Expression
     */
    public function raw($value)
    {
        return new Expression($value);
    }
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->manager->select($query, $bindings, $useReadPdo);
    }

    public function insert($query, $bindings = [])
    {
        return $this->manager->insert($query, $bindings);

    }
    public function update($query, $bindings = [])
    {
        return $this->manager->update($query, $bindings);

    }

    public function delete($query, $bindings = [])
    {
        return $this->manager->delete($query, $bindings);

    }

    public function statement($query, $bindings = [])
    {
        return $this->manager->statement($query, $bindings);

    }

    public function listen(\Closure $callback)
    {
        return $this->manager->listen($callback);

    }

    public function transaction(\Closure $callback)
    {
        return $this->manager->transaction($callback);

    }

    public function beginTransaction()
    {
        return $this->manager->beginTransaction();

    }

    public function rollBack()
    {
        return $this->manager->rollBack();

    }

    public function commit()
    {
        return $this->manager->commit();

    }

}
