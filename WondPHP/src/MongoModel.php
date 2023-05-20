<?php
namespace WondPHP;

use Jenssegers\Mongodb\Eloquent\Model as BaseModel;

class MongoModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        Orm::getInstance();
    }
}
