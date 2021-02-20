<?php

namespace oreo\lib;

use controller\Iframe;

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
				echo json_encode(["status"=>"error","msg"=>"授权失败"]);die();
			}else{
                echo('{"code":-2,"msg":"Session验证失败"}');
                header("location:".url("../Login"));
			}
		}
        $is_controller = Route::$controller; //控制器名称
        $is_action = Route::$action;//方法名称
        //拼接
        $auths =   $is_controller . '/' . $is_action;
        if($auths == 'Home/index'){
            $auths = 'Home';
        }
        if($auths != 'Home/adminNavBar' && $is_controller != 'Iframe' && $is_controller != 'Index'){
            if($is_action != 'rolePermissionData' && $is_action != 'addAdminRoleList'){
               $this->adminGroup($auths);
            }
        }
	}

    /**
     * 获取权限信息
     * @param string $auths
     * @return string[]
     */
	private function adminGroup(string $auths){
        //admin
        $admin = Db::table('auth_admin')->where("id=:id")->bind(':id', $_SESSION['admin_info']['id'])->find();
        if($admin['state']!=1) exit('{"code":407,"msg":"您的账号被封禁，更多问题请联系官方客服咨询"}');
        //role
        $ps = Db::table('auth_permission')->where('user_id',$admin['role_id'])->all();
        if (count($ps) < 1) {
            return array('ok'=>'2');
        }
        $ps_menu_ids = array();
        for ($i = 0; $i < count($ps); $i++) {
            $ps_menu_ids[$i] = $ps[$i]['menu_id'];
        }
        $str = implode(',', $ps_menu_ids);
        $menus = Db::table('auth_menu')->where("id in ($str)")->all();
        for ($i = 0; $i < count($menus); $i++) {
            $permissions[$i] = $menus[$i]['action_name'].$menus[$i]['function_name'];
        }
        if (!in_array(strtolower($auths), array_map('strtolower', $permissions))) {
            if(isAjax()){
                responseType("json");
                echo json_encode(["status"=>"error","msg"=>"无权操作"]);die();
            }else{
                responseType("html");
                echo header("location:".url("../Iframe/errorHelp")); die();
            }
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
