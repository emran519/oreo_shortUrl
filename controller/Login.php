<?php

namespace controller;

use oreo\lib\Controller;
use oreo\lib\Db;

class Login extends Controller{

	public function __construct(){
		parent::__construct();
	}

	//登录界面
	public function index(){
	    if(!empty($_SESSION['admin_info'])){
		    header("location:".url("../home"));
		}
        return view('login', [
            'web_name' => $this->systemInfo('web_name')['value'],
            'icp_num' => $this->systemInfo('icp_num')['value']
        ]);
	}

    //登录
    public function checkLogin($username,$password){
        if(empty($_SESSION['captcha']) || $_SESSION['captcha'] != request()->post('vercode')){
            return json(0,"验证码错误");
        }
        unset($_SESSION['captcha']);
        $res = Db::table("auth_admin")->where("username=:username")->bind(':username', $username)->find();
        if($res && $res['password'] == md5(config("app.admin_salt").$password)){
            $_SESSION['admin_info'] = $res;
            Db::table("auth_admin")->where("id",$res['id'])->update(['last_login_time'=>date("Y-m-d H:i:s")]);
            $this->admin_log("登录成功");
            return json(200,"登录成功");
        }else{
            $this->exception_log("登录失败:用户名[{$username}]");
            return json(0,"用户名或密码错误");
        }
    }

    //验证码
    public function captcha(){
       \oreo\lib\Tool::captcha();
    }

    //退出登录
	public function logout(){
		if(!empty($_SESSION['admin_info'])){
			$this->admin_log("退出登录");
			unset($_SESSION['admin_info']);
		}
		return json(200,'退出登录');
	}

	function __destruct(){}
}
