<?php
namespace app\models;

use WondPHP\MongoModel;

class Book extends MongoModel
{
    protected $connection = 'mongodb';
    protected $fillable = ['title'];

}
?>
