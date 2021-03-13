<?php


namespace app\home\controller;


use oreo\lib\Db;

class Base
{

    public $user_id;
    public $user_info;

    public function __construct()
    {
        if(empty($_SESSION['user_info'])){
            if(isAjax()){
                responseType("json");
                echo json_encode(["code"=>"-7","msg"=>"请登录后操作"]);die();
            }else{
                echo('{"code":-2,"msg":"登录验证失败"}');
                header("location:".url("./home/Login"));die();
            }
        }
        $this->user_id = $_SESSION['user_info']['id'];
        $this->user_info = $_SESSION['user_info'];
    }

    /**
     * 查询系统变量
     * @param string $fieldName 变量名
     * @return false|mixed
     */
    protected function systemInfo(string $fieldName){
        if(!getCache('system',$fieldName)) {
            $fieldName = preg_replace('/[ ]/', '', $fieldName);
            $conf = Db::table('system')->where("name=:name")->bind(':name',$fieldName)->find();
            setCache('system',$fieldName, $conf,604800);
            return $conf;
        }else{
            return getCache('system',$fieldName);
        }
    }

    public function __destruct(){}

}