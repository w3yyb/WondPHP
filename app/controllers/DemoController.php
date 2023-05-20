<?php
namespace app\controllers;
use app\model\Article ;
use app\model\Book ;
use app\models\User;
use app\models\Admin\User as AdminUser;
use View;
use RedisService;
use Db;
 use Container;
 use errorHandel;
use WondPHP\Request as Request;

// use Illuminate\Database\Capsule\Manager as DB;


/**
* \HomeController
*/
class  DemoController extends BaseController
{


protected  static $middleware33 = [ 
    'aMiddleware:admin' 	=> ['except' 	=> ['hello'] ],
    'bMiddleware' => ['only' 		=> ['Index'] ],
];

// public     $middleware = ['aMiddleware','bMiddleware'];

  
  public function __construct()
  {
    // $this->middleware('bMiddleware');
    // $this->middleware('aMiddleware');

    //     // $this->middleware('log', ['only' => [
    //     //     'fooAction',
    //     //     'barAction',
    //     // ]]);

        // $this->middleware('aMiddleware', ['except' => [
        //     'test',
        //     'Indexdd',
        // ]]);
    // var_dump($error);
    // echo 999;
  }
  public function test(){

echo 'test~~~~~~~';
  }
  public function Index(User $user, AdminUser $adminuser,Request $rq ,$aa )
  {
    var_dump($user->username,$adminuser->username);

     echo 'INdex';
    // echo $adsfasdf;
//    echo  $container->setB;
//  echo  $container->get('logger');
    // $name = request()->get('name');
    // $session = request()->session();
    // $session->set('name', $name);
    
    return response('hello ' );

    echo  session('key1', 'default');

    session(['key1'=>'value1', 'key2' => 'value2']);
    $session = request()->session();
    $all = $session->all();
    // var_dump($all);
exit;
    // $a=request()->file();
    $file = request()->file('upload');
        if ($file && $file->isValid()) {
            $file->move(PUBLIC_PATH.'/dd/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
    // return response()->download( PUBLIC_PATH.'/a.jpg');
    // return  "hello world";

    // // return response('hello webman')->cookie('foo5577', 'valu55e',5555555);
    // return response()->file( PUBLIC_PATH.'/a.jpg'); 
     
    // var_dump(request()->getRemoteIp());
   
    // exit;
    // return  "hello world";
    
    return response('hello webman');

    // // return response('hello webman')->cookie('foo5577', 'valu55e');
    // // return redirect('/user');
    // return json(['code' => 0, 'msg' => 'ok']);
    // create a log channel

    // return  "hello world";

    // logs('dddddddddd');
    // var_dump(config('database.charset'));
//mysql db
   $users = DB::table('sw')->get();
   foreach ($users as $key => $value) {
  echo $value->username .' ------';

   }
 
// //mysql orm
$a=Article::all()->toArray();
foreach ($a as $key => $value) {
  echo $value['username'];
}
 
$a=Article::all()->toArray();
foreach ($a as $key => $value) {
  echo $value['username'];}

  $a=Article::all()->toArray();
foreach ($a as $key => $value) {
  echo $value['username'];}
  $a=Article::all()->toArray();
  foreach ($a as $key => $value) {
    echo $value['username'];}

    $a=Article::all()->toArray();
    foreach ($a as $key => $value) {
      echo $value['username'];}
// //mongodb
// Book::create(['ird' => 1, 'title' => 'The Fault in Our Stars']);
$mongodb=Book::all();
foreach ($mongodb as $key => $value) {
  echo $value['title'];
}


// // RedisService::set('key','value',5,'s');
// // echo RedisService::get('key');


// $array=['username'=>'afasdf'];
// $this->view = View::make('home')->with('items',$array);
// $this->view = View::make('home')->with('items',Article::all());
                                    // ->withTitle('MFFC :-D')
                                    // ->withFuckMe('OK!');

    





  }
}
