<?php
use oreo\lib\Config;
use oreo\lib\Route;
define("_NOW_",time());
define('DS', DIRECTORY_SEPARATOR);
define("BASE_PATH",dirname(__DIR__) . DS);
require(BASE_PATH . "oreo/common.php");
require(BASE_PATH . "oreo/env.php");
spl_autoload_register("autoload");
Config::init();
Config::get("app.debug") ? error_reporting(E_ALL | E_STRICT) : error_reporting(0);
if(Config::get("app.safety")){
    \oreo\lib\SafetyCheck::getRedis();
}
if(Config::get("app.session.state")){
    session_start();
}
echo Route::run();
