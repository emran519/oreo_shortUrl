<?php
date_default_timezone_set('Asia/Shanghai');
define("_NOW_",time());
define('DS', DIRECTORY_SEPARATOR);
define("BASE_PATH",dirname(__DIR__) . DS);
define("RUNTIME",dirname(__DIR__) . DS ."runtime" . DS);
define('IS_CLI', preg_match("/cli/i", php_sapi_name()) ? true : false);
require_once "../oreo/init.php";