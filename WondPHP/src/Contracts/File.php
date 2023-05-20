<?php
namespace WondPHP\Contracts;

interface File
{

      
//todo 有些还没有实现
public function disk($name=null);
public function prepend($path, $data);
public function append($path, $data);
public function put($path, $contents, $lock = false);
public function get($name);
public function exists($path);
public function missing($path);
public function download($path, $name = null, array $headers = []);//todo
public function url($path);
public function size($path);
public function copy($path, $target);
public function move($path, $target);
public function delete($paths);
public function files($directory, $hidden = false);
public function allFiles($directory, $hidden = false);
public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false);
public function deleteDirectory($directory, $preserve = false);




}