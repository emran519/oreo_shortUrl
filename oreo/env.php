<?php
define("RUNTIME",dirname(__DIR__) . DS ."runtime" . DS);
define('IS_CLI', preg_match("/cli/i", php_sapi_name()) ? true : false);
define("DATETIME",date("Y-m-d H:i:s"));
//$home = $_SERVER['REQUEST_URI'];
//if (!empty($_SERVER['QUERY_STRING'])) {
//    $home = substr($_SERVER['REQUEST_URI'], 0, strpos($home, '?'));
//}
//if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
//    $home = str_replace($_SERVER['PATH_INFO'], '', $home);
//} else {
//    $home = substr($home, 0, strrpos($home, '/'));
//}
//if (!isset($_SERVER['REQUEST_SCHEME'])) {
//    $_SERVER['REQUEST_SCHEME'] = 'http';
//}
define('__HOME__', '//' . $_SERVER['HTTP_HOST']);