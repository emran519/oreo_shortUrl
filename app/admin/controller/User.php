<?php


namespace app\admin\controller;


use oreo\lib\Controller;
use oreo\lib\Db;

class User extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->auth();
    }

    public function userList(){
        return \oreo\lib\View::display('admin/user/userList');
    }

    public function userDataList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('user')->limit($page,$limit)->all();
        //查询总数
        $count_list = Db::table('user')->count();  //总数
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','count'=>$count_list,'data'=>$list];
    }

    //添加新用户
    public function addUserData(){
        $data = request()->post(); //获取Post对象
        $email = preg_replace('/[ ]/','',$data['email']);//移除前后空格登录账号
        if(!$email) return json(0,'邮箱不能为空');
        $phone = preg_replace('/[ ]/','',$data['phone']);//移除前后空格登录账号
        if(!$phone) return json(0,'手机号不能为空');
        $password = preg_replace('/[ ]/','',$data['password']);//登录密码
        if(!$password) return json(0,'登录密码不能为空');
        $res = Db::table('user')->where("email=:email")->bind(':email',$email)->find();
        if(!empty($res)) return json(0,'邮箱已被绑定');
        $res2 = Db::table('user')->where("phone=:phone")->bind(':phone',$phone)->find();
        if(!empty($res2)) return json(0,'手机号码已被绑定');
        $param = [
            'phone'   =>  $phone,
            'email'   =>  $email,
            'qq_num'   =>  $data['qq_num'],
            'password'   =>  md5(config("app.admin_salt").$password),//管理员密码
            'create_time'=>  date('Y-m-d H:i:s'),//添加时间
            'state'      =>  $data['state']//状态
        ];
        Db::table('user')->insert($param);
        return json(200,'添加成功');
    }

    //编辑用户账号
    public function editUserData(){
        $data = request()->post(); //获取Post对象
        if(!$data['id']) return json(0,'表单错误');
        $email = preg_replace('/[ ]/','',$data['email']);//移除前后空格登录账号
        if(!$email) return json(0,'邮箱不能为空');
        $phone = preg_replace('/[ ]/','',$data['phone']);//移除前后空格登录账号
        if(!$phone) return json(0,'手机号不能为空');
        $res = Db::table('user')->where('id',$data['id'])->find();
        if (empty($res)) return json(0,'用户账号不存在');
        if($res['email']!=$email){
            $user = Db::table('user')->where('email',$email)->find();
            if(!empty($user))return json(0,'此邮箱已被占用');
        }
        if($res['phone']!=$phone){
            $user = Db::table('user')->where('phone',$phone)->find();
            if(!empty($user))return json(0,'此手机号码已被占用');
        }
        if($data['password']){
            $password = preg_replace('/[ ]/', '', $data['password']);//登录密码
            $param['password'] = md5(\config("app.admin_salt").$password);
        }
        $param['email'] = $email;
        $param['phone'] = $phone;
        $param['qq_num'] = $data['qq_num'];
        $param['state'] = $data['state'];
        Db::begin();
        try {
            Db::table('user')->where('id',$data['id'])->update($param);
            Db::commit();
            return json(200,'更新成功');
        }catch (\Exception $e){
            Db::rollback();
            return json(0,'更新失败',$e->getMessage());
        }
    }

    //删除用户
    public function delUserData(){
        $data = request()->post(); //获取Post对象
        if(!$data['id']) return json(0,'ID不能为空');
        $authUser = Db::table('user')->where(['id'=>$data['id']])->find();
        if(empty($authUser)) return json(0,'用户信息不存在');
        Db::begin();
        try {
            Db::table('user')->where('id',$authUser['id'])->delete();
            Db::table('short_url_log')->where('user_id',$authUser['id'])->delete();
            Db::table('short_url')->where('user_id',$authUser['id'])->delete();
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return json(0,'删除失败');
        }
        return json(200,'删除成功');
    }
}