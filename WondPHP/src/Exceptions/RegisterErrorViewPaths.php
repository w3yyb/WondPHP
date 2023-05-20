<?php

namespace WondPHP\Exceptions;

use WondPHP\Facades\View;

class RegisterErrorViewPaths
{
    /**
     * Register the error view paths.
     *
     * @return void
     */
    public function __invoke()
    {
//  View::make();

//  View::make('home')->with('items',$array);

        // View::replaceNamespace('errors', collect(config('view.paths'))->map(function ($path) {
        //     return "{$path}/errors";
        // })->push(__DIR__.'/views')->all());
    }
}
