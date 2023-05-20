<?php
namespace WondPHP\Contracts;

interface DB
{
    public static function table($table, $as = null, $connection = null);
    public function raw($value);
    public static function connection($connection = null);
    public function select($query, $bindings = [], $useReadPdo = true);
    public function insert($query, $bindings = []);
    public function update($query, $bindings = []);
    public function delete($query, $bindings = []);
    public function statement($query, $bindings = []);
    public function listen(\Closure $callback);
    public function transaction(\Closure $callback);
    public function beginTransaction();
    public function rollBack();
    public function commit();
}
