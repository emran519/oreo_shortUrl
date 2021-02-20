<?php

namespace oreo\lib;

/**
 * Class SafetyCheck 安全检查类
 * @package oreo\lib
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */
class SafetyCheck{
    /**
     * 过滤提交数据正则
     * @var array
     */
    protected static $filterUrl = [
        'xss' => "\\=\\+\\/v(?:8|9|\\+|\\/)|\\%0acontent\\-(?:id|location|type|transfer\\-encoding)",
    ];
 
    /**
     * 过滤提交数据正则
     * @var array
     */
    protected static $filterArgs = [
        'xss'   => "[\\'\\\"\\;\\*\\<\\>].*\\bon[a-zA-Z]{3,15}[\\s\\r\\n\\v\\f]*\\=|\\b(?:expression)\\(|\\<script[\\s\\\\\\/]|\\<\\!\\[cdata\\[|\\b(?:eval|alert|prompt|msgbox)\\s*\\(|url\\((?:\\#|data|javascript)",
        'sql'   => "[^\\{\\s]{1}(\\s|\\b)+(?:select\\b|update\\b|insert(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+into\\b).+?(?:from\\b|set\\b)|[^\\{\\s]{1}(\\s|\\b)+(?:create|delete|drop|truncate|rename|desc)(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+(?:table\\b|from\\b|database\\b)|into(?:(\\/\\*.*?\\*\\/)|\\s|\\+)+(?:dump|out)file\\b|\\bsleep\\([\\s]*[\\d]+[\\s]*\\)|benchmark\\(([^\\,]*)\\,([^\\,]*)\\)|(?:declare|set|select)\\b.*@|union\\b.*(?:select|all)\\b|(?:select|update|insert|create|delete|drop|grant|truncate|rename|exec|desc|from|table|database|set|where)\\b.*(charset|ascii|bin|char|uncompress|concat|concat_ws|conv|export_set|hex|instr|left|load_file|locate|mid|sub|substring|oct|reverse|right|unhex)\\(|(?:master\\.\\.sysdatabases|msysaccessobjects|msysqueries|sysmodules|mysql\\.db|sys\\.database_name|information_schema\\.|sysobjects|sp_makewebtask|xp_cmdshell|sp_oamethod|sp_addextendedproc|sp_oacreate|xp_regread|sys\\.dbms_export_extension)",
        'other' => "\\.\\.[\\\\\\/].*\\%00([^0-9a-fA-F]|$)|%00[\\'\\\"\\.]",
    ];
	
	private static $warning = [];
    /**
     * 数据过滤
     * @param $filterData
     * @param $filterArgs
     */
    protected static function filterData($filterData, $filterArgs){
        foreach ($filterData as $key => $value) {
            if (!is_array($key)) {
                self::filterCheck($key, $filterArgs);
            } else {
                self::filterData($key, $filterArgs);
            }
            if (!is_array($value)) {
                self::filterCheck($value, $filterArgs);
            } else {
                self::filterData($value, $filterArgs);
            }
        }
    }
 
    /**
     * 数据检查
     * @param $str
     * @param $filterArgs
     */
    protected static function filterCheck($str, $filterArgs){
        foreach ($filterArgs as $key => $value) {
            if (preg_match("/" . $value . "/is", $str) == 1 || preg_match("/" . $value . "/is", urlencode($str)) == 1) {
                //记录日志 - 信息拦截
				self::$warning[] = "[攻击方式：{$key}，时间：".DATETIME."，IP：".IP."，参数：( {$str} )]\n";
            }
        }
        return false;
    }

    /**
     * 数据检查入口
     */
    public static function run(){
        $referer     = empty($_SERVER['HTTP_REFERER']) ? [] : [$_SERVER['HTTP_REFERER']];
        $queryString = empty($_SERVER["QUERY_STRING"]) ? [] : [$_SERVER["QUERY_STRING"]];
        self::filterData($queryString, self::$filterUrl);
        self::filterData($_GET, self::$filterArgs);
        self::filterData($_POST, self::$filterArgs);
        self::filterData($_COOKIE, self::$filterArgs);
        self::filterData($referer, self::$filterArgs);
		if(empty(self::$warning))return true;
		$controller = new \oreo\lib\Controller();
		foreach(self::$warning as $v){
            _log($v);
		}
        self::ipSetRedis();
		\oreo\lib\Error::securityCheck();
    }

    /**
     * 命中安全规则IP列入Redis，存入
     */
    protected static function ipSetRedis(){
        $redis = new \Redis();
        $redis->connect(Config::get("app.redis.host"),Config::get("app.redis.port"));
        $ip = request()->ip();
        if(!empty(Config::get("app.redis.password"))){
            $redis->auth(Config::get("app.redis.password"));
        }
        //判断缓存的键是否还存在
        if($redis->get("web:safe:".request()->ip())!=Config::get("app.safety_frequency")){
            $redis->incr("web:safe:".$ip);
            $redis->expire("web:safe:".$ip,60*30);
        }else{
            \oreo\lib\Error::safetyFailed();
        }
    }

    /**
     * 命中安全规则IP列入Redis，查询
     */
    public static function getRedis(){
        $redis = new \Redis();
        $redis->connect(Config::get("app.redis.host"),Config::get("app.redis.port"));
        $ip = request()->ip();
        if(!empty(Config::get("app.redis.password"))){
            $redis->auth(Config::get("app.redis.password"));
        }
        if($redis->get("web:safe:".$ip)&&$redis->get("web:safe:".$ip)==Config::get("app.safety_frequency"))
        {
            \oreo\lib\Error::safetyFailed();
        }
    }
}
