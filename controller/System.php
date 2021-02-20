<?php

namespace controller;

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
        return view('system/website', [
            'web_name' => $this->systemInfo('web_name'),
            'web_url' => $this->systemInfo('web_url'),
            'icp_num' => $this->systemInfo('icp_num'),
            'system_state' => $this->systemInfo('system_state'),
            'system_state_text' => $this->systemInfo('system_state_text')
        ]);
    }

}