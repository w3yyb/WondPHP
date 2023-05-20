<?php
namespace WondPHP;

use Error;
use  Illuminate\Support\Str as Str;

use WondPHP\Contracts\Http as  HttpContracts;
// use Illuminate\Log\LogManager;
 
class Http implements HttpContracts
{
    protected  $http ;

     
    public  function __construct()
    {
        $this->http = new \Illuminate\Http\Client\Factory();

    }

   
    public function __call($method, $parameters)
    {
      return   $this->http->$method(...$parameters);
    }


 
 
    
}
