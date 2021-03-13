<?php


namespace app\home\controller;


use oreo\extend\Sms;
use oreo\lib\Db;
use oreo\lib\Exception;

class OtherBase
{

    /**
     * 查询系统变量
     * @param string $fieldName 变量名
     * @return false|mixed
     */
    protected function systemInfo(string $fieldName){
        if(!getCache('system',$fieldName)) {
            $fieldName = preg_replace('/[ ]/', '', $fieldName);
            $conf = Db::table('system')->where("name=:name")->bind(':name',$fieldName)->find();
            setCache('system',$fieldName, $conf,604800);
            return $conf;
        }else{
            return getCache('system',$fieldName);
        }
    }

    //发送验证码
    public function sendSms(int $sys_sms, string $contact, int $type){
        $type_name = $type == 1 ? '绑定帐号验证码' : '找回密码验证码';
        $web_name = $this->systemInfo('web_name')['value'];
        $code = rand(111111,999999);//验证码
        $sms = new Sms();
        //如果是邮件
        if($sys_sms==1){
            $smtp_url = $this->systemInfo('smtp_url')['value'];
            $smtp_port = $this->systemInfo('smtp_port')['value'];
            $mail_name = $this->systemInfo('mail_name')['value'];
            $mail_pass = $this->systemInfo('mail_pass')['value'];
            $sms->email($smtp_url, $smtp_port, $mail_name, $mail_pass, $web_name);
            $res = $sms->emailParam($contact,"{$type_name}", "您的{$type_name}：{$code}，如非本人操作，请忽略本邮件！");
        }elseif ($sys_sms==2){
            $accessKeyId = $this->systemInfo('sms_ali_id')['value'];
            $accessKeySecret = $this->systemInfo('sms_ali_secret')['value'];
            $signName = $this->systemInfo('sms_ali_sign_name')['value'];
            $templateCode = $this->systemInfo('sms_ali_tpl_id')['value'];
            $sms->aliSms($accessKeyId, $accessKeySecret, $signName, $templateCode);
            $res = $sms->aliSmsParam($contact, $code);
        }else{
            $appId = $this->systemInfo('sms_ten_id')['value'];
            $appKey = $this->systemInfo('sms_ten_key')['value'];
            $tempId = $this->systemInfo('sms_ten_tpl_id')['value'];
            $sign = $this->systemInfo('sms_ten_sign')['value'];
            $sms->qCloudSms($appId, $appKey);
            $res = $sms->qCloudSmsParam(86, $contact, $tempId, $code, $sign);
        }
        if($res === true){
            $_SESSION['send_mail']=time();
            $sms_res = $this->sendCodeLog($sys_sms, $contact, $code);
            if($sms_res) return true;
        }else{
            return false;
        }
    }

    //验证码入库
    private function sendCodeLog(int $sys_sms, string $contact, int $code){
        $param = [
            'type' =>  $sys_sms == 1 ? 1 : 2,
            'target' => $contact,
            'code' => $code,
            'state' => 2,
            'client_ip' => request()->ip(),
            'send_time' => time()
        ];
        Db::begin();
        try{
            Db::table('sms_log')->insert($param);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            return false;
        }
        return true;
    }

    //校验验证码
    public function isVerifyCode(int $code, string $contact){
        //查询
        $res = Db::table('sms_log')->where(['target'=>$contact,'code'=>$code,'state'=>2])->find();
        if(empty($res)) return ['result'=> false, 'msg' => '验证码不正确'];
        if($res['send_time']<time()-900) return ['result'=> false, 'msg' => '验证码已失效'];
        //修改为已验证
        $param = [
            'state' => 1,
            'verify_time' => time()
        ];
        Db::table('sms_log')->where(['target'=>$contact,'code'=>$code,'state'=>2])->update($param);
        return ['result'=> true, 'msg' => 'ok'];
    }

}