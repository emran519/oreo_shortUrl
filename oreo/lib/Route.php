<?php

namespace oreo\lib;

use oreo\lib\config;
use oreo\lib\Response;
use oreo\lib\SafetyCheck;
class Route{

	static public $controller = "";
	static public $action = "";
	static public $params = [];
	static public $rawPost = null;
	static public $privileges = "";
	static public $existentParam = null;

	public function __construct(){}

	static public function parseUrl(){
		//静态化
		$url_html_suffix = Config::get("app.url_html_suffix");
		if(!empty($url_html_suffix) && substr($_SERVER['QUERY_STRING'],-strlen($url_html_suffix)) == $url_html_suffix){
			$_SERVER['QUERY_STRING'] = substr($_SERVER['QUERY_STRING'], 0,-strlen($url_html_suffix));
		}
		//
		parse_str($_SERVER['QUERY_STRING'],$queryStringParams);
		isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO']) ? $isPathinfo=true : $isPathinfo = false;
		if($isPathinfo){
			if(!empty($url_html_suffix) && substr($_SERVER['PATH_INFO'],-strlen($url_html_suffix)) == $url_html_suffix){
				$_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'], 0,-strlen($url_html_suffix));
			}
			$path_info = trim($_SERVER['PATH_INFO'],'/');
			$arr = explode("/",$path_info);
			self::$controller = array_shift($arr);
			self::$action = array_shift($arr);
			for($i=0;$i<count($arr);$i=$i+2){
				self::$params[$arr[$i]] = isset($arr[$i+1]) ? $arr[$i+1] : "";
			}
			self::$params = array_merge(self::$params,$queryStringParams);
		}else{
			if(!empty($queryStringParams["controller"])){
				self::$controller = $queryStringParams["controller"];
				unset($queryStringParams["controller"]);
				unset($_GET["controller"]);
			}
			if(!empty($queryStringParams["action"])){
				self::$action = $queryStringParams["action"];
				unset($queryStringParams["action"]);
				unset($_GET["action"]);
			}
			self::$params = $queryStringParams;
		}
		$_GET = array_merge($_GET,self::$params);
		$_REQUEST = array_merge($_GET,$_POST);
		self::$params = $_REQUEST;
		self::$rawPost = file_get_contents("php://input");
		//
		if(empty(self::$controller) || stripos(self::$controller,"index.php") !== false)self::$controller="Index";
		if(empty(self::$action) || stripos(self::$action,"index.php") !== false)self::$action = "index";
		self::$controller[0] = strtoupper(self::$controller[0]);
		self::$privileges = self::$controller.'.'.self::$action;
		SafetyCheck::run();
	}
	static public function parseCli(){
		//
		$params = [];
		if($_SERVER['argc'] > 1){
			$args_list = array_slice($_SERVER['argv'],1);
			foreach($args_list as $arg){
				if(strpos($arg,"=") !== false){
					$pos = strpos($arg,"=");
					$arg_k = trim(substr($arg,0,$pos));
					$arg_v = trim(substr($arg,-(strlen($arg)-($pos+1))));
					if(!empty($arg_k) && !empty($arg_v)){
						if(strtolower($arg_k) == "controller"){
							self::$controller = $arg_v;
						}else if(strtolower($arg_k) == "action"){
							self::$action = $arg_v;
						}else{
							$params[$arg_k] = trim($arg_v);
						}
					}
				}
			}
		}
		self::$params = $params;
		if(empty(self::$controller))self::$controller="Index";
		if(empty(self::$action))self::$action = "index";
		self::$controller[0] = strtoupper(self::$controller[0]);
		self::$privileges = self::$controller.'.'.self::$action;
		//
	}
	
	static public function run(){

	 	if(IS_CLI){
	 	    self::parseCli();
		}else{
	 	    self::parseUrl();
		}
		$controllerClassName = "controller\\".self::$controller;
		$actionName = self::$action;
		$actionNameLower = strtolower($actionName);
		//
		if(method_exists($controllerClassName,$actionName)){
			$result = invoke($controllerClassName,$actionName);
		}else if(method_exists($controllerClassName,$actionNameLower)){
			$result = invoke($controllerClassName,$actionNameLower);
		}else if(method_exists($controllerClassName,"_empty")){
			$result = invoke($controllerClassName,"_empty");
		}else{
		    if(Config::get("app.route.existent")){
                self::$existentParam = self::$controller;
                self::$controller = Config::get("app.route.default_class");
                self::$action = Config::get("app.route.default_fun");
                self::$privileges = self::$controller.'.'.self::$action;
                $controllerClassName = "controller\\".Config::get("app.route.default_class");
                $actionName = self::$action;
                method_exists($controllerClassName,$actionName);
                $result = invoke($controllerClassName,$actionName);
            }else{
                \oreo\lib\Error::urlErr($controllerClassName.".".$actionName);
            }
		}
		/*
		if(!IS_CLI && \core\Config::get("sys.session")){
			\core\Session::clear();
			\core\Session::save();
		}
		*/
		//格式化输出
		if(isset($result)){
			if(IS_CLI){
				Response::html($result);
			}else{
				$response_type = Config::get("app.response_type");
				switch($response_type){
					case "html":Response::html($result);break;
					case "json":Response::json($result);break;
					case "xml":Response::xml($result);break;
					default : Response::html($result);
				}
			}
		}
	}

	public static function version(){
	    return '1.0';
    }

	public function __destruct(){}
}
