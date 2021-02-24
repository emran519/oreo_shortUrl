<?php


namespace app\home\controller;


use oreo\lib\Db;

class Register
{

    public function index(){
        return \oreo\lib\View::display('home/register');
    }

    public function regUser(){
        $data = request()->post(); //获取Post对象
        //过滤
        if(!$data['username']) return json(0,'用户名不能为空');
        $username = preg_replace('/[ ]/','',$data['username']);//移除前后空格登录账号
        $username = strtolower($username);//登录账号//登录账号转小写
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $username) > 0) return json(0,'登录账号不能包含中文字符');
        $password = preg_replace('/[ ]/','',$data['password']);//登录密码
        if(!$password) return json(0,'登录密码不能为空');
        $contact = preg_replace('/[ ]/','',$data['contact']);//邮箱
        if(!$contact) return json(0,'邮箱不能为空');
        if (!filter_var($contact, FILTER_VALIDATE_EMAIL))return json(0,'邮箱不合法');
        //查询
        $res_user = Db::table('user')->where("username=:username")->bind(':username',$username)->find();
        if(!empty($res_user))return json(0,'该用户名已被注册');
        $res_mail = Db::table('user')->where("email=:email")->bind(':email',$contact)->find();
        if(!empty($res_mail))return json(0,'该邮箱已被注册');
        $param = [
            'username'   =>  $username,//管理员账号
            'password'   =>  md5(config("app.admin_salt").$password),//管理员密码
            'email'  =>  $contact,//邮箱
            'create_time'=>  date('Y-m-d H:i:s'),//添加时间
            'state'      =>  1//状态;1=>正常;2=>封禁
        ];
        Db::table('user')->insert($param);
        return json(200,'注册成功');
    }

}