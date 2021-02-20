<?php


namespace controller;


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
        return \oreo\lib\View::display('system/domain');
    }

    //查看域名配置列表
    public function domainList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('domain')->limit($page,$limit)->all();
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
        $state = request()->post('state');//状态;1=>正常;2=>停止
        //查询
        $res = Db::table('domain')->where("domain=:domain")->bind(':domain',$domain)->find();
        if(!empty($res)) return json(0,'该域名已存在，请勿重复添加');
        $param = [
            'domain'=> $domain,//域名
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
        $state = request()->post('state');//状态;1=>正常;2=>停止
        $safe = request()->post('safe');//防红;1=>开;2=>关闭
        $tpl = request()->post('safe_tpl');
        if($safe==1 && empty($tpl))return json(0,'防红守护模式下，请选择防红模板');
        //查询
        $res = Db::table('domain')->where("id=:id")->bind(':id',request()->post('id'))->find();
        if(empty($res)) return json(0,'当前域名信息不存在，请刷新后再试');
        $param = [
            'domain'=> $domain,//域名
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
            Db::table('domain_text')->where('domain_id=:domain_id')->bind(':domain_id', $id)->delete();
            Db::commit();
            return json(200,'删除成功');
        }catch (\Exception $e){
            Db::rollback();
        }
        return json(0,'删除失败');
    }

    //域名配置-短网址列表
    public function shortDomain(){
        return \oreo\lib\View::display('system/shortDomain');
    }

    //域名配置-短网址列表
    public function shortDomainList(){
        $limit = request()->get('limit/d',1);//如果有范围就默认范围否则为范围为1
        $page = intval( (request()->get('page/d',1) - 1) * $limit);
        //分页查询
        $list = Db::table('domain_text')
            ->alias('a')
            ->join('oreo_domain b','a.domain_id = b.id')
            ->field('b.id as domain_id,b.domain,a.domain_id,a.id,a.address,a.cycle,a.record,a.create_time,a.end_time,b.safe')
            ->limit($page,$limit)
            ->all();
        //查询总数
        $count_list = Db::table('domain_text')->count();  //总数
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
        Db::table('domain_text')->where('id',$id)->update($param);
        return json(200,'续签成功');
    }

    //域名配置-短网址列表-删除
    public function delShortDomain(){
        $id = request()->post('id'); //id
        if(!$id) return json(0,'Id不能为空');
        Db::table('domain_text')->where('id=:id')->bind(':id', $id)->delete();
        return json(200,'删除成功');
    }

    //域名配置-短网址列表-删除全部失效链接
    public function delAllShortDomain(){
        $now = time();
        $res = Db::table('domain_text')->alias('a')
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
        Db::table('domain_text')->where("id in ($str)")->delete();
        return json(200,'删除成功');
    }

}