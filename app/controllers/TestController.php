<?php
/**
 * Created by PhpStorm.
 * User: ruby
 * Date: 2021/5/18
 * Time: 18:21
 */
namespace app\controllers;

use WondPHP\Contracts\Request;


class TestController extends BaseController{

    public function index(){
        echo 'test.index';
    }
    public function store(Request $request){

        $validatReturn = $this->validate($request,[
            'name' => 'required|email|max:6',
            'password' => 'required',
        ]);
        var_dump($validatReturn);
       // var_dump($request->all());
        return;
        $data = $request->all();
        $validator = app('validator')->make($request->all(), [
            'name' => 'required|email|max:6',
            'password' => 'required',
        ]);
        if($validator->fails()){
            $errors = $validator->errors()->getMessages();
            var_dump($errors);
        }

        var_dump($data);
    }
}