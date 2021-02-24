<?php

namespace oreo\lib;


class Route{
    /**
     * 应用
     */
    static public $app = "";

    /**
     * 控制器
     */
    static public $controller = "";

    /**
     * 方法
     */
    static public $action = "";

    /**
     * 参数
     */
    static public $params = [];

    /**
     * post参数
     */
    static public $rawPost = null;

    /**
     * 应用/控制器/方法
     */
    static public $privileges = "";


    public function __construct(){}

    static public function parseUrl(){
        //静态化
        $url_html_suffix = Config::get("app.url_html_suffix");
        if(!empty($url_html_suffix) && substr($_SERVER['QUERY_STRING'],-strlen($url_html_suffix)) == $url_html_suffix){
            $_SERVER['QUERY_STRING'] = substr($_SERVER['QUERY_STRING'], 0,-strlen($url_html_suffix));
        }
        //获取URL传参
        parse_str($_SERVER['QUERY_STRING'],$queryStringParams);
        //检测是否有输入应用地址
        isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO']) ? $isPathinfo=true : $isPathinfo = false;
        //如果有输入应用地址
        if($isPathinfo){
            if(!empty($url_html_suffix) && substr($_SERVER['PATH_INFO'],-strlen($url_html_suffix)) == $url_html_suffix){
                $_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'], 0,-strlen($url_html_suffix));
            }
            $path_info = trim($_SERVER['PATH_INFO'],'/');
            $arr = explode("/",$path_info);
            self::$app = array_shift($arr); //应用
            self::$controller = array_shift($arr);//控制权
            self::$action = array_shift($arr);//方法
            self::$params = $queryStringParams;//参数
        }else{
           //如果未指定应用则是默认应用
            self::$app = Config::get("app.route.default_app");//应用
            self::$controller = Config::get("app.route.default_class");//控制权
            self::$action = Config::get("app.route.default_fun")?:"index";//方法
            self::$params = $queryStringParams;
        }

        self::$rawPost = file_get_contents("php://input");
        //如果控制器为空且控制器为index 则 控制等于 当前应用的
        if(empty(self::$controller) || stripos(self::$controller,"index.php") !== false)self::$controller="Index";
        if(empty(self::$action) || stripos(self::$action,"index.php") !== false)self::$action = "index";
        self::$controller[0] = strtoupper(self::$controller[0]);
        self::$privileges = self::$app.'.'.self::$controller.'.'.self::$action;
        SafetyCheck::run();
    }
    static public function parseCli(){
        //
        $params = [];
        if($_SERVER['argc'] > 1){
            $args_list = array_slice($_SERVER['argv'],1);
            foreach($args_list as $arg){
                if(strpos($arg,"=") !== false){
                    $pos = strpos($arg,"=");
                    $arg_k = trim(substr($arg,0,$pos));
                    $arg_v = trim(substr($arg,-(strlen($arg)-($pos+1))));
                    if(!empty($arg_k) && !empty($arg_v)){
                        if(strtolower($arg_k) == "controller"){
                            self::$controller = $arg_v;
                        }else if(strtolower($arg_k) == "action"){
                            self::$action = $arg_v;
                        }else{
                            $params[$arg_k] = trim($arg_v);
                        }
                    }
                }
            }
        }
        self::$params = $params;
        if(empty(self::$controller))self::$controller="Index";
        if(empty(self::$action))self::$action = "index";
        self::$controller[0] = strtoupper(self::$controller[0]);
        self::$privileges = self::$controller.'.'.self::$action;
        //
    }

    static public function run(){
        if(IS_CLI){
            self::parseCli();
        }else{
            self::parseUrl();
        }
        $controllerClassName = "app\\".self::$app."\\controller\\".self::$controller; //控制器路径
        $actionName = self::$action; //方法
        $actionNameLower = strtolower($actionName); //转小写
        if(method_exists($controllerClassName,$actionName)){ //先匹配Url地址中的方法
            $result = invoke($controllerClassName,$actionName);
        }else if(method_exists($controllerClassName,$actionNameLower)){ //匹配 转小写后的方法
            $result = invoke($controllerClassName,$actionNameLower);
        }else{
            $controllerClassName = "app\\".Config::get("app.route.null_route.app")."\\controller\\".Config::get("app.route.null_route.controller");
            $actionName = Config::get("app.route.null_route.action"); //方法
            self::$action = $actionName;
            method_exists($controllerClassName,$actionName);
            $result = invoke($controllerClassName,$actionName);
        }
        /*
        if(!IS_CLI && \core\Config::get("sys.session")){
            \core\Session::clear();
            \core\Session::save();
        }
        */
        //格式化输出
        if(isset($result)){
            if(IS_CLI){
                Response::html($result);
            }else{
                $response_type = Config::get("app.response_type"); //输出格式
                switch($response_type){
                    case "html":Response::html($result);break;
                    case "json":Response::json($result);break;
                    case "xml":Response::xml($result);break;
                    default : Response::html($result);
                }
            }
        }
    }

    public static function version(){
        return '2.0';
    }

    public function __destruct(){}
}
