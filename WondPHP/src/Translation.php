<?php
namespace WondPHP;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use WondPHP\Contracts\Translation as TranslationContracts;


class Translation extends Translator implements TranslationContracts
{

    public function __construct($loader=null, $locale=null)
    {
        $locale = config('app.locale');

        $loader =new FileLoader(new \Illuminate\Filesystem\Filesystem, BASE_PATH.'/resources/lang');
        return parent::__construct($loader, $locale);
    }



    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        return new FileLoader(new \Illuminate\Filesystem\Filesystem, BASE_PATH.'/resources/lang');
    }
}
