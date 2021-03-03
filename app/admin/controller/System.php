<?php

namespace app\admin\controller;

use oreo\lib\Controller;
use oreo\lib\Db;
class System extends Controller
{

    public function __construct(){
        parent::__construct();
        $this->auth();
    }

    //编辑系统变量
    public function systemSet(){
        $data = request()->post();
        foreach ($data as $k => $value) {
            $value=preg_replace('/[ ]/','',$value);
            Db::table('system')->where("name",$k)->update(['value' => "$value"]);
        }
        \oreo\lib\Cache::clear();
        return  json('200','更新成功');
    }

    //基本参数
    public function webSite(){
        return view('admin/system/website', [
            'web_name' => $this->systemInfo('web_name'),
            'web_url' => $this->systemInfo('web_url'),
            'icp_num' => $this->systemInfo('icp_num'),
            'system_sms' => $this->systemInfo('system_sms'),
            'system_state' => $this->systemInfo('system_state'),
            'system_state_text' => $this->systemInfo('system_state_text')
        ]);
    }

    //短信/邮件
    public function send(){
        return view('admin/system/send', [
            'smtp_url' => ['info' => 'SMTP地址', 'name' => 'smtp_url', 'value' => 'smtp.qq.com'],
            'smtp_port' => $this->systemInfo('smtp_port'),
            'mail_name' => ['info' => '发件邮箱账号', 'name' => 'mail_name', 'value' => '演示站不呈现保密数据'],
            'mail_pass' => ['info' => '发件邮箱密码', 'name' => 'mail_pass', 'value' => '演示站不呈现保密数据'],
            'sms_ali_id' => ['info' => 'AccessKeyId', 'name' => 'sms_ali_id', 'value' => '演示站不呈现保密数据'],
            'sms_ali_secret' => ['info' => 'AccessKeySecret', 'name' => 'sms_ali_secret', 'value' => '演示站不呈现保密数据'],
            'sms_ali_sign_name' => ['info' => '签名内容', 'name' => 'sms_ali_sign_name', 'value' => '演示站不呈现保密数据'],
            'sms_ali_tpl_id' => ['info' => '短信模板ID', 'name' => 'sms_ali_tpl_id', 'value' => '演示站不呈现保密数据'],
            'sms_ten_id' => ['info' => 'AppId', 'name' => 'sms_ten_id', 'value' => '演示站不呈现保密数据'],
            'sms_ten_key' => ['info' => 'AppKey', 'name' => 'sms_ten_key', 'value' => '演示站不呈现保密数据'],
            'sms_ten_tpl_id' => ['info' => '模板ID', 'name' => 'sms_ten_tpl_id', 'value' => '演示站不呈现保密数据'],
            'sms_ten_sign' => ['info' => '短信签名', 'name' => 'smtp_url', 'value' => '演示站不呈现保密数据'],
        ]);
        /*return view('admin/system/send', [
            'smtp_url' => $this->systemInfo('smtp_url'),
            'smtp_port' => $this->systemInfo('smtp_port'),
            'mail_name' => $this->systemInfo('mail_name'),
            'mail_pass' => $this->systemInfo('mail_pass'),
            'sms_ali_id' => $this->systemInfo('sms_ali_id'),
            'sms_ali_secret' => $this->systemInfo('sms_ali_secret'),
            'sms_ali_sign_name' => $this->systemInfo('sms_ali_sign_name'),
            'sms_ali_tpl_id' => $this->systemInfo('sms_ali_tpl_id'),
            'sms_ten_id' => $this->systemInfo('sms_ten_id'),
            'sms_ten_key' => $this->systemInfo('sms_ten_key'),
            'sms_ten_tpl_id' => $this->systemInfo('sms_ten_tpl_id'),
            'sms_ten_sign' => $this->systemInfo('sms_ten_sign'),
        ]);*/
    }

}