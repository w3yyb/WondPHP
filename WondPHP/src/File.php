<?php
namespace WondPHP;

use Error;
use Illuminate\Filesystem\Filesystem;

use WondPHP\Contracts\File as FileContracts;

class File implements FileContracts
{
    private $manager;

    public function __construct()
    {
        // $container =app();
        $config =include BASE_PATH.'/config/filesystems.php';
 
        $container['config'] =$config;

        $this->files = new Filesystem();

        $container = app();
        // $container->instance('app', $container);
        // $container['config']['filesystems'] = new \Illuminate\Config\Repository($config);
        $container['config']['filesystems'] =  $config;

        $this->manager = new \Illuminate\Filesystem\FilesystemManager($container);

    }

    public function disk($name=null)
    {

        return $this->manager->disk($name);

    }


    public function prepend($path, $data)
    {

        return $this->manager->prepend($path, $data);

    }

    public function append($path, $data)
    {

        return $this->manager->append($path, $data);

    }

    public function put($path, $contents, $lock = false)
    {

        return $this->manager->put($path, $contents, $lock);

    }

    public function get($name)
    {

        return $this->manager->get($name);

    }

    public function exists($path)
    {

        return $this->manager->exists($path);

    }

    public function missing($path)
    {

        return $this->manager->missing($path);

    }

      public function download($path, $name = null, array $headers = [])//todo
      {
        return $this->manager->download($path, $name ,  $headers );

      }

      public function url($path)
    {

        return $this->manager->url($path);

    }

    public function size($path)
    {

        return $this->manager->size($path);

    }

    public function copy($path, $target)
    {
        return $this->manager->copy($path, $target);

    }

    public function move($path, $target)
    {
        return $this->manager->move($path, $target);

    }

    public function delete($paths)
    {

        return $this->manager->delete($paths);

    }
    public function files($directory, $hidden = false)
{
    return $this->manager->files($directory, $hidden);

}

public function allFiles($directory, $hidden = false)
{
    return $this->manager->allFiles($directory, $hidden);

}

public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
{
    return $this->manager->makeDirectory($path, $mode , $recursive , $force );


}
public function deleteDirectory($directory, $preserve = false)
{
    return $this->manager->deleteDirectory($directory, $preserve);

}
    
}
