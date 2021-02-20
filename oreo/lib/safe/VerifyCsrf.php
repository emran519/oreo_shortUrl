<?php

namespace oreo\lib\safe;

class verifyCsrf
{
    protected static $originCheck = true; //来源控制

    public static function _checkToken( $key, $origin ){
        if ( !isset( $_SESSION[ 'csrf_' . $key ] ) )
            return false;
        if ( !isset( $origin ) )
            return false;
        $hash = $_SESSION[ 'csrf_' . $key ]; //获取存在session中的token
        //验证来源  根据加密验证
        if( self::$originCheck && sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) != substr( base64_decode( $hash ), 10, 40 ) )
            return false;
        //验证token
        if ( $origin != $hash )
            return false;
        //验证时间
        $expired_time = time() - $_SESSION['token_time'];
        if ($expired_time >= 300)
            return false;
        return true;
    }

    //跳转
    public static function _jump() {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

}