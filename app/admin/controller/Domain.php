<?php


namespace app\admin\controller;


use oreo\lib\Controller;
use oreo\lib\Db;

class Domain extends Controller
{

    public function __construct(){
        parent::__construct();
        $this->auth();
    }

    //域名配置-域名列表
    public function domainConfig(){
        return \oreo\lib\View::display('admin/domain/domainList');
    }

    //查看域名配置列表
    public function domainList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('domain')
            ->alias('a')
            ->join('oreo_domain_filter b','a.filter_id = b.id')
            ->field('a.id,a.domain,a.cycle,a.safe,a.safe_tpl,a.state,a.create_time,b.id as filter_id,b.filter_name')
            ->limit($page,$limit)->all();
        //查询总数
        $count_list = Db::table('domain')->count();  //总数
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','count'=>$count_list,'data'=>$list];
    }

    //添加域名
    public function addDomainData(){
        $domain = preg_replace('/[ ]/','',request()->post('domain'));//移除前后空格域名
        $domain = strtolower($domain);//域名转小写
        if(!$domain) return json(0,'域名不能为空');
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $domain) > 0) return json(0,'域名不能包含中文字符');
        $safe = request()->post('safe');//防红;1=>开;2=>关闭
        $tpl = request()->post('safe_tpl');
        if($safe==1 && empty($tpl))return json(0,'防红守护模式下，请选择防红模板');
        $cycle = request()->post('cycle/d',0);//生命周期
        $state = request()->post('state');//状态;1=>正常生成;2=>登录后生成;3=>停止服务
        $filter = request()->post('filter_id');//过滤组ID
        if(!$state) return json(0,'请选择状态');
        if(!$filter)return json(0,'请选择过滤组，如您未添加过滤组，请先添加后再操作');
        //查询
        $res = Db::table('domain')->where("domain=:domain")->bind(':domain',$domain)->find();
        if(!empty($res)) return json(0,'该域名已存在，请勿重复添加');
        $param = [
            'domain'=> $domain,//域名
            'filter_id'=> $filter,//过滤组ID
            'cycle' => $cycle,//生命周期
            'safe' => $safe,//防红;1=>开;2=>关闭
            'safe_tpl' => $tpl,
            'state' => $state,//状态;1=>正常;2=>停止
            'create_time'=>  date('Y-m-d H:i:s'),//添加时间
        ];
        Db::table('domain')->insert($param);
        return json(200,'添加成功');
    }

    //编辑域名
    public function editDomainData(){
        if(empty(request()->post('id'))) return json(0,'表单错误');
        $domain = preg_replace('/[ ]/','',request()->post('domain'));//移除前后空格域名
        $domain = strtolower($domain);//域名转小写
        if(!$domain) return json(0,'域名不能为空');
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $domain) > 0) return json(0,'域名不能包含中文字符');
        $cycle = request()->post('cycle/d',0);//生命周期
        $state = request()->post('state');//状态;1=>正常生成;2=>登录后生成;3=>停止服务
        $safe = request()->post('safe');//防红;1=>开;2=>关闭
        $tpl = request()->post('safe_tpl');
        if(!$state) return json(0,'请选择状态');
        $filter = request()->post('filter_id');//过滤组ID
        if(!$filter)return json(0,'请选择过滤组，如您未添加过滤组，请先添加后再操作');
        if($safe==1 && empty($tpl))return json(0,'防红守护模式下，请选择防红模板');
        //查询
        $res = Db::table('domain')->where("id=:id")->bind(':id',request()->post('id'))->find();
        if(empty($res)) return json(0,'当前域名信息不存在，请刷新后再试');
        $param = [
            'domain'=> $domain,//域名
            'filter_id'=> $filter,//过滤组ID
            'cycle' => $cycle,//生命周期
            'safe' => $safe,//防红;1=>开;2=>关闭
            'safe_tpl' => $tpl,
            'state' => $state,//状态;1=>正常;2=>停止
        ];
        Db::table('domain')->where('id',request()->post('id'))->update($param);
        return json(200,'编辑成功');
    }

    //域名配置-域名列表-删除
    public function delDomainData(){
        $id = request()->post('id'); //id
        if(!$id) return json(0,'Id不能为空');
        Db::begin();
        try {
            Db::table('domain')->where('id=:id')->bind(':id', $id)->delete();
            Db::table('short_url')->where('domain_id=:domain_id')->bind(':domain_id', $id)->delete();
            Db::commit();
            return json(200,'删除成功');
        }catch (\Exception $e){
            Db::rollback();
        }
        return json(0,'删除失败');
    }

    //域名配置-短网址列表
    public function shortDomain(){
        return \oreo\lib\View::display('admin/domain/shortDomainList');
    }

    //域名配置-短网址列表
    public function shortDomainList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('short_url')
            ->alias('a')
            ->join('oreo_domain b','a.domain_id = b.id')
            ->field('b.id as domain_id,b.domain,a.domain_id,a.id,a.address,a.cycle,a.record,a.create_time,a.end_time,b.safe')
            ->limit($page,$limit)
            ->all();
        //查询总数
        $count_list = Db::table('short_url')->count();  //总数
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','count'=>$count_list,'data'=>$list];
    }

    //域名配置-短网址列表-续签
    public function ShortDomainTime(){
        $id = request()->post('id'); //id
        $cycle = request()->post('cycle/d',0);//续签生命周期
        if(!$id) return json(0,'Id不能为空');
        if($cycle!=0){
            $param = [
                'cycle' => $cycle,//生命周期
                'end_time'=> strtotime("+$cycle day")//添加时间
            ];
        }else{
            $param = [
                'cycle' => $cycle,//生命周期
            ];
        }
        Db::table('short_url')->where('id',$id)->update($param);
        return json(200,'续签成功');
    }

    //域名配置-短网址列表-删除
    public function delShortDomain(){
        $id = request()->post('id'); //id
        if(!$id) return json(0,'Id不能为空');
        Db::table('short_url')->where('id=:id')->bind(':id', $id)->delete();
        return json(200,'删除成功');
    }

    //域名配置-短网址列表-删除全部失效链接
    public function delAllShortDomain(){
        $now = time();
        $res = Db::table('short_url')->alias('a')
            ->join('oreo_domain b','a.domain_id = b.id')
            ->where("b.cycle<>:cycle and a.end_time<:end_time")
            ->bind([':cycle'=>0,':end_time'=>$now])
            ->field('a.id')
            ->all();
        if(empty($res))return json(0,'未发现失效短网址');
        $ids = array();
        for ($i = 0; $i < count($res); $i++) {
            $ids[$i] = $res[$i]['id'];
        }
        $str = implode(',', $ids);
        Db::table('short_url')->where("id in ($str)")->delete();
        return json(200,'删除成功');
    }

    //域名配置-域名过滤设置-列表
    public function shortUrlFilter(){
        return \oreo\lib\View::display('admin/domain/shortUrlFilter');
    }

    public function shortUrlFilterList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('domain_filter')->limit($page,$limit)->all();
        //查询总数
        $count_list = Db::table('domain_filter')->count();  //总数
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','count'=>$count_list,'data'=>$list];
    }

    public function addUrlFilter(){
        $name = request()->post('filter_name');//名称
        $content = request()->post('filter_content');//过滤内容
        if(empty($name) || empty($content))return json(0,'各项不能为空');
        $param = [
            'filter_name'=> $name,//域名
            'filter_content' => $content,//生命周期
            'create_time'=>  date('Y-m-d H:i:s'),//添加时间
        ];
        Db::table('domain_filter')->insert($param);
        return json(200,'添加成功');
    }

    public function editUrlFilter(){
        $id = request()->post('id');//ID
        $name = request()->post('filter_name');//名称
        $content = request()->post('filter_content');//过滤内容
        if(empty($name) || empty($content))return json(0,'各项不能为空');
        $param = [
            'filter_name'=> $name,//域名
            'filter_content' => $content//生命周期
        ];
        Db::table('domain_filter')->where('id',$id)->update($param);
        return json(200,'编辑成功');
    }

    public function delUrlFilter(){
        $id = request()->post('id'); //id
        if(!$id) return json(0,'Id不能为空');
        Db::table('domain_filter')->where('id=:id')->bind(':id', $id)->delete();
        return json(200,'删除成功');
    }

    public function urlFilterList(){
        $list = Db::table('domain_filter')->field('id,filter_name')->all();
        if(empty($list)){ //如果没有数据
            return json(0,'暂无数据');
        }
        return json(200,'获取成功',$list);
    }
}