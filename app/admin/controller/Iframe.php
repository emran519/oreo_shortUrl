<?php

namespace app\admin\controller;

use oreo\lib\Controller;
use oreo\lib\View;
class Iframe extends Controller
{

    public function __construct(){
        parent::__construct();
        $this->auth();
    }

    public function errorHelp(){
        return View::display('error/error');
    }

    public function errorStateHelp(){
        return View::display('error/error_state');
    }

    //权限管理-添加-权限规则
    public function addPermission(){
        return View::display('admin/iframe/auth/addPermission');
    }

    //权限管理-添加-角色
    public function addAdminRole(){
        return View::display('admin/iframe/auth/addAdminRole');
    }

    //权限管理-编辑-角色
    public function editAdminRolePage(){
        return View::display('admin/iframe/auth/editAdminRole');
    }

    //权限管理-编辑-角色权限
    public function editRoleAuthPage(){
        return View::display('admin/iframe/auth/editRoleAuth');
    }

    //权限管理-添加-管理员
    public function addAdminPage(){
        return View::display('admin/iframe/auth/addAdmin');
    }

    //权限管理-编辑-管理员
    public function editAdminPage(){
        return View::display('admin/iframe/auth/editAdmin');
    }

    //域名管理-添加-域名
    public function addDomain(){
        return View::display('admin/iframe/domain/addDomain');
    }

    //域名管理-编辑-域名
    public function editDomain(){
        return View::display('admin/iframe/domain/editDomain');
    }

    //域名过滤设置-添加-过滤
    public function addUrlFilter(){
        return View::display('admin/iframe/domain/addUrlFilter');
    }

    //域名过滤设置-编辑-过滤
    public function editUrlFilter(){
        return View::display('admin/iframe/domain/editUrlFilter');
    }

    //用户管理-添加
    public function addUserPage(){
        return View::display('admin/iframe/user/addUser');
    }

    //用户管理-编辑
    public function editUserPage(){
        return View::display('admin/iframe/user/editUser');
    }

    function __destruct(){}

}