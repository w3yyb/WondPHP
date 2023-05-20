<?php
namespace WondPHP;

/**
*     FileName: cache.php
*         Desc:  PHP简单文件缓存
*       Author: Lenix
*        Email: yyb8@vip.qq.com
*     HomePage: http://www.p2hp.com
*      Version: 0.0.1
*   LastChange: 2018-12-22 09:50:50
*      History:
*/

class PageCache
{
use SingletonTrait;

    public function __construct($cache_time=86400)
    {

        if (!isset($_GET['phpfilecache']) && PAGE_CACHE) {
            define("PAGE_PATH", BASE_PATH."/cache/page/");
            // define("PAGE_TIME", 86400);//缓存时间:秒
            define("PAGE_COMPRESS", true);//缓存压缩
            // var_dump($_SERVER['REQUEST_METHOD']);
            if ($_SERVER['REQUEST_METHOD']!=='GET') {
                return;
            }
            echo $this->PAGE_getcache($cache_time);
            exit;
        }
    }



    public function PAGE_getcache($cache_time,$iscache='')
    {
        $url= $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $cacheurl=strpos($url, "?")?$url."&phpfilecache=true":$url."?phpfilecache=true";
        $cachename=PAGE_PATH.md5($url).".c";
        $cachetime=$iscache?time()+1:time()-($cache_time);

        if (file_exists($cachename) && filemtime($cachename)>=$cachetime) {
            if (PAGE_COMPRESS) {
                $return=file_get_contents($cachename);
                $data=function_exists('gzcompress')?@gzuncompress($return):$return;
                return unserialize($data);
            } else {
                $return=file_get_contents($cachename);
                $data=$return;
                return $data;
            }
        } else {
            $return=file_get_contents($cacheurl);
            $this->PAGE_writecache($cachename, $return);
            return $return;
        }
    }
    public function PAGE_writecache($name, $array)
    {
        if (PAGE_COMPRESS) {
            function_exists('gzcompress')?$return =gzcompress(serialize($array)):$return=serialize($array);
            @file_put_contents($name, $return);
        } else {
            $return=$array;
            @file_put_contents($name, $return);
        }
    }
}
