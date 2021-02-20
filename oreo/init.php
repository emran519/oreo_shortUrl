<?php
require(BASE_PATH . "oreo/common.php");
//define("IP",isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1"));

define("DATETIME",date("Y-m-d H:i:s"));
    \oreo\lib\Config::init();
    \oreo\lib\Config::get("app.debug") ? error_reporting(E_ALL | E_STRICT) : error_reporting(0);
    if(\oreo\lib\Config::get("app.safety")){
        \oreo\lib\SafetyCheck::getRedis();
    }
    if(\oreo\lib\Config::get("app.session.state"))session_start();
    define("INDEX_PATH","/index.php/");
    define("STATIC_PATH","static");
    define("UPLOAD_PATH","static/upload/");
    $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";
    define("HTTP_HOST",$http . $_SERVER["HTTP_HOST"]);
$home = $_SERVER['REQUEST_URI'];
if (!empty($_SERVER['QUERY_STRING'])) {
    $home = substr($_SERVER['REQUEST_URI'], 0, strpos($home, '?'));
}
if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    $home = str_replace($_SERVER['PATH_INFO'], '', $home);
} else {
    $home = substr($home, 0, strrpos($home, '/'));
}
if (!isset($_SERVER['REQUEST_SCHEME'])) {
    $_SERVER['REQUEST_SCHEME'] = 'http';
}
define('__HOME__', '//' . $_SERVER['HTTP_HOST'] . $home);
\oreo\lib\Route::run();
