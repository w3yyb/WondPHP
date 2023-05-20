<?php
namespace WondPHP;
use WondPHP\Contracts\Request as RequestContracts;

/**
 * Class Request
 *
 */
class Request extends Http\Request  implements RequestContracts
{
    use SingletonTrait;
    public function __construct()
    {
        parent::__construct();
    }
     
}
