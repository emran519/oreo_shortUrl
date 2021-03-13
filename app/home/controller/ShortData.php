<?php


namespace app\home\controller;


use oreo\lib\Db;

class ShortData extends Base
{

    public function index(){
        return view('home/shortData',[
            'user_email' => $this->user_info['email']?:"0",
            'user_phone' => $this->user_info['phone']?:"0",
            'user_qq' => $this->user_info['qq_num']?:"0"
        ]);
    }

    public function langData(){
        $id = request()->param('id');
        $res = Db::table('short_url_log')->where(['short_id'=>$id,'user_id'=>$this->user_id])->all();
        $data = [];//数据组
        //操作系统
        $data['windows'] = 0;//windows
        $data['linux'] = 0;//linux
        $data['unix'] = 0;//unix
        $data['mac'] = 0;//mac
        $data['android'] = 0;//android
        $data['iphone'] = 0;//iphone
        //浏览器
        $data['ie'] = 0;//ie
        $data['edg'] = 0;//edg
        $data['chrome'] = 0;//chrome
        $data['firefox'] = 0;//firefox
        $data['safari'] = 0;//safari
        $data['opera'] = 0;//opera
        $data['other'] = 0;//other
        //语言
        $data['simChn'] = 0;//简体中文
        $data['traChn'] = 0;//繁体中文
        $data['eng'] = 0;//英文
        foreach ($res as $k=>$v){
            $data = $this->lang($v,$data);
            $data = $this->browserData($v,$data);
            $data = $this->systemData($v,$data);
        }
        return json(200,'查询成功',$data);
    }

    private function systemData($value,$data){
        switch ($value['system']){
            case 1:
                $data['windows'] ++;
                break;
            case 2:
                $data['linux'] ++;
                break;
            case 3:
                $data['unix'] ++;
                break;
            case 4:
                $data['mac'] ++;
                break;
            case 5:
                $data['android'] ++;
                break;
            case 6:
                $data['iphone'] ++;
                break;
        }
        return $data;
    }

    private function browserData($value,$data){
        switch ($value['browser']){
            case 1:
                $data['ie'] ++;
                break;
            case 2:
                $data['edg'] ++;
                break;
            case 3:
                $data['chrome'] ++;
                break;
            case 4:
                $data['firefox'] ++;
                break;
            case 5:
                $data['safari'] ++;
                break;
            case 6:
                $data['opera'] ++;
                break;
            default:
                $data['other'] ++;
                break;
        }
        return $data;
    }

    private function lang($value,$data){
        switch ($value['lang']){
            case 1:
                $data['simChn'] ++;
                break;
            case 2:
                $data['traChn'] ++;
                break;
            default:
                $data['eng'] ++;
                break;
        }
        return $data;
    }

    private function get7day($time = '', $format='Y-m-d'){
        $time = $time != '' ? $time : time();
        //组合数据
        $data = [];
        for ($i=0; $i<=6; $i++){
            $ii = $i-(6);
            $data[$i]['click_date'] = date($format ,strtotime( ('+'. $ii . ' days'), $time));
            $data[$i]['count'] = 0;
        }
        return $data;
    }

    public function click7Data(){
        $id = request()->post('id');
        $arr1 = Db::query("select a.click_date,ifnull(b.count,0) as count
from (
    SELECT curdate() as click_date
    union all
    SELECT date_sub(curdate(), interval 1 day) as click_date
    union all
    SELECT date_sub(curdate(), interval 2 day) as click_date
    union all
    SELECT date_sub(curdate(), interval 3 day) as click_date
    union all
    SELECT date_sub(curdate(), interval 4 day) as click_date
    union all
    SELECT date_sub(curdate(), interval 5 day) as click_date
    union all
    SELECT date_sub(curdate(), interval 6 day) as click_date
) a left join (
  select date(add_time) as datetime, count(*) as count
  from oreo_short_url_log
  WHERE (short_id = {$id} and user_id ={$this->user_id})
  group by date(add_time)
) b on a.click_date = b.datetime");
        //$arr1 = Db::query("SELECT DATE(add_time) createtime,COUNT(*) AS click FROM oreo_short_url_log WHERE (short_id = {$id} and user_id = {$this->user_id}) and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(add_time) GROUP BY createtime");
        $arr2 = $this->get7day(); //获取近七天
        $arr = [];
        $click_num = array_column($arr1, 'count', 'click_date');
        foreach($arr2 as $key => $item) {
            $arr[$key]['click_date'] = $item['click_date'];
            $arr[$key]['count'] =  $click_num[$item['click_date']] ? : 0;
        }
        return json(200,'ok',$arr);
    }

}