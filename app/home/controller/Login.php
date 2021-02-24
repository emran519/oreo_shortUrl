<?php


namespace app\home\controller;


use oreo\lib\Db;

class Login
{
    public function index(){
        return \oreo\lib\View::display('home/login');
    }

    //登录
    public function checkLogin(){
        $username = request()->post('username');
        $password = request()->post('password');
        if(empty($username) || empty($password)) return json(0,"用户名或密码不能玩空");
        $res = Db::table("user")->where("username=:username")->bind(':username', $username)->find();
        if($res && $res['password'] == md5(config("app.admin_salt").$password)){
            $_SESSION['admin_info'] = $res;
            Db::table("user")->where("id",$res['id'])->update(['login_ip'=>request()->ip()]);
            return json(200,"登录成功");
        }else{
            return json(0,"用户名或密码错误");
        }
    }
}