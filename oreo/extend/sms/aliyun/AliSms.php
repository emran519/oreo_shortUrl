<?php

namespace oreo\extend\sms\aliyun;
/**
 * 阿里云短信验证码发送类
 *
 */
class AliSms {
    // 保存错误信息
    public $error;
    // Access Key ID
    private $accessKeyId = '';
    // Access Access Key Secret
    private $accessKeySecret = '';
    // 签名
    private $signName = '';
    // 模版ID
    private $templateCode = '';
    public function __construct($cofig = array()) {
        // 配置参数
        $this->accessKeyId = $cofig ['accessKeyId'];
        $this->accessKeySecret = $cofig ['accessKeySecret'];
        $this->signName = $cofig ['signName'];
        $this->templateCode = $cofig ['templateCode'];
    }
    private function percentEncode($string) {
        $string = urlencode ( $string );
        $string = preg_replace ( '/\+/', '%20', $string );
        $string = preg_replace ( '/\*/', '%2A', $string );
        $string = preg_replace ( '/%7E/', '~', $string );
        return $string;
    }
    /**
     * 签名
     *
     * @param unknown $parameters
     * @param unknown $accessKeySecret
     * @return string
     */
    private function computeSignature($parameters, $accessKeySecret) {
        ksort ( $parameters );
        $canonicalizedQueryString = '';
        foreach ( $parameters as $key => $value ) {
            $canonicalizedQueryString .= '&' . $this->percentEncode ( $key ) . '=' . $this->percentEncode ( $value );
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode ( substr ( $canonicalizedQueryString, 1 ) );
        $signature = base64_encode ( hash_hmac ( 'sha1', $stringToSign, $accessKeySecret . '&', true ) );
        return $signature;
    }
    /**
     * @param string $phone
     * @param string $verify_code
     *
     */
    public function send_verify($phone, $verify_code) {
        $params = array (   //此处作了修改
            'SignName' => $this->signName,
            'Format' => 'JSON',
            'Version' => '2017-05-25',
            'AccessKeyId' => $this->accessKeyId,
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid (),
            'Timestamp' => gmdate ( 'Y-m-d\TH:i:s\Z' ),
            'Action' => 'SendSms',
            'TemplateCode' => $this->templateCode,
            'PhoneNumbers' => $phone,
            'TemplateParam' => '{"code":"' . $verify_code . '"}'//更换为自己的实际模版
        );
        //var_dump($params);die;
        // 计算签名并把签名结果加入请求参数
        $params ['Signature'] = $this->computeSignature ( $params, $this->accessKeySecret );
        // 发送请求（此处作了修改）
        //$url = 'https://sms.aliyuncs.com/?' . http_build_query ( $params );
        $url = 'http://dysmsapi.aliyuncs.com/?' . http_build_query ( $params );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        $result = json_decode ( $result, true );
        //var_dump($result);die;
        if (  $result ['Code'] != 'OK') {
            $this->error = $this->getErrorMessage ( $result ['Code'] );
            return false;
        }
        return true;
    }
    /**
     * 获取详细错误信息
     *
     * @param unknown $status
     */
    public function getErrorMessage($status) {
        // 阿里云的短信 乱八七糟的(其实是用的阿里大于)
        // https://api.alidayu.com/doc2/apiDetail?spm=a3142.7629140.1.19.SmdYoA&apiId=25450
        $message = array (
            'isv.BUSINESS_LIMIT_CONTROL' => '短信发送频率超限',
            'isv.DAY_LIMIT_CONTROL' => '已经达到您在控制台设置的短信日发送量限额值',
            'isv.SMS_CONTENT_ILLEGAL' => '短信内容包含禁止发送内容,请修改短信文案',
            'isv.SMS_SIGN_ILLEGAL' => '签名禁止使用,请在短信服务控制台中申请符合规定的签名',
            'isp.RAM_PERMISSION_DENY' => 'RAM权限不足。,请为当前使用的AK对应子账号进行授权：AliyunDysmsFullAccess（管理权限）',
            'isv.OUT_OF_SERVICE' => '余额不足。余额不足时，套餐包中即使有短信额度也无法发送短信',
            'isv.PRODUCT_UN_SUBSCRIPT' => '未开通云通信产品的阿里云客户',
            'isv.PRODUCT_UNSUBSCRIBE' => '产品未开通',
            'isv.ACCOUNT_NOT_EXISTS' => '账户不存在，使用了错误的账户名称或AK',
            'isv.ACCOUNT_ABNORMAL' => '账户异常',
            'isv.SMS_TEMPLATE_ILLEGAL' => '短信模板不存在，或未经审核通过',
            'isv.SMS_SIGNATURE_ILLEGAL' => '签名不存在，或未经审核通过',
            'isv.INVALID_PARAMETERS' => '参数格式不正确',
            'isv.MOBILE_NUMBER_ILLEGAL' => '机号码格式错误',
            'isv.MOBILE_COUNT_OVER_LIMIT' => '参数PhoneNumbers中指定的手机号码数量超出限制',
            'isv.TEMPLATE_MISSING_PARAMETERS' => '模版缺少变量',
            'isv.BLACK_KEY_CONTROL_LIMIT' => '黑名单管控',
            'isv.PARAM_LENGTH_LIMIT' => '参数超出长度限制',
            'isv.PARAM_NOT_SUPPORT_URL' => '不支持URL',
            'isv.AMOUNT_NOT_ENOUGH' => '账户余额不足',
            'SignatureDoesNotMatch' => '签名（Signature）加密错误',
        );
        if (isset ( $message [$status] )) {
            return $message [$status];
        }
        return $status;
    }
}