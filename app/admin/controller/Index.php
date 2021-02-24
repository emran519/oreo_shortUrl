<?php


namespace app\admin\controller;

use oreo\lib\Controller;
use oreo\lib\Db;
class Index extends Controller
{

    public function __construct(){
        parent::__construct();
        $this->auth();
    }

    //首页
    public function index(){
        return view('admin/home', [
            'web_name' => $this->systemInfo('web_name')['value']
        ]);
    }

    //获取导航栏
    public function adminNavBar(){
        //权限
        $ps = Db::table('auth_permission')->field('menu_id')->where("user_id=:id")->bind(':id', $_SESSION['admin_info']['role_id'])->all();
        if(empty($ps)) return json(200,'无任何权限');
        $ps_menu_ids = array();
        for ($i = 0; $i < count($ps); $i++) {
            $ps_menu_ids[$i] = $ps[$i]['menu_id'];
        } //循环出该有的权限
        $str = implode(',', $ps_menu_ids);
        $menus = Db::table('auth_menu')
            ->field('id,name as title,parent_id,app_name as url1,action_name as url2,function_name as url3,icon,spread',false)
            ->where("id in ($str) and (`is_menu` = '1')")->order('sort','asc')->all();//查询该ID，菜单权限
        foreach ($menus as $k => $v) {
            if($v['url2']){
                $menus[$k]['href'] = $v['url1'] . '/' . $v['url2'] . '/' . $v['url3'];
            }else{
                $menus[$k]['href'] =  $v['url1'];
            }
            unset($v['url1'],$v['url2'],$v['url3']);
        }
        $tree = $this->getTree($menus,0);
        return json(200,'',$tree);
    }

    private function getTree($list,$pid)
    {
        $tree = array();
        foreach($list as $k => $v)
        {
            if($v['parent_id'] == $pid)
            {         //父亲找到儿子
                $v['children'] = $this->getTree($list, $v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    function __destruct(){}

}