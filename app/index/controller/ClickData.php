<?php


namespace app\index\controller;


use oreo\lib\Db;

class ClickData
{
    public static $agent;
    public static $lang;
    public static $browser;
    public static $system;

    public function __construct()
    {
        self::$agent = $_SERVER['HTTP_USER_AGENT'];
        $phone = request()->isMobile();
        //获取语言
        self::$lang = self::getLang();
        //获取浏览器
        self::$browser = self::getBrowse();
        //如果是手机浏览则获取手机系统
        if(!empty($phone)){
            self::$system = self::mobileType();
        }else{
            self::$system = self::systemType();
        }
    }

    public static function setShorUrlLog($short_id,$user_id){
        $param = [
          'short_id' => $short_id,
            'user_id' => $user_id,
            'lang' => self::$lang,
            'browser' => self::$browser,
            'system' => self::$system,
            'ip_address' => request()->ip(),
            'city' => '',
            'add_time' => date('Y-m-d H:i:s')
        ];
        Db::table('short_url_log')->insert($param);
        return true;
    }

    private static function getBrowse() {
        if (preg_match('/Triden/i',self::$agent)) {
            $Browser = '1';//IE
        }
        elseif (preg_match('/Edg/i',self::$agent)) {
            $Browser = '2';//Edg
        }
        elseif (preg_match('/Chrome/i',self::$agent)) {
            $Browser = '3';//Chrome
        }
        elseif (preg_match('/Firefox/i',self::$agent)) {
            $Browser = '4'; //Firefox
        }
        elseif (preg_match('/Safari/i',self::$agent)) {
            $Browser = '5';//Safari
        }
        elseif (preg_match('/Opera/i',self::$agent)) {
            $Browser = '6';//Opera
        } else {
            $Browser = '0';//Other
        }
        return $Browser;
    }

    private static function getLang() {
        $Lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
        //使用substr()截取字符串，从 0 位开始，截取4个字符
        if (preg_match('/zh-c/i',$Lang)) {
            //preg_match()正则表达式匹配函数
            $Lang = '1';//简体中文
        } elseif (preg_match('/zh/i',$Lang)) {
            $Lang = '2';//繁體中文
        } else {
            $Lang = '3';//English
        }
        return $Lang;
    }

    private static function systemType(){
        if (preg_match('/win/i', self::$agent)) {
            $system = 1; //Windows
        } elseif(preg_match('/linux/i', self::$agent)) {
            $system = 2;//Linux
        } elseif(preg_match('/unix/i', self::$agent)) {
            $system = 3;//Unix
        } elseif(preg_match('/Mac/i', self::$agent)) {
            $system = 4;//MacOs
        }else{
            $system = '0';//other
        }
        return $system;
    }

    private static function mobileType(){
        if(strpos(self::$agent, 'Android')) {
            $mobile = 5;//android
        } elseif(strpos(self::$agent, 'iphone') || strpos(self::$agent, 'ipod')) {
            $mobile = 6; //iphone
        } else {
            $mobile = '0';//other
        }
        return $mobile;
    }
}