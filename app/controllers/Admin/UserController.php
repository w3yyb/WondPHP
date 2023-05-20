<?php
namespace app\controllers\Admin;
use app\models\Article ;
use app\models\Book ;
use View;
use RedisService;
use Db;
 use Container;
 use Config;
 use facade\App as App;
 use facade\Events;
 use app\events\BlogView;
 use Illuminate\Support\Facades\Event;
// use Protocols\Http\Request;
use WondPHP\Request as Request;
use WondPHP\facade\Hello as FacadeHello;



// use Illuminate\Database\Capsule\Manager as DB;



/**
* \HomeController
*/
class UserController 
{

protected static  $middlewareqqq = [ 
  'aMiddleware:admin' 	=> ['except' 	=> ['hello'] ],
  'bMiddleware' => ['only' 		=> ['Index'] ],
];
  
  public function __construct()
  {
 
		// Event::fire(new BlogView());
 
    
    // echo 999;
    echo 'HOme __construct'."\n";
    // session(['key1888'=>'value1', 'key2888' => 'value2']);
 
    // $container = app()->get("Events");
    //  event(new BlogView);
    // // $ev=$container::getInstance()->get('Events');
    // // var_dump($container);
    // Event::dispatch(new BlogView);

    // // App::setLocale('zh');
    // echo  App::getLocale(); 
    // // echo 999;
    // echo 'HOme __construct'."\n";
 
    // // app('Translation')->setLocale('zh');
    // echo __('messages.welcome');

    // // event(new BlogView);
    // Event::dispatch(new BlogView);

    // // App::setLocale('zh');
    // echo  App::getLocale(); 
    // // echo 999;
    // echo 'HOme __construct'."\n";
    // // app('Translation')->setLocale('zh');
    // echo __('messages.welcome');

  }

  public function test($id,$num)
  {

    var_dump($id,$num);
    echo 1111111111;
  }
  public function Index(Request $request )
  {

    echo 'admin';
   
    // echo env('APP_PURPOSES');

    echo 2332323;
    return 'xxxxxxxxx';
return response('dddd ');

//     return ['aaa'=>'bbb'];
return response(['aaa'=>'bbbbbb']);
// $_SESSION['aaaaaaaaa']=9999999;

    session(['key1'=>'value1', 'key2' => 'value2']);
    $session = request()->session();
    $all = $session->all();
    var_dump($_SESSION);
    // return 'hello world';
echo $request->input('name');

   var_dump($request->acceptJson());
    //  return cookie('foo777', 'value');
    // return redirect('/user');
     $response1 = response('hahahha ');
     $hd=$response1->getHeaders();

    //  var_dump($hd);
             $response1->cookie('foo66117771', 'value');
             return $response1;
     return 'hello world';

echo 99999999;
    $response1 = response();
    // var_dump($response1);
            $response1->cookie('foo66117771', 'value');
    //  $response1->modify('header','one111441','ffffffffffffff');
    
            return $response1;

     echo 2222222222;
     echo '路由:' .route('adminuser', ['id' => 100]).'   '; // 结果为 /blog/100
     echo env('APP_PURPOSES');
    echo config('database.prefix');
       
     


    // return  response('helloooooooooooo');

    // echo 9999;
    // echo request()->aaaa;
    // var_dump( request()->all());

    // echo  response()->caps('foo');//能取到从middleware中的
// echo request()->getLocalIp();
// 创建一个对象
$response = response();
// var_dump($response);
// echo  response()->share('foo99');

// .... 业务逻辑省略

// 设置cookie
$response->cookie('foo', 'value');

// .... 业务逻辑省略

// // 设置http头
// $response->header('Content-Type', 'application/json');
$response->withHeaders([
            'one1111q' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);

// .... 业务逻辑省略

// 设置要返回的数据
$response->withBody('返回的数据');
// $response->modify('header','one1111','fffffffffff');

return  $response;

    echo response('Home INdex~~~~~');
    echo 'aaaaaaaaaaaa';
    // return 'xxxxxx';
     
    var_dump( request()->all());
    echo 7777777;
    exit;
    session(['key1888'=>'value1', 'key2888' => 'value2']);

    echo  session('key1', 'default');
	  // echo  cookie('yyyyybbbbbb', 'value');

	 echo cookie('xxxxxxxxxxxxx', 'value');

    return response('helloooooooooooo');

// exit;
    var_dump(config('APP_PURPOSES'));
    echo env('APP_PURPOSES');
    // echo $adsfasdf;
   echo  $container->setB;
 echo  $container->get('logger');
    $name = request()->get('name');
    $session = request()->session();
    $session->set('name', $name);
    
    return response('hello ' . $session->get('name'));

    echo  session('key1', 'default');

    session(['key1'=>'value1', 'key2' => 'value2']);
    $session = request()->session();
    $all = $session->all();
    // var_dump($all);
// exit;
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