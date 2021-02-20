<?php

namespace oreo\lib\safe;

class Csrf {

    protected static $originCheck = true; //来源控制

    protected static $token = "";

    //根据token   生成session token
    public static function _set_Token( $key=null ) {
        if (empty($key)){
            throw new \Exception("key为NULL");
        }
        $extra = self::$originCheck ? sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) : ''; //判断是否启用来源验证
        $token = base64_encode( time() . $extra . self::_getString( 32 ) ); //一起加密
        $_SESSION[ 'csrf_' . $key ] = $token; //放入session
        self::$token = $token;
        return $token;
    }

    //生成字符串
    protected static function _getString( $length ) {
        $text = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen( $text ) - 1;
        $string = '';
        for ( $i = 0; $i < $length; ++$i ){
            $string .= $text[intval(mt_rand( 0.0, $max ))];
        }
        return $string;
    }

    //放入隐藏域
    public static function _set_Input_Token(  ) {
        //echo "<input type='hidden' name='token' value='".$_SESSION[ 'csrf_' . $key ]."'>";
        return self::$token;
    }

    //生成token过期时间

    protected static function _set_Time() {
        $_SESSION['token_time'] = time();
    }

    //init初始化
    /*public function _init( $token ) {
        CSRF::_set_Token($token);
        CSRF::_set_Input_Token($token);
        CSRF::_set_Time();
    }*/

    public static function token($token){
        self::_set_Token($token);
        self::_set_Time();
        return  self::_set_Input_Token();
    }
}