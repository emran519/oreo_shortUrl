<?php


namespace app\home\controller;


use oreo\lib\Db;

class Register extends OtherBase
{

    public function index(){
        return view('home/register', [
            'system_sms' => $this->systemInfo('system_sms')['value'],
        ]);
    }

    public function regUser(){
        $data = request()->post(); //获取Post对象
        $sys_sms = $this->systemInfo('system_sms')['value'];
        $sys_sms_name = $sys_sms == 1 ? '邮箱' : '手机号码';
        $sys_sms_where = $sys_sms == 1 ? 'email' : 'phone';
        //过滤
        $contact = preg_replace('/[ ]/','',$data['is_target']);//接受对象
        if(!$contact) return json(0,"{$sys_sms_name}不能为空");
        if($sys_sms==1){
            if (!filter_var($contact, FILTER_VALIDATE_EMAIL))return json(0,'邮箱不合法');
        }else{
            if(!preg_match("/^1[345789]\d{9}$/", $contact))return json(0,'手机号码不合法');
        }
        $password = preg_replace('/[ ]/','',$data['password']);//登录密码
        if(!$password) return json(0,'登录密码不能为空');
        if(empty($_SESSION['captcha']) || $_SESSION['captcha'] != request()->post('local_verify_code')){
            return json(0,"图形验证码错误");
        }
        //发送验证码
        if(!empty($data['sms_type'])){
            if(isset($_SESSION['send_mail']) && $_SESSION['send_mail']>time()-60){
                return json(0,'请勿频繁发送验证码');
            }
            $sms_code = $this->sendSms($sys_sms, $contact,1);
            if($sms_code){
                return json(200,'验证码发送成功，请注意查收');
            }else{
                return json(0,'验证码发送失败或数据库写入失败');
            }
        }
        if(empty($data['verifyCode']))return json(0,'验证码不能为空');
        //查询
        $res_target = Db::table('user')->where("{$sys_sms_where}=:target")->bind(':target',$contact)->find();
        if(!empty($res_target))return json(0,"该{$sys_sms_name}已被注册");
        //校验验证码
        $is_code = $this->isVerifyCode($data['verifyCode'], $contact);
        if($is_code['result'] !== true) return json(0,$is_code['msg']);
        $param = [
            'password'   =>  md5(config("app.admin_salt").$password),
            "$sys_sms_where"  =>  $contact,//邮箱
            'create_time'=>  date('Y-m-d H:i:s'),//添加时间
            'state'      =>  1//状态;1=>正常;2=>封禁
        ];
        Db::table('user')->insert($param);
        unset($_SESSION['captcha']);
        return json(200,'注册成功');
    }

    //图形验证码
    public function captcha(){
        \oreo\lib\Tool::captcha();
    }

}