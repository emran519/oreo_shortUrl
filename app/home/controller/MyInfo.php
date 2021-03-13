<?php


namespace app\home\controller;


use oreo\lib\Db;

class MyInfo extends Base
{

    public function index(){
        return view('home/myInfo', [
            'user_info' => $this->user_info,
            'user_email' => $this->user_info['email']?:"0",
            'user_phone' => $this->user_info['phone']?:"0",
            'user_qq' => $this->user_info['qq_num']?:"0"
        ]);
    }

    public function bindEmail(){
        $data = request()->post(); //获取Post对象
        $bind_type = $data['bind_type'] == 1 ? '邮箱' : '手机号码';
        $bind_type_where = $data['bind_type'] == 1 ? 'email' : 'phone';
        $bind_sms = $data['bind_type'] == 1 ? 1 : 2;
        //过滤
        $contact = preg_replace('/[ ]/','',$data['is_target']);//接受对象
        if(!$contact) return json(0,"{$bind_type}不能为空");
        if($data['bind_type']==1){
            if (!filter_var($contact, FILTER_VALIDATE_EMAIL))return json(0,'邮箱不合法');
        }else{
            if(!preg_match("/^1[345789]\d{9}$/", $contact))return json(0,'手机号码不合法');
        }
        if(empty($_SESSION['captcha']) || $_SESSION['captcha'] != request()->post('local_verify_code')){
            return json(0,"图形验证码错误");
        }
        //查询
        $res_target = Db::table('user')->where("{$bind_type_where}=:target")->bind(':target',$contact)->find();
        if($res_target)return json(0,"该{$bind_type}已经被绑定，不能重复操作");
        //发送验证码
        $sms = new OtherBase();
        if(!empty($data['sms_type'])){
            if(isset($_SESSION['send_mail']) && $_SESSION['send_mail']>time()-60){
                return json(0,'请勿频繁发送验证码');
            }
            $sms_code = $sms->sendSms($bind_sms, $contact,1);
            if($sms_code){
                return json(200,'验证码发送成功，请注意查收');
            }else{
                return json(0,'验证码发送失败或数据库写入失败');
            }
        }
        if(empty($data['verifyCode']))return json(0,'验证码不能为空');
        //校验验证码
        $is_code = $sms->isVerifyCode($data['verifyCode'], $contact);
        if($is_code['result'] !== true) return json(0,$is_code['msg']);
        $param = [
            $bind_type_where => $contact
        ];
        Db::table('user')->where(['id'=>$this->user_id])->update($param);
        unset($_SESSION['captcha']);
        $_SESSION['user_info']["$bind_type_where"] = $contact;
        return json(200,'绑定成功');
    }

    public function addUserInfo(){
        $qq = request()->post('qq_num/d');
        if(empty($qq)) $qq = null;
        $password = preg_replace('/[ ]/','',request()->post('password'));
        if(!empty($password)){
            $param = [
              'qq_num' => $qq,
                'password' => md5(config("app.admin_salt").$password)
            ];
        }else{
            $param = [
                'qq_num' => $qq,
            ];
        }
        Db::table('user')->where('id',$this->user_id)->update($param);
        $_SESSION['user_info']['qq_num'] = $qq;
        return json(200,'保存成功');
    }
}