<?php


namespace oreo\lib;

/**
 * Class Config 读取配置文件
 * @package oreo\lib
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */
class Config
{
    /**
     * @var array
     */
    static public  $config = [];
    /**
     * @var array
     */
    static protected  $databaseConfig = [];

    public function __construct(){}

    static public function init(){
        $path =  BASE_PATH . "config";
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || is_dir($file) || $file[0] == ".")continue;
            $file = substr(strtolower($file),0,-4);
            if(substr($file,0,5) == "oreo_" && strlen($file)>5){
                self::$databaseConfig['database'] = include BASE_PATH . "config/" . $file . ".php";
                self::$config[substr($file,5)] = include  BASE_PATH . "config/" . $file . ".php";
            }
        }
    }

    /**
     * @param string $key 键
     * @return mixed|null
     */
    static public function get(string $key){
        $paramArr = explode(".", $key);
        $src = self::$config;
        $i = 0;
        while(true){
            if($i == count($paramArr) - 1){
                if(isset($src[$paramArr[$i]])){
                    return $src[$paramArr[$i]];
                }else{
                    return null;
                }
            }else{
                if(isset($src[$paramArr[$i]])){
                    $src = $src[$paramArr[$i]];
                }else{
                    return null;
                }
                $i ++;
            }
        }
    }

    /**
     * 读取数据库配置
     * @return mixed
     */
    static public function getDatabaseConfig(){
        return self::$databaseConfig['database'];
    }

    /**
     * 读取全部配置
     * @return array
     */
    static public function getAll(){
        return ['config'=>self::$config,'databaseConfig'=>self::$databaseConfig];
    }

    public function __destruct(){}
}
