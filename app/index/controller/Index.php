<?php


namespace app\index\controller;


use oreo\lib\Controller;
use oreo\lib\Db;
use oreo\lib\safe\Csrf;
use oreo\lib\safe\verifyCsrf;

class Index extends Controller
{

    public function __construct(){
        parent::__construct();
        $web_mode = $this->systemInfo('system_state')['value'];
        $web_mode_msg = $this->systemInfo('system_state_text')['value'];
        if($web_mode==2 && \oreo\lib\Route::$action != 'longUrl'){
            exit( view('error/error', [
                'web_name' => $this->systemInfo('web_name')['value'],
                'text' => $web_mode_msg
            ]));
        }if($web_mode==3){
            exit( view('error/error', [
                'web_name' => $this->systemInfo('web_name')['value'],
                'text' => $web_mode_msg
            ]));
        }
    }

    /**
     * 首页
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/21 16:39
     *
     * @param string|null $msg 内容
     * @return false|string|null
     */
    public function index(string $msg = null){
        return view('index', [
            'msg' => $msg?:'基于OreoFrame的短网址生成系统',
            'web_name' => $this->systemInfo('web_name')['value'],
            'icp_num' => $this->systemInfo('icp_num')['value']
        ]);
    }


    /**
     * 域名列表
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/21 16:39
     *
     * @return false|string
     */
    public function domainList(){
        $domain = Db::table('domain')->field('id,domain')->where('state',1)->all();
        if(empty($domain))return json(0,'暂无短域名','暂无短域名');
        return json(200,'获取成功',['token'=>Csrf::token('token'),'domain'=>$domain]);
    }

    /**
     * 生成短网址
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/23 6:36
     *
     * @return false|string
     */
    public function shortDomain(){
        $_token = request()->post('token');
        $vc = VerifyCsrf::_checkToken('token',$_token);
        if ($vc === false) {
            return json(0,'安全认证失败,请刷新页面后再试',['token'=>Csrf::token('token')]);
        }
        $domainId = request()->post('domain_id');//选择的域名
        $target = request()->post('longDomain');//目标地址
        if(!$target)return json(0,'目标地址不能为空',['token'=>Csrf::token('token')]);
        //判断
        $preg = "/^http(s)?:\\/\\/.+/";
        if(!preg_match($preg,$target)){
            $target = "http://" . $target;
        }
        //验证
        if(empty($domainId))return json(0,'请选择短网址域名',['token'=>Csrf::token('token')]);
        if(empty($target))return json(0,'请填写目标地址',['token'=>Csrf::token('token')]);
        //域名截取(禁止套娃)
        $short = substr($target,strripos($target,"/")+1);
        $shortUrl = Db::table('domain_text')->where("address=:address")->bind(':address',$short)->find();
        if(!empty($shortUrl))return json(0,'本站短网址不能再次生成操作',['token'=>Csrf::token('token')]);
        return $this->saveShortUrl($domainId, $target);
    }

    /**
     * 保存短网址
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/23 6:36
     *
     * @param int $domainId
     * @param string $target
     * @return string
     */
    private function saveShortUrl(int $domainId, string $target) : string{
        //查询域名是否激活
        $res = Db::table('domain')
            ->alias('a')
            ->join('oreo_domain_filter b','a.filter_id = b.id')
            ->where("a.id=:id")->bind(':id',$domainId)
            ->field('a.id,a.cycle,a.domain,b.filter_content')
            ->find();
        if(empty($res)) return json(0,'当前短网址不存在，请切换其他',['token'=>Csrf::token('token')]);
        //验证过滤组
        if($res['filter_content'] != 'null'){
            $filter = explode("|", $res['filter_content']);
            foreach ($filter as $k => $v) {
                if ($this->strexists($target, $v)) {
                    return json(0,'当前目标网址被禁用',['token'=>Csrf::token('token')]);
                }
            }
        }
        //进行编码
        $base_Domain =  base64_encode($target);
        //查询
        $domain = Db::table('domain_text')
            ->where("domain_id=:domain_id")
            ->where('target=:target')
            ->bind([':domain_id'=>$res['id'],':target'=>$base_Domain])
            ->find();
        if (!empty($domain)){
            //更新到期时间以及创建者IP
            $param = [
                'user_ip' => request()->ip(),//创建者IP
                'create_time'=>  time(),//添加时间
                'end_time'=>  (time() + $res['cycle'] * 86400)//到期时间
            ];
            Db::table('domain_text')->where(['domain_id'=>$res['id'],'target'=>$base_Domain])->update($param);
            return json(200,'生成成功',['token'=>Csrf::token('token'),'domain'=>$res['domain'].'/'.$domain['address']]);
        }
        //生成特征码
        $url = crc32($res['id'].$base_Domain);//冗余校验码
        $address =  $this->code62($url);//地址
        //存入数据库
        $param = [
            'domain_id'=> $res['id'],//域名
            'address' => $address,//分配地址
            'target' => $base_Domain,//目标地址
            'cycle' => $res['cycle'],//生命周期
            'user_ip' => request()->ip(),//创建者IP
            'create_time'=>  time(),//添加时间
            'end_time'=>  (time() + $res['cycle'] * 86400)//到期时间
        ];
        Db::table('domain_text')->insert($param);
        return json(200,'生成成功',['token'=>Csrf::token('token'),'domain'=>$res['domain'].'/'.$address]);
    }

    /**
     * 防洪设置
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/23 6:36
     *
     * @param string $short_url
     * @return array
     */
    private function mobile_open(string $short_url) : array{
        //查询
        $url = Db::table('domain_text')
            ->alias('a')
            ->join('oreo_domain b','a.domain_id = b.id')
            ->where("a.address=:address")
            ->bind(':address',$short_url)
            ->field('a.domain_id,a.target,a.cycle,a.create_time,a.end_time,b.domain,b.safe,b.safe_tpl')
            ->find();
        $conf = [];
        $conf['sql'] = $url;
        if(!empty($conf['sql']) && $conf['sql']['safe']==1){
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (strpos($user_agent, 'MicroMessenger') !== false) {
                preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
                $conf['res'] = false;
            } else if (strpos($user_agent, 'QQ/') !==false) {
                $conf['res'] = false;
            }else{
                $conf['res'] = true;
            }
        }else{
            $conf['res'] = true;
        }
        return $conf;
    }


    public function longUrl(){
        $short_url = \oreo\lib\Route::$app;//分配地址
        $url = $this->mobile_open($short_url);
        if($url['res'] == false){
            return view("domainSafe/{$url['sql']['safe_tpl']}", [
                'web_name' => $this->systemInfo('web_name')['value']
            ]);
        }
        if(empty($url['sql'])) {
            return $this->index('当前短网址不存在');
        }
        if($url['sql']['domain'] != request()->host()){
            return $this->index('当前短网址错误');
        }
        if($url['sql']['cycle']!=0){
            if($url['sql']['end_time']<=time()){
                return $this->index('当前短网址失效');
            }
        }
        $longUrl = base64_decode($url['sql']['target']);
        Db::query("update oreo_domain_text set `record`=record + 1 where address= '{$short_url}' ");
        return header("location:$longUrl");
    }

    private function code62(string $x) : string{
        $show='';
        while($x-->0){
            $s=$x % 62;
            if ($s>35){
                $s=chr($s+61);
            }elseif($s>9&&$s<=35){
                $s=chr($s+55);
            }
            $show.=$s;
            $x=floor($x/62);
        }
        return $show;
    }

    private function strexists($string, $find) {
        return !(strpos($string, $find) === FALSE);
    }
}