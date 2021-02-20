<?php

namespace oreo\lib;

class Error{

	public function __construct(){}

	//类不存在
	static public function emptyClass($className){
		echo json_encode(["code"=>"0","msg"=>"类文件不存在"],JSON_UNESCAPED_UNICODE);
		exit;
	}
	//文件不存在
	static public function emptyFile($filePath){
		echo json_encode(["code"=>"0","msg"=>"当前访问文件不存在"],JSON_UNESCAPED_UNICODE);
		exit;
	}
	//404
	static public function urlErr(){
		echo json_encode(["code"=>"0","msg"=>"404，找不到文件"],JSON_UNESCAPED_UNICODE);
		exit;
	}
	//未通过安全检查
	static public function securityCheck(){
		echo json_encode(["code"=>"0","msg"=>"安全检测未通过"],JSON_UNESCAPED_UNICODE);
		exit;
	}
    //未通过安全检查
    static public function safetyFailed(){
        echo json_encode(["code"=>"0","msg"=>"您的IP已经被禁止,请稍后再试"],JSON_UNESCAPED_UNICODE);
        exit;
    }

	public function __destruct(){}
}
