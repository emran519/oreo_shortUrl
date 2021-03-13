<?php


namespace app\home\controller;


use oreo\lib\Db;

class Index extends Base
{

    public function Index(){
        return view('home/index',[
            'user_email' => $this->user_info['email']?:"0",
            'user_phone' => $this->user_info['phone']?:"0",
            'user_qq' => $this->user_info['qq_num']?:"0",
        ]);
    }

    public function myBasic(){
        $res = Db::table('short_url')->field('user_id,cycle,record,create_time,end_time')->where('user_id',$this->user_id)->all();
        $count = count($res); //全部
        //过期的
        $allClick = 0;
        $timeOut = 0;
        foreach ($res as $k=>$v){
            $allClick = $allClick + $v['record'];
            if($v['cycle']!=0){
                if($v['end_time']<=time()){
                    $timeOut ++ ;
                }
            }
        }
        return json(200,'查询成功',['count'=>$count,'allClick'=>$allClick,'timeOut'=>$timeOut]);
    }

    public function allShortUrl(){
        $start = request()->get('start/d', 0);
        $page = request()->get('length/d',1);
        $search = request()->get('search');
        //如果有搜索
        if($search['value']){
            $list = Db::table('short_url')->where(['user_id'=>$this->user_id,'address'=>$search['value']])->limit($start,$page)->all();
            $count_list = count($list);  //总数
        }else{
            $list = Db::table('short_url')->alias('a')
                ->join('oreo_domain b','a.domain_id = b.id')
                ->field('a.id,a.address,a.cycle,a.record,a.create_time,a.end_time,b.domain')
                ->where('a.user_id',$this->user_id)
                ->limit($start,$page)
                ->all();
            $count_list = Db::table('short_url')->where('user_id',$this->user_id)->count();  //总数
        }
        responseType("json");
        return ['code'=>'200','msg'=>'获取成功','recordsTotal'=>$count_list,'recordsFiltered'=>$count_list,'data'=>$list];
    }

    public function delShortUrl(){
        $id = request()->post('id');
        $user_id = $this->user_id;
        if(!$id) return json(0,'Id不能为空');
        Db::begin();
        try {
            Db::table('short_url')->where('id=:id')->where('user_id=:user_id')->bind([':id'=>$id,':user_id'=>$user_id])->delete();
            Db::table('short_url_log')->where('short_id=:short_id')->where('user_id=:user_id')->bind([':short_id'=>$id,':user_id'=>$user_id])->delete();
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return json(0,'删除失败');
        }
        return json(200,'删除成功');
    }

    public function shortUrlTime(){
        $id = request()->post('id/d');
        $user_id = $this->user_id;
        if(!$id) return json(0,'Id不能为空');
        $res = Db::table('short_url')->where('id=:id')->where('user_id=:user_id')->bind([':id'=>$id,':user_id'=>$user_id])->find();
        if(empty($res))return json(0,'此短网址不存在');
        if($res['cycle']==0) return json(0,'此短网址为长期网址，不支持续期');
        if($res['end_time']>=time())return json(0,'此短网址暂未到期');
        $param = [
            'cycle' => 7,//生命周期
            'end_time'=> strtotime("+7 day")//添加时间
        ];
        Db::begin();
        try {
            Db::table('short_url')->where(['id' => $id,'user_id'=>$user_id])->update($param);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return json(0,'续期失败',$e->getMessage());
        }
        return json(200,'续期成功');
    }

}