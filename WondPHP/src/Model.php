<?php
namespace WondPHP;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        Orm::getInstance();
    }
}
