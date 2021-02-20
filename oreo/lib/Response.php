<?php

namespace oreo\lib;

use oreo\lib\Config;

class Response{

	public function __construct(){}

	static public function setType($type){
		Config::$config['app']["response_type"] = $type;
		return true;
	}
	static public function html($data){
		header("Content-type: text/html; charset=utf-8;");
		echo $data;exit;
	}
	static public function json($data){
		header("Content-type: application/json; charset=utf-8;");
		echo json_encode($data);exit;
	}
	static public function xml($data){
		header("Content-type:text/xml;charset=utf-8");
		echo self::arr2xml($data);exit;
	}
	static function arr2xml($data, $root = true){
	    $str="";
	    if($root)$str .= "<xml>";
	    foreach($data as $key => $val){
	        if(is_array($val)){
	            $child = self::arr2xml($val, false);
	            $str .= "<$key>$child</$key>";
	        }else{
	            $str.= "<$key><![CDATA[$val]]></$key>";
	        }
	    }
	    if($root)$str .= "</xml>";
	    return $str;
	}

	public function __destruct(){}
}
