<?php

namespace oreo\lib;

/**
 * Class Cache 缓存
 * @package oreo\lib
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */
class Cache{

    /**
     * @var string 缓存目录
     */
	public static $path = BASE_PATH . "runtime/cache/";

    /**
     * Cache constructor.
     */
	public function __construct(){}

    /**
     * 存入缓存
     * @param string $key 键名
     * @param mixed $contents 键值
     * @param int $expiration 缓存过期时间
     * @return false|int
     */
	static public function setCache(string $key, $contents, $expiration=600){
		$fileName = md5($key);
		return self::set($fileName,$contents,$expiration);
	}

    /**
     * 读取缓存
     * @param string $key 键名
     * @return false|mixed
     */
	static public function getCache(string $key){
		$fileName = md5($key);
		return self::get($fileName);
	}

    /**
     * 写入当前请求响应缓存
     * @param $contents
     * @param int $expiration
     * @return false|int
     */
	static public function responseCache($contents,$expiration=600){
		$fileName = self::getResponseFileName();
		return self::set($fileName,$contents,$expiration);
	}

    /**
     * 获取当前请求响应缓存
     * @return false|mixed
     */
	static public function getResponseCache(){
		$fileName = self::getResponseFileName();
		return self::get($fileName);
	}

    /**
     * 读取缓存
     * @param string $fileName 缓存文件名MD5值
     * @return false|mixed
     */
	static private function get(string $fileName){
		$filePath = self::$path.'/'.$fileName;
		if(!file_exists($filePath))return false;
		$data = unserialize(file_get_contents($filePath));
		if($data["expiration"] < _NOW_){
			@unlink($filePath);
			return false;
		}
		return $data["contents"];
	}

    /**
     * 写入缓存
     * @param string $fileName
     * @param mixed $contents 键值
     * @param int $expiration
     * @return false|int
     */
	static private function set(string $fileName, $contents, int $expiration){
		self::checkPath();
		$filePath = self::$path.'/'.$fileName;
		if(file_exists($filePath))@unlink($filePath);
		$data = ["expiration"=>_NOW_+$expiration,"contents"=>$contents];
		return file_put_contents($filePath,serialize($data));
	}

    /**
     * 检查缓存目录
     * 无文件夹则创建cache
     */
	static private function checkPath(){
		$path = self::$path;
		if(!is_dir($path))@mkdir($path, 0777, true);
	}

    /**
     * 获取当前请求生成文件名
     * @return string
     */
	static private function getResponseFileName(){
		$controllerName = \oreo\lib\Route::$controller;
		$actionName = \oreo\lib\Route::$action;
		$params = \oreo\lib\Route::$params;
		$keys = $controllerName.$actionName;
		if(!empty($params)){
			ksort($params);
			foreach($params as $k=>$v){
				$keys .= $k;
				$keys .= $v;
			}
		}
		return md5($keys);
	}

    /**
     * 删除所有缓存
     * @return bool
     */
	static public function clear(){
		return removeDir(self::$path);
	}

	public function __destruct(){}
}
