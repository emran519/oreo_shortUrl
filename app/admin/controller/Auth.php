<?php


namespace app\admin\controller;

use oreo\lib\Controller;
use oreo\lib\Db;

class Auth extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->auth();
    }

    //权限规则管理页面
    public function permissionList(){
        return \oreo\lib\View::display('admin/auth/permissionList');
    }

    //查看权限列表
    public function adminMenuList(){
        $type = request()->post('type'); //获取Post对象
        if($type==1){
            $list = Db::table('auth_menu')->order('sort','asc')->all();
        }else{
            $res = Db::table('auth_menu')->field('id,name,parent_id')->order('sort','asc')->all();
            $r = \oreo\extend\NodeTree::makeTree($res);
            return json_encode($r,JSON_UNESCAPED_UNICODE);
        }
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        return json(200,'成功',$list);
    }

    //添加新的权限规则
    public function addAdminMenuData(){
        $param = request()->post(); //获取Post对象
        if(!$param['name']) return json(0,'菜单名称不能为空');
        if(!$param['app_name']) return json(0,'应用名称不能为空');
        if(!$param['action_name']) return json(0,'控制器名称不能为空');
        if(!$param['function_name']) return json(0,'方法名称不能为空');
        if($param['parent_id']==''){
            $parent_id=0;
        }else{
            $parent_id=$param['parent_id'];
        }
        $data = [
            'name'  =>  $param['name'], //菜单名称
            'parent_id' =>  $parent_id,//父级菜单
            'is_menu'  => $param['is_menu'],//是否菜单
            'icon'  => $param['icon'],//icon图标
            'spread'  => $param['spread'],//1=>展开;0=>不展开
            'app_name'  =>  $param['app_name'],//应用名称
            'action_name'  =>  $param['action_name'],//控制器名称
            'function_name'  =>  $param['function_name'],//方法名称
            'sort'  =>  $param['sort'],//排序数值
            'create_time'  =>  date('Y-m-d H:i:s'),//添加时间
            'update_time'  =>  date('Y-m-d H:i:s'),//更新时间
        ];
        Db::table('auth_menu')->insert($data);
        return json(200,'添加成功');
    }

    //编辑规则
    public function editAdminMenuData(){
        $menuID = request()->post('menu_id'); //规则ID
        if(!$menuID)  return json(0,'标识ID不能为空');
        //先查询
        $isMenu = Db::table('auth_menu')->where("id=:id")->bind(':id',$menuID)->find();
        if (empty($isMenu)) return json(0,'当前规则不存在');
        $fieldName = request()->post('field_name'); //字段
        $isValue = request()->post('is_value'); //值
        $param = [
            $fieldName  => $isValue,
            'update_time' => date('Y-m-d H:i:s')
        ];
        Db::table('auth_menu')->where(['id'=>$menuID])->update($param);
        return json(200,'修改成功');
    }

    //删除规则
    public function delPermissionData(){
        $role_id = request()->post('role_id'); //类型
        if(!$role_id) return json(0,'Id不能为空');
        $retArr = explode(',', $role_id);//分割
        for($i=0;$i<count($retArr);$i++){
            Db::table('auth_menu')->where('id=:id')->bind(':id', $retArr[$i])->delete();
        }
        return json(200,'删除成功');
    }

    //角色管理页面
    public function adminRole(){
        return \oreo\lib\View::display('admin/auth/adminRole');
    }

    //角色列表查询
    public function adminRoleList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('auth_role')->limit($page,$limit)->all();
        //查询总数
        $count_list = Db::table('auth_role')->count();  //总数
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','count'=>$count_list,'data'=>$list];
    }

    //权限管理-角色管理列表-添加角色
    public function addAdminRoleData(){
        if(!request()->post('role_name')) return json(0,'角色名称不能为空');
        //先查询
        $isRole = Db::table('auth_role')->where("role_name=:role_name")->bind(':role_name',request()->post('role_name'))->find();
        if ($isRole) return json(0,'当前角色已存在');
        $data = [
            'user_id'=>$_SESSION['admin_info']['id'],
            'role_name'=>request()->post('role_name'),
            'represent'=>request()->post('represent'),
            'create_time'=>date('Y-m-d H:i:s'),
            'update_time'=>date('Y-m-d H:i:s')
        ];
        Db::table('auth_role')->insert($data);
        return json(200,'添加成功');
    }

    //权限管理-角色管理列表-编辑角色
    public function editAdminRoleData(){
        if(!request()->post('role_name'))  return json(0,'角色名称不能为空');
        if(!request()->post('role_code')) return json(0,'角色ID不能为空');
        //先查询
        $isRole = Db::table('auth_role')->where("id=:id")->bind(':id',request()->post('role_code'))->find();
        if (empty($isRole)) return json(0,'当前角色不存在');
        $data = [
            'role_name'=>request()->post('role_name'),
            'represent'=>request()->post('represent'),
            'update_time'=>date('Y-m-d H:i:s')
        ];
        Db::table('auth_role')->where("id",request()->post('role_code'))->update($data);
        return json(200,'修改成功');
    }

    //权限管理-角色管理列表-删除角色
    public function delAdminRoleData(){
        if(request()->post('role_id')==1)return json(0,'超级管理员角色不可删除');
        $role = Db::table('auth_role')->where("id=:role_id")->bind(':role_id',request()->post('role_id'))->find();
        if (!$role) return json(0,'当前操作的数据不存在或ID错误');
        Db::table('auth_role')->where("id=:role_id")->bind(':role_id',request()->post('role_id'))->delete();
        //删除角色权限表中的数据
        Db::begin();
        try {
            Db::table('auth_permission')->where("user_id=:user_id")->bind(':user_id',request()->post('role_id'))->delete();
            //删除账号
            Db::table('auth_admin')->where("role_id=:role_id")->bind(':role_id',request()->post('role_id'))->delete();
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
        }
        return json(200,'删除成功');
    }

    //权限管理-权限规则列表-角色权限
    public function rolePermissionData(){
        $list =  Db::table('auth_permission')->field('menu_id')->where('user_id',request()->post('role_id'))->all();
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        return json(200,'获取成功',$list);
    }

    //权限管理-权限规则列表-添加-角色权限
    public function addRolePermissionData(){
        if(!request()->post('role_id')) return json(0,'角色代码不能为空');
        if(!request()->post('menu_id')) return json(0,'权限规则ID不能为空');
        if(!request()->post('thisStatus')) return json(0,'状态不能为空');
        //新增
        if(request()->post('thisStatus')==1){
            $param = [
                'user_id' => request()->post('role_id'),
                'menu_id' => request()->post('menu_id'),
                'create_time'=>date('Y-m-d H:i:s'),
                'update_time'=>date('Y-m-d H:i:s')
            ];
            Db::table('auth_permission')
                ->insert($param);
        }else{
            Db::table('auth_permission')
                ->where( ['user_id'=>request()->post('role_id'),'menu_id'=>request()->post('menu_id')])
                ->delete();
        }
        return json(200,'操作成功');
    }

    //用户管理页面
    public function adminList(){
        return \oreo\lib\View::display('admin/auth/adminList');
    }

    //管理员列表
    public function adminUserList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('auth_admin')->alias('a')
            ->join('oreo_auth_role b','a.role_id = b.id')
            ->field('a.id,b.id as role_id,a.username,a.gender,a.real_name,a.user_phone,a.user_email,a.state,a.create_time,b.role_name')
            ->limit($page,$limit)->all();
        //查询总数
        $count_list = Db::table('auth_admin')->count();  //总数
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','count'=>$count_list,'data'=>$list];
    }

    //添加管理员时查看角色
    public function addAdminRoleList(){
        $list = Db::table('auth_role')->field('id,role_name')->all();
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        return json(200,'获取成功',$list);
    }

    //添加新管理员账号
    public function addAdminData(){
        $data = request()->post(); //获取Post对象
        $username = preg_replace('/[ ]/','',$data['username']);//移除前后空格登录账号
        $username = strtolower($username);//登录账号//登录账号转小写
        if(!$username) return json(0,'管理员用户名不能为空');
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $data['username']) > 0) return json(0,'登录账号不能包含中文字符');
        if(!$data['real_name']) return json(0,'真实姓名不能为空');
        $password = preg_replace('/[ ]/','',$data['password']);//登录密码
        if(!$password) return json(0,'登录密码不能为空');
        $res = Db::table('auth_admin')->where("username=:username")->bind(':username',$username)->find();
        if(!empty($res)) return json(0,'管理员账号已存在');
        $param = [
            'username'   =>  $username,//管理员账号
            'password'   =>  md5(config("app.admin_salt").$password),//管理员密码
            'real_name'  =>  $data['real_name'],//真实姓名
            'user_phone' =>  $data['user_phone'],//联系电话
            'user_email' =>  $data['user_email'],//邮箱
            'gender'     =>  $data['gender'],//性别
            'role_id'    =>  $data['role_id'],//角色ID
            'create_time'=>  date('Y-m-d H:i:s'),//添加时间
            'state'      =>  $data['state']//状态
        ];
        Db::table('auth_admin')->insert($param);
        return json(200,'添加成功');
    }

    //编辑管理员账号
    public function editAdminData(){
        $data = request()->post(); //获取Post对象
        if(!$data['id']) return json(0,'表单错误');
        $username = preg_replace('/[ ]/','',$data['username']);//移除前后空格登录账号
        $username = strtolower($username);//登录账号//登录账号转小写
        if(!$username) return json(0,'管理员用户名不能为空');
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $data['username']) > 0) return json(0,'登录账号不能包含中文字符');
        if(!$data['real_name']) return json(0,'真实姓名不能为空');
        $res = Db::table('auth_admin')->where('id',$data['id'])->find();
        if (empty($res)) return json(0,'管理员账号不存在');
        if($res['username']!=$username){
            //
            $user = Db::table('auth_admin')->where('username',$username)->find();
            if(!empty($user))return json(0,'此账号已被占用,请更换其他新账号');
            $param['username']  = $username;
        }
        if($data['password']){
            $password = preg_replace('/[ ]/', '', $data['password']);//登录密码
            $param['password'] = md5(\config("app.admin_salt").$password);
        }
        $param['real_name'] = $data['real_name'];
        $param['user_phone'] = $data['user_phone'];
        $param['real_name'] = $data['real_name'];
        $param['user_email'] = $data['user_email'];
        $param['gender'] = $data['gender'];
        $param['state'] = $data['state'];
        Db::begin();
        try {
            Db::table('auth_admin')->where('id',$data['id'])->update($param);
            Db::commit();
            return json(200,'更新成功');
        }catch (\Exception $e){
            Db::rollback();
            return json(0,'更新失败',$e->getMessage());
        }
    }

    //删除管理员
    public function delAdminData(){
        $data = request()->post(); //获取Post对象
        if(!$data['id']) return json(0,'ID不能为空');
        $authUser = Db::table('auth_admin')->where(['id'=>$data['id'],'role_id'=>$data['role_id']])->find();
        if(empty($authUser)) return json(0,'用户信息不存在');
        Db::table('auth_admin')->where('id',$authUser['id'])->delete();
        return json(200,'删除成功');
    }


    function __destruct(){}
}