<?php
/**
 * 返回json格式的函数
 * @param int $code 200为成功则失败
 * @param null $msg 消息内容
 * @param mixed $data data参数
 * @return false|string
 */
function json(int $code,$msg = null, $data = null){
    if($code==200){
        $arr['code'] = $code;
        $arr['msg']  = $msg ? : '成功';
        $arr['data'] = $data;
    }else{
        $arr['code'] = -1;
        $arr['msg']  = $msg ? : '失败';
        $arr['data'] = $data;
    }
    return json_encode($arr,JSON_UNESCAPED_UNICODE);
}
//获取配置
function config($var=null){
    if(empty($var)){
        return \oreo\lib\Config::getAll();
    }else{
        return \oreo\lib\Config::get($var);
    }
}
//读取cache
function getCache($type,$value){
    if($type == 'system'){
        oreo\lib\Cache::$path = BASE_PATH . "runtime/cache/system";
    }
    return oreo\lib\Cache::getCache($value);
}
//写入Cache
function setCache($type,$key,$contents,$expiration){
    if($type == 'system'){
        oreo\lib\Cache::$path = BASE_PATH . "runtime/cache/system";
    }
    return oreo\lib\Cache::setCache($key,$contents,$expiration);
}
//输出日志文件
function _log($log=''){
    $logPath = RUNTIME . "log/";
    if(!is_dir($logPath)){
        mkdir(iconv("UTF-8", "GBK", $logPath),0777,true);
    }
    $filePath = RUNTIME . "log/" .  "web_safe_".date('Y-m-d').".log";
    if(file_exists($filePath)){
        $size = filesize($filePath);
        if($size && $size > 5*1024*1024)rename($filePath,$filePath."-".date('Y_m_d_H_i_s'));
    }
    $file = fopen($filePath,'a+');
    if($file){
        fputs($file,$log."\r\n");
    }
    fclose($file);
    return true;
}
//参数绑定
function invoke($className,$method){
    $method = new \ReflectionMethod($className,$method);
    $methodParams = $method->getParameters();
    $inParams = [];
    if(!empty($methodParams)){
        foreach($methodParams as $methodParam){
            if(!$methodParam->isDefaultValueAvailable() && !isset(\oreo\lib\Route::$params[$methodParam->name])){
                responseType("json");
                echo json_encode(["code"=>"0","msg"=>"参数【".$methodParam->name."】未传入"]);
                exit;
            }
            $paramValue = isset(\oreo\lib\Route::$params[$methodParam->name]) ? \oreo\lib\Route::$params[$methodParam->name] : $methodParam->getDefaultValue();
            $inParams[$methodParam->name] = $paramValue;
        }
    }
    //dump($inParams);
    return $method->invokeArgs(new $className,$inParams);
}
//设置输出数据类型 html|json|xml
function responseType($type){
    return \oreo\lib\Response::setType($type);
}
//Request
function request(){
    return new \oreo\lib\Request();
}
//全局赋值
function assign(array $param){
    foreach($param as $k=>$v) {
        $GLOBALS[$k] = $v;
    }
}
//判断是否Ajax
function isAjax(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        return true;
    }
    return false;
}
//删除目录
function removeDir($dirName){
    if(! is_dir($dirName)){
        return false;
    }
    $handle = @opendir($dirName);
    while(($file = @readdir($handle)) !== false){
        if($file != '.' && $file != '..'){
            $dir = $dirName . '/' . $file;
            is_dir($dir) ? removeDir($dir) : @unlink($dir);
        }
    }
    closedir($handle);
    return rmdir($dirName) ;
}
//自动加载
spl_autoload_register("autoload");
function autoload($className){
    if(strpos($className,"\\") !== false){
        $pathArr = explode("\\", $className);
        $pathArr[count($pathArr)-1][0] = strtoupper($pathArr[count($pathArr)-1][0]);
        $filePath = BASE_PATH;
        foreach($pathArr as $path){
            $filePath .= $path . DS;
        }
        $filePath = rtrim($filePath,DS) . ".php";
    }else{
        $className[0] = strtoupper($className[0]);
        $filePath = BASE_PATH . $className . ".php";
    }
    if(file_exists($filePath)){
        require_once($filePath);
        if(!class_exists($className)){
            if(end($pathArr) == \oreo\lib\Route::$controller){
                // \oreo\lib\Error::urlErr($className);
                \oreo\lib\Error::emptyClass($className);
            }else{
                \oreo\lib\Error::emptyClass($className);
            }
        }
    }else{
        if(oreo\lib\Config::get("app.route.existent")){
            return false;
        }
        \oreo\lib\Error::emptyFile($filePath);
    }
}
//生成控制器地址
function url($addr="",$param=[]){
    $url_html_suffix = oreo\lib\Config::get("app.url_html_suffix");
    $index = oreo\lib\Config::get("app.path_info_index") ? "/index.php/" : "/";
    $isPathinfo = (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) || oreo\lib\Config::get("app.path_info_only") ? true : false;
    if($isPathinfo){
        if($addr == ""){
            $url = $index . oreo\lib\Route::$controller . "/" . oreo\lib\Route::$action;
        }else if(strpos($addr,"/") === false){
            $url = $index . oreo\lib\Route::$controller . "/" . $addr;
        }else{
            $url = $index . $addr;
        }
        if(!empty($param)){
            foreach($param as $k=>$v){
                $url .= "/".$k."/".$v;
            }
        }
        $url .= $url_html_suffix;
    }else{
        if($addr == ""){
            $url = "/index.php?controller=" . oreo\lib\Route::$controller . "&action=" . oreo\lib\Route::$action;
        }else if(strpos($addr,"/") === false){
            $url = "/index.php?controller=" . oreo\lib\Route::$controller . "&action=" . $addr;
        }else{
            $addr = trim($addr,"/");
            $addrArr = explode("/",$addr);
            $url = "/index.php?controller=" . array_shift($addrArr) . "&action=" . array_shift($addrArr);
            if(!empty($addrArr) && count($addrArr) % 2 == 0){
                for($i=0;$i<count($addrArr);$i=$i+2){
                    if(isset($addrArr[$i+1]) && !isset($param[$addrArr[$i]])){
                        $param[$addrArr[$i]] = $addrArr[$i+1];
                    }else if(!isset($addrArr[$i+1])){
                        $param[$addrArr[$i]] = "";
                    }
                }
            }
        }
        if(!empty($param)){
            foreach($param as $k=>$v){
                $url .= "&".$k."=".$v;
            }
        }
    }
    return $url;
}
//快速返回模版输出
function view($tplPath=null,$data=null){
    \oreo\lib\View::assign($data);
    return \oreo\lib\View::display($tplPath);
}
function ctr($data){
    return __HOME__ .'/'. oreo\lib\Route::$controller . '/' . $data;
}
