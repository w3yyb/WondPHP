<?php
namespace WondPHP;

class GetEnv
{
    use SingletonTrait;

    public function __construct()
    {
        
//把这个放到项目的入口文件里
        if (is_file(BASE_PATH.'/.env')) {
            $env = parse_ini_file(BASE_PATH.'/.env', true);    //解析env文件,name = PHP_KEY
            foreach ($env as $key => $val) {
                $name = strtoupper($key);
                if (\is_array($val)) {
                    foreach ($val as $k => $v) {    //如果是二维数组 item = PHP_KEY_KEY
                        $item = $name . '_' . strtoupper($k);
                        putenv("$item=$v");
                    }
                } else {
                    putenv("$name=$val");
                }
            }
        }
        //用getenv('对应的key')获取值
    }
}
