<?php

namespace oreo\lib;


/**
 * Class Controller 控制器类
 * @package oreo\lib
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */

class Controller{

	static public $config = null;

	public function __construct(){
	}

    /**
     * 权限验证
     */
	public function auth(){
		if(empty($_SESSION['admin_info'])){
			if(isAjax()){
				responseType("json");
				echo json_encode(["code"=>"-7","msg"=>"授权失败"]);die();
			}else{
                echo('{"code":-2,"msg":"Session验证失败"}');
                header("location:".url("./admin/Login"));die();
			}
		}
        $is_app = Route::$app; //控制器名称
        $is_controller = Route::$controller; //控制器名称
        $is_action = Route::$action;//方法名称
        //拼接
        if(!empty($is_controller)){
            $auths = $is_app . '/' . $is_controller . '/' . $is_action;
        }else{
            $auths =  $is_app;
        }
        if($auths != $is_app. '/index/adminNavBar' && $is_controller != 'Iframe' && $is_controller != 'Index'){
            if($is_action != 'rolePermissionData' && $is_action != 'addAdminRoleList' && $is_action != 'urlFilterList'){
               $this->adminGroup($auths);
            }
        }
	}

    /**
     * 获取权限信息
     * @param string $auths
     * @return string
     */
	private function adminGroup(string $auths){
        //admin
        $admin = Db::table('auth_admin')->where("id=:id")->bind(':id', $_SESSION['admin_info']['id'])->find();
        if(empty($admin) || $admin['state']!=1) exit('{"code":407,"msg":"您的账号被封禁，更多问题请联系官方客服咨询"}');
        //role
        $ps = Db::table('auth_permission')->alias('a')
            ->join('oreo_auth_menu b', 'b.id = a.menu_id')
            ->where('a.user_id',$admin['role_id'])
            ->all();
        $permissions = [];
        foreach ($ps as $k => $v){
            $permissions[$k] = $v['app_name'].'/'.$v['action_name'].'/'.$v['function_name'];
        }
        if (!in_array(strtolower($auths), array_map('strtolower', $permissions))) {
            echo $this->isResponse('您的用户组无权访问当前模块');
            exit();
        }
        return true;
    }


    private function isResponse(string $msg) {
        if(isAjax()){
            responseType("json");
            return json_encode(["code"=>"-2","msg"=>$msg]);
        }else{
            responseType("html");
            return view('error/error',['text'=>$msg,'web_name'=>$this->systemInfo('web_name')['value']]);
        }
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

    /**
     * 写入log
     * @param string $msg
     * @return bool
     */
	public function admin_log(string $msg){
		$msg = mb_substr($msg,0,150);
		$data= ['admin_id'=>$_SESSION['admin_info']['id'],'route'=>Route::$privileges,'msg'=>htmlspecialchars($msg),'ip_addr'=>request()->ip(),'add_time'=>date('Y-m-d H:i:s')];
		Db::table('admin_log')->insert($data);
		return true;
	}

    /**
     * @param string $msg
     * @return bool
     */
	public function exception_log(string $msg){
		$msg = mb_substr($msg,0,600);
        $data= ['route'=>Route::$privileges,'msg'=>htmlspecialchars($msg),'ip_addr'=>request()->ip(),'add_time'=>date('Y-m-d H:i:s')];
        Db::table('admin_log')->insert($data);
        return true;
	}

	public function __destruct(){}
}
