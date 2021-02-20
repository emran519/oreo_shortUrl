<?php


namespace oreo\extend;

use oreo\extend\sms\qcloud\SmsSingleSender;
use oreo\extend\sms\aliyun\AliSms;
use oreo\extend\sms\sendmail\SendClass;


class Sms
{
    /**
     * 短信相关信息
     * @var
     */
    protected static $result;

    /**
     * 邮件相关信息
     * @var array
     */
    protected static $mail = array();

    /**
     * 腾讯云短信
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/15 1:44
     *
     * @param string $appId  腾讯云短信AppId
     * @param string $appKey 腾讯云短信AppKey
     * @return $this
     */
    public function qCloudSms(string $appId, string $appKey) : self
    {
        $qCloud =  new SmsSingleSender($appId, $appKey);
        self::$result = $qCloud;
        return $this;
    }

    /**
     * 指定模板单发
     * @author 饼干<609451870@qq.com>
     * date : 2021/2/15 1:44
     *
     * @param  int    $nationCode  国家码，如 86 为中国
     * @param  string $phoneNumber 不带国家码的手机号
     * @param  int    $tempId      模板 id
     * @param  string $param       模板参数列表，如模板 {1}...{2}...{3}，那么需要带三个参数
     * @param  string $sign        签名，如果填空串，系统会使用默认签名
     * @return array|string        应答json字符串，详细内容参见腾讯云协议文档
     */
    public function qCloudSmsParam(int $nationCode, string $phoneNumber, int $tempId, string $param, string $sign = "") : array
    {
        $res = self::$result->sendWithParam($nationCode, $phoneNumber, (array)["$param"], $tempId, $sign, "", "");
        self::$result = null;
        return json_decode($res,true);
    }

    /**
     * 阿里云短信发送
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/15 1:47
     *
     * @param string $accessKeyId     AccessKeyId
     * @param string $accessKeySecret AccessKeySecret
     * @param string $signName        签名内容
     * @param string $templateCode    短信模板CODE
     * @return $this
     */
    public function aliSms(string $accessKeyId, string $accessKeySecret, string $signName, string $templateCode) : self
    {
        $config = array (
            'accessKeyId'     => $accessKeyId,
            'accessKeySecret' => $accessKeySecret,
            'signName'        => $signName,
            'templateCode'    => $templateCode
        );
        $ali =  new AliSms($config);
        self::$result = $ali;
        return $this;
    }

    /**
     * 阿里云短信
     * author : 饼干<609451870@qq.com>
     * date : 2021/2/15 1:48
     *
     * @param string $phone 短信接受手机号码
     * @param string $param 数字验证码
     * @return bool
     */
    public function aliSmsParam(string $phone, string $param) : bool
    {
        $res = self::$result->send_verify($phone, $param);
        self::$result = null;
        return $res;
    }

    /**
     * SMTP邮件发送
     * @author 饼干<609451870@qq.com>
     * date : 2021/2/15 1:44
     *
     * @param string $smtp_url  SMTP地址
     * @param int    $smtp_port SMTP端口 465或25
     * @param string $mail_name 发件邮箱账号
     * @param string $mail_pass 发件邮箱密码
     * @param string $alias     别名(可选)
     * @return $this
     */
    public function email(string $smtp_url, int $smtp_port, string $mail_name, string $mail_pass, string $alias) : self
    {
        $mail = new SendClass($smtp_url, $smtp_port, $mail_name, $mail_pass, 1, $smtp_port==465?1:0);
        self::$mail['email_name'] = $mail_name;
        self::$mail['alias'] = $alias;
        self::$result = $mail;
        return $this;
    }

    /**
     * SMTP邮件发送其他配置
     * @author 饼干<609451870@qq.com>
     * date : 2021/2/15 1:44
     *
     * @param string $email 收件邮箱
     * @param string $title 邮件标题
     * @param string $text  邮件内容
     * @return string
     */
    public function emailParam(string $email, string  $title, string $text) : string
    {
        $res = self::$result->send($email, self::$mail['email_name'], $title, $text, self::$mail['alias']);
        self::$result = null;
        self::$mail   = null;
        return json_decode($res,true);
    }

}