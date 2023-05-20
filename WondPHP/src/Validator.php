<?php
/**
 * Date: 2021/5/19
 * Time: 18:26
 */
namespace WondPHP;

use Illuminate\Validation\Factory;
use WondPHP\Contracts\Translation;
use WondPHP\Contracts\Validator as ValidatorContracts;

class Validator extends Factory implements ValidatorContracts
{

    public function __construct(Translation $translator=null, Container $container = null){

        $translator = app('translator');
        parent::__construct($translator);

    }
}