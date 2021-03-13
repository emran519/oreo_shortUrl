<?php

namespace app\home\controller;

use oreo\lib\Db;

class Tool extends Base
{

    public function recoveryUrl(){
        return view('home/recoveryUrl',[
            'user_email' => $this->user_info['email']?:"0",
            'user_phone' => $this->user_info['phone']?:"0",
            'user_qq' => $this->user_info['qq_num']?:"0"
        ]);
    }

    public function shortUrl(){
        return view('home/shortUrl',[
            'user_email' => $this->user_info['email']?:"0",
            'user_phone' => $this->user_info['phone']?:"0",
            'user_qq' => $this->user_info['qq_num']?:"0"
        ]);
    }

    //解析链接
    public function longUrlFormat(){
        $short_name = request()->post('short_name');
        if(empty($short_name))return json(0,'短连接地址不能为空');
        $res = Db::table('short_url')->where('address=:short_add')->bind(':short_add',$short_name)->field('target')->find();
        if(empty($res))return json(0,'短连接不存在或有误');
        $longUrl = base64_decode($res['target']);
        return json(200,'解析成功',"$longUrl");
    }
}