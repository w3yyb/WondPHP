<?php
namespace app\controllers;
use app\models\Article ;
use app\models\Book ;
use View;
use RedisService;
// use Db;
 use Container;
 use Config;
 use Facades\App as App;
 use Facades\Events;
 use app\events\BlogView;
//  use Illuminate\Support\Facades\Event;
use Event;
// use Protocols\Http\Request;
// use WondPHP\Request as Request;
 use WondPHP\Contracts\Request;
 use WondPHP\Contracts\Translation;
 use WondPHP\Facades\Cache;
 use WondPHP\Facades\DB;
 use WondPHP\Facades\Cookie;
 use WondPHP\Facades\Http;
 use  File;
use WondPHP\Facades\Redis;
use WondPHP\Facades\Log;
// use Illuminate\Database\Capsule\Manager as DB;



/**
* \HomeController
*/
class HomeController extends BaseController
{
  
  protected static  $middleware999 = [ 
    'aMiddleware:admin' 	=> ['except' 	=> ['hello'] ],
    'bMiddleware' => ['only' 		=> ['Index'] ],
  ];

  public function __construct()
  {
 
		// Event::fire(new BlogView());
 
    
    // echo 999;
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
 
    // // app('translation')->setLocale('zh');
    // echo __('messages.welcome');

    // // event(new BlogView);
    // Event::dispatch(new BlogView);

    // // App::setLocale('zh');
    // echo  App::getLocale(); 
    // // echo 999;
    // echo 'HOme __construct'."\n";
    //   app('translation')->setLocale('zh');
    //  echo __('messages.welcome');

  }

  public function test($id,$num)
  {

    var_dump($id,$num);
    echo 1111111111;
  }
  public function Index(Request $request )
  {
    return 'hello world';

    $response = Http::get('http://php.weather.sina.com.cn/whd.php');
    return $response->body() ;


    return view('admin.profile', ['name' => '<script>dfdfd</script>','haha'=>[2,3]]);

    return view('home')
            ->with('name', 'Victoria');



    return View::make('home', ['name' => 'James']);

    return view('home', ['name' => 'James2']);
$array=['username'=>'afasdf'];
$this->view = View::make('home',['name' => 'James']);
 

    Cookie::queue(Cookie::make('namefgdfgsdf', 'value', 88));

    Cookie::queue('name3333556', 'value', 99);
    Cookie::make('test26626', 'hello, world', 9999);
    return 'hello world';

    var_dump($request->post(),$request->get());
    $a=Article::all()->toArray();
    foreach ($a as $key => $value) {
      echo $value['username'];
    }
    $mongodb=Book::all();
foreach ($mongodb as $key => $value) {
  echo $value['title'];
}


    echo   'iiiiiiiiiiiii';
 
    // return response('xxxxxxxxxxxxxxx');
return 'hello world';

    // return redirect('form')->withInput();


    $request->flashExcept('password');


    // $request->flash();
    $username = $request->old('username');
    echo 5555555;
    var_dump($username);
    Cookie::queue(Cookie::make('namefgdfgsdf', 'value', 88));

    Cookie::queue('name3333556', 'value', 99);
    Cookie::make('test26626', 'hello, world', 9999);
    // Cookie::forget('test22');
    // Cookie::forever('rrrrrr', 'value');
    // // Cookie::set('ssssss', 'value', 3600);
    // Cookie::delete('ssssss');

    echo 'aaaaaaaaaaaaa';
    // $value = Cookie::get('test22');

    // var_dump($value);
// $array=['username'=>'afasdf'];
// $this->view = View::make('home')->with('items',$array);
     
    // logs('dddddddddd');
    // throw new \Exception("Error Processing Request", 1);
    // abort(403, '页面异常11');

    // trigger_error("Cannot divide by zero", E_USER_ERROR);

    // $log = Log::channel('log2');
    // $log->info('log2 test');
    // Log::debug('An informational message44.');

    // $app = app('cache66')->get('ddd');
    // // 判断对象实例是否存在
    // var_dump($app);

    // var_dump(app('app36'));
    return 'hello world';
    $redis = Redis::connection();
    var_dump($redis);
    Redis::pipeline(function ($pipe) {
      for ($i = 0; $i < 10; $i++) {
          $pipe->set("key:$i", $i);
      }
  });

    Redis::set('name', 'Taylorttt');

$values = Redis::get('name');
var_dump($values);

return 'sssssss';

    File::disk('local')->put('file.txt', 'Contents');
    File::put('avatars/1', 'ddddd');
    File::prepend('file.log', 'Prepended Text');
    // var_dump( File::exists('file.log'));
    var_dump(File::deleteDirectory('aaa'));
    var_dump(get_included_files());

    // return File::size('file.log');

    // File::disk('local')->put('file.txt', 'Contents');
    // File::prepend('file.log', 'Prepended Text');
    // File::disk('local')->put('avatars/1', 'dddddddddddd');

    $affected = DB::statement('delete from  sw   where username = ?', ['Dayle']);

    // DB::insert('insert into sw (id,age, username) values (?,?, ?)', [11,12, 'Dayle']);



    $users = DB::select('select * from sw ');


    Cache::put('test', 'This is loaded from Memcached cache.', 500);
    $value = Cache::get('test');
var_dump($value,555);
    var_dump($request->action());
    // // echo env('APP_PURPOSES');

    // var_dump(route('/admin',['id' => 100]));
//     echo 2332323;
//     return 'xxxxxxxxx';
// return response('dddd ');

// //     return ['aaa'=>'bbb'];
// return response(['aaa'=>'bbbbbb']);
// // $_SESSION['aaaaaaaaa']=9999999;

    session(['key1'=>'value1', 'key2' => 'value2']);
    $session = request()->session();
    $all = $session->all();
    var_dump($all,$_SESSION);
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
