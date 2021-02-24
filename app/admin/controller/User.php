<?php


namespace app\admin\controller;


use oreo\lib\Db;

class User
{

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

}