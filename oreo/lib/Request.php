<?php


namespace oreo\lib;

/**
 * Class Request
 * 此类来自于ThinkPHP5
 * 作者认为TP的request类目前很好用，所以写出更好的之前先引用TP的
 * 同样作者也此类的基础上做出了一些更改
 * @package oreo\lib
 */
class Request
{
    /**
     * php://input内容
     * @var string
     */
    protected $input;

    /**
     * 当前SERVER参数
     * @var array
     */
    protected $server = [];

    /**
     * 请求类型
     * @var string
     */
    protected $method;

    /**
     * 请求类型
     * @var string
     */
    protected $varMethod = '_method';

    /**
     * 当前REQUEST参数
     * @var array
     */
    protected $request = [];

    /**
     * 全局过滤规则
     * @var array
     */
    protected $filter;

    /**
     * 当前请求参数
     * @var array
     */
    protected $param = [];

    /**
     * 当前ROUTE参数
     * @var array
     */
    protected $route = [];

    /**
     * 当前HEADER参数
     * @var array
     */
    protected $header = [];

    /**
     * COOKIE数据
     * @var array
     */
    protected $cookie = [];

    /**
     * 当前GET参数
     * @var array
     */
    protected $get = [];

    /**
     * 当前POST参数
     * @var array
     */
    protected $post = [];

    /**
     * 当前PUT参数
     * @var array
     */
    protected $put;

    /**
     * 当前FILE参数
     * @var array
     */
    protected $file = [];

    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        // 表单请求类型伪装变量
        'var_method'       => '_method',
        // 表单ajax伪装变量
        'var_ajax'         => '_ajax',
        // 表单pjax伪装变量
        'var_pjax'         => '_pjax',
        // PATHINFO变量名 用于兼容模式
        'var_pathinfo'     => 's',
        // 兼容PATH_INFO获取
        'pathinfo_fetch'   => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
        // 默认全局过滤方法 用逗号分隔多个
        'default_filter'   => '',
        // 域名根，如thinkphp.cn
        'url_domain_root'  => '',
        // HTTPS代理标识
        'https_agent_name' => '',
        // IP代理获取标识
        'http_agent_ip'    => 'HTTP_X_REAL_IP',
        // URL伪静态后缀
        'url_html_suffix'  => 'html',
    ];

    /**
     * 主机名（含端口）
     * @var string
     */
    protected $host;

    /**
     * 获取
     * @access public
     */
    public function __construct()
    {
        // 保存 php://input
        $this->input = file_get_contents('php://input');
        //header
        if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
            $header = $result;
        } else {
            $header = [];
            $server = $_SERVER;
            foreach ($server as $key => $val) {
                if (0 === strpos($key, 'HTTP_')) {
                    $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                    $header[$key] = $val;
                }
            }
            if (isset($server['CONTENT_TYPE'])) {
                $header['content-type'] = $server['CONTENT_TYPE'];
            }
            if (isset($server['CONTENT_LENGTH'])) {
                $header['content-length'] = $server['CONTENT_LENGTH'];
            }
        }
        $this->header = array_change_key_case($header);
        $this->server = $_SERVER;
        $this->get     = $_GET;
        $this->post    = $_POST ?: $this->getInputData($this->input);
        $this->request = $_REQUEST;
        $this->cookie  = $_COOKIE;
    }

    protected function getInputData($content): array
    {
        $contentType = $this->contentType();
        if ('application/x-www-form-urlencoded' == $contentType) {
            parse_str($content, $data);
            return $data;
        } elseif (false !== strpos($contentType, 'json')) {
            return (array) json_decode($content, true);
        }
        return [];
    }

    /**
     * 当前请求 HTTP_CONTENT_TYPE
     * @access public
     * @return string
     */
    public function contentType(): string
    {
        $contentType = $this->header('Content-Type');
        if ($contentType) {
            if (strpos($contentType, ';')) {
                [$type] = explode(';', $contentType);
            } else {
                $type = $contentType;
            }
            return trim($type);
        }
        return '';
    }

    /**
     * 设置或者获取当前的Header
     * @access public
     * @param  string $name header名称
     * @param  string $default 默认值
     * @return string|array
     */
    public function header(string $name = '', string $default = null)
    {
        if ('' === $name) {
            return $this->header;
        }
        $name = str_replace('_', '-', strtolower($name));
        return $this->header[$name] ?? $default;
    }

    /**
     * 获取server参数
     * @access public
     * @param  string $name 数据名称
     * @param  string $default 默认值
     * @return mixed
     */
    public function server(string $name = '', string $default = '')
    {
        if (empty($name)) {
            return $this->server;
        } else {
            $name = strtoupper($name);
        }
        return $this->server[$name] ?? $default;
    }

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile()
    {
        if ($this->server('HTTP_VIA') && stristr($this->server('HTTP_VIA'), "wap")) {
            return true;
        } elseif ($this->server('HTTP_ACCEPT') && strpos(strtoupper($this->server('HTTP_ACCEPT')), "VND.WAP.WML")) {
            return true;
        } elseif ($this->server('HTTP_X_WAP_PROFILE') || $this->server('HTTP_PROFILE')) {
            return true;
        } elseif ($this->server('HTTP_USER_AGENT') && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $this->server('HTTP_USER_AGENT'))) {
            return true;
        }
        return false;
    }

    /**
     * 当前的请求类型
     * @access public
     * @param  bool $origin 是否获取原始请求类型
     * @return string
     */
    public function method(bool $origin = false): string
    {
        if ($origin) {
            // 获取原始请求类型
            return $this->server('REQUEST_METHOD') ?: 'GET';
        } elseif (!$this->method) {
            if (isset($this->post[$this->varMethod])) {
                $method = strtolower($this->post[$this->varMethod]);
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
                    $this->method    = strtoupper($method);
                    $this->{$method} = $this->post;
                } else {
                    $this->method = 'POST';
                }
                unset($this->post[$this->varMethod]);
            } else {
                $this->method = $this->server('REQUEST_METHOD') ?: 'GET';
            }
        }
        return $this->method;
    }

    /**
     * 获取变量 支持过滤和默认值
     * @access public
     * @param  array        $data 数据源
     * @param  string|false $name 字段名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤函数
     * @return mixed
     */
    public function input(array $data = [], $name = '', $default = null, $filter = '')
    {
        if (false === $name) {
            // 获取原始数据
            return $data;
        }
        $name = (string) $name;
        if ('' != $name) {
            // 解析name
            if (strpos($name, '/')) {
                [$name, $type] = explode('/', $name);
            }
            $data = $this->getData($data, $name);
            if (is_null($data)) {
                return $default;
            }
            if (is_object($data)) {
                return $data;
            }
        }
        $data = $this->filterData($data, $filter, $name, $default);
        if (isset($type) && $data !== $default) {
            // 强制类型转换
            $this->typeCast($data, $type);
        }
        return $data;
    }

    /**
     * 强制类型转换
     * @access public
     * @param  mixed  $data
     * @param  string $type
     * @return mixed
     */
    private function typeCast(&$data, string $type)
    {
        switch (strtolower($type)) {
            // 数组
            case 'a':
                $data = (array) $data;
                break;
            // 数字
            case 'd':
                $data = (int) $data;
                break;
            // 浮点
            case 'f':
                $data = (float) $data;
                break;
            // 布尔
            case 'b':
                $data = (boolean) $data;
                break;
            // 字符串
            case 's':
                if (is_scalar($data)) {
                    $data = (string) $data;
                } else {
                    throw new \InvalidArgumentException('variable type error：' . gettype($data));
                }
                break;
        }
    }

    protected function filterData($data, $filter, $name, $default)
    {
        // 解析过滤器
        $filter = $this->getFilter($filter, $default);
        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
        } else {
            $this->filterValue($data, $name, $filter);
        }
        return $data;
    }

    /**
     * 递归过滤给定的值
     * @access public
     * @param  mixed $value 键值
     * @param  mixed $key 键名
     * @param  array $filters 过滤方法+默认值
     * @return mixed
     */
    public function filterValue(&$value, $key, $filters)
    {
        $default = array_pop($filters);
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                // 调用函数或者方法过滤
                $value = call_user_func($filter, $value);
            } elseif (is_scalar($value)) {
                if (is_string($filter) && false !== strpos($filter, '/')) {
                    // 正则过滤
                    if (!preg_match($filter, $value)) {
                        // 匹配不成功返回默认值
                        $value = $default;
                        break;
                    }
                } elseif (!empty($filter)) {
                    // filter函数不存在时, 则使用filter_var进行过滤
                    // filter为非整形值时, 调用filter_id取得过滤id
                    $value = filter_var($value, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $value) {
                        $value = $default;
                        break;
                    }
                }
            }
        }

        return $value;
    }

    protected function getFilter($filter, $default): array
    {
        if (is_null($filter)) {
            $filter = [];
        } else {
            $filter = $filter ?: $this->filter;
            if (is_string($filter) && false === strpos($filter, '/')) {
                $filter = explode(',', $filter);
            } else {
                $filter = (array) $filter;
            }
        }
        $filter[] = $default;
        return $filter;
    }

    /**
     * 获取数据
     * @access public
     * @param  array  $data 数据源
     * @param  string $name 字段名
     * @param  mixed  $default 默认值
     * @return mixed
     */
    protected function getData(array $data, string $name, $default = null)
    {
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return $default;
            }
        }
        return $data;
    }

    /**
     * 获取指定的参数
     * @access public
     * @param  array        $name 变量名
     * @param  mixed        $data 数据或者变量类型
     * @param  string|array $filter 过滤方法
     * @return array
     */
    public function only(array $name, $data = 'param', $filter = ''): array
    {
        $data = is_array($data) ? $data : $this->$data();
        $item = [];
        foreach ($name as $key => $val) {
            if (is_int($key)) {
                $default = null;
                $key     = $val;
                if (!isset($data[$key])) {
                    continue;
                }
            } else {
                $default = $val;
            }
            $item[$key] = $this->filterData($data[$key] ?? $default, $filter, $key, $default);
        }
        return $item;
    }

    /**
     * 获取POST参数
     * @access public
     * @param  string|array $name 变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function post($name = '', $default = null, $filter = '')
    {
        if (is_array($name)) {
            return $this->only($name, $this->post, $filter);
        }
        return $this->input($this->post, $name, $default, $filter);
    }

    /**
     * 获取GET参数
     * @access public
     * @param  string|array $name 变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function get($name = '', $default = null, $filter = '')
    {
        if (is_array($name)) {
            return $this->only($name, $this->get, $filter);
        }
        return $this->input($this->get, $name, $default, $filter);
    }

    /**
     * 获取PUT参数
     * @access public
     * @param  string|array $name 变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function put($name = '', $default = null, $filter = '')
    {
        if (is_array($name)) {
            return $this->only($name, $this->put, $filter);
        }
        return $this->input($this->put, $name, $default, $filter);
    }

    /**
     * 获取路由参数
     * @access public
     * @param  string|array $name 变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function route($name = '', $default = null, $filter = '')
    {
        if (is_array($name)) {
            return $this->only($name, $this->route, $filter);
        }
        return $this->input($this->route, $name, $default, $filter);
    }

    /**
     * 获取当前请求的参数
     * @access public
     * @param  string|array $name 变量名
     * @param  mixed        $default 默认值
     * @param  string|array $filter 过滤方法
     * @return mixed
     */
    public function param($name = '', $default = null, $filter = '')
    {
        if (empty($this->mergeParam)) {
            $method = $this->method(true);
            // 自动获取请求变量
            switch ($method) {
                case 'POST':
                    $vars = $this->post(false);
                    break;
                case 'PUT':
                case 'DELETE':
                case 'PATCH':
                    $vars = $this->put(false);
                    break;
                default:
                    $vars = [];
            }
            // 当前请求参数和URL地址中的参数合并
            $this->param = array_merge($this->param, $this->get(false), $vars, $this->route(false));
            $this->mergeParam = true;
        }
        if (is_array($name)) {
            return $this->only($name, $this->param, $filter);
        }
        return $this->input($this->param, $name, $default, $filter);
    }

    /**
     * 获取客户端IP地址
     * @access public
     * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function ip($type = 0, $adv = true)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        $httpAgentIp = 'HTTP_X_REAL_IP';
        if ($httpAgentIp && $this->server($httpAgentIp)) {
            $ip = $this->server($httpAgentIp);
        } elseif ($adv) {
            if ($this->server('HTTP_X_FORWARDED_FOR')) {
                $arr = explode(',', $this->server('HTTP_X_FORWARDED_FOR'));
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif ($this->server('HTTP_CLIENT_IP')) {
                $ip = $this->server('HTTP_CLIENT_IP');
            } elseif ($this->server('REMOTE_ADDR')) {
                $ip = $this->server('REMOTE_ADDR');
            }
        } elseif ($this->server('REMOTE_ADDR')) {
            $ip = $this->server('REMOTE_ADDR');
        }
        // IP地址类型
        $ip_mode = (strpos($ip, ':') === false) ? 'ipv4' : 'ipv6';
        // IP地址合法验证
        if (filter_var($ip, FILTER_VALIDATE_IP) !== $ip) {
            $ip = ('ipv4' === $ip_mode) ? '0.0.0.0' : '::';
        }
        // 如果是ipv4地址，则直接使用ip2long返回int类型ip；如果是ipv6地址，暂时不支持，直接返回0
        $long_ip = ('ipv4' === $ip_mode) ? sprintf("%u", ip2long($ip)) : 0;
        $ip = [$ip, $long_ip];
        return $ip[$type];
    }

    /**
     * 获取上传的文件信息
     * @access public
     * @param  string   $name 名称
     * @return null|array|\oreo\lib\file\File
     */
    public function file($name = '')
    {
        if (empty($this->file)) {
            $this->file = isset($_FILES) ? $_FILES : [];
        }
        $files = $this->file;
        if (!empty($files)) {
            if (strpos($name, '.')) {
                list($name, $sub) = explode('.', $name);
            }
            // 处理上传文件
            $array = $this->dealUploadFile($files, $name);
            if ('' === $name) {
                // 获取全部文件
                return $array;
            } elseif (isset($sub) && isset($array[$name][$sub])) {
                return $array[$name][$sub];
            } elseif (isset($array[$name])) {
                return $array[$name];
            }
        }
        return;
    }

    protected function dealUploadFile($files, $name)
    {
        $array = [];
        foreach ($files as $key => $file) {
            if ($file instanceof \oreo\lib\file\File) {
                $array[$key] = $file;
            } elseif (is_array($file['name'])) {
                $item  = [];
                $keys  = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    if ($file['error'][$i] > 0) {
                        if ($name == $key) {
                            $this->throwUploadFileError($file['error'][$i]);
                        } else {
                            continue;
                        }
                    }
                    $temp['key'] = $key;
                    foreach ($keys as $_key) {
                        $temp[$_key] = $file[$_key][$i];
                    }
                    $item[] = (new \oreo\lib\file\File($temp['tmp_name']))->setUploadInfo($temp);
                }
                $array[$key] = $item;
            } else {
                if ($file['error'] > 0) {
                    if ($key == $name) {
                        $this->throwUploadFileError($file['error']);
                    } else {
                        continue;
                    }
                }
                $array[$key] = (new \oreo\lib\file\File($file['tmp_name']))->setUploadInfo($file);
            }
        }
        return $array;
    }

    protected function throwUploadFileError($error)
    {
        static $fileUploadErrors = [
            1 => '上传文件大小超过最大值',
            2 => '上传文件大小超过最大值',
            3 => '只上传文件的一部分',
            4 => '没有要上传的文件',
            6 => '找不到上传临时目录',
            7 => '文件写入错误',
        ];
        $msg = $fileUploadErrors[$error];
        throw new Exception($msg);
    }

    /**
     * 获取当前包含协议、端口的域名
     * @access public
     * @param  bool $port 是否需要去除端口号
     * @return string
     */
    public function domain($port = false)
    {
        return $this->scheme() . '://' . $this->host($port);
    }

    /**
     * 当前请求的host
     * @access public
     * @param bool $strict  true 仅仅获取HOST
     * @return string
     */
    public function host($strict = false)
    {
        if (!$this->host) {
            $this->host = $this->server('HTTP_X_REAL_HOST') ?: $this->server('HTTP_HOST');
        }
        return true === $strict && strpos($this->host, ':') ? strstr($this->host, ':', true) : $this->host;
    }

    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public function scheme()
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function isSsl()
    {
        if ($this->server('HTTPS') && ('1' == $this->server('HTTPS') || 'on' == strtolower($this->server('HTTPS')))) {
            return true;
        } elseif ('https' == $this->server('REQUEST_SCHEME')) {
            return true;
        } elseif ('443' == $this->server('SERVER_PORT')) {
            return true;
        } elseif ('https' == $this->server('HTTP_X_FORWARDED_PROTO')) {
            return true;
        } elseif ($this->config['https_agent_name'] && $this->server($this->config['https_agent_name'])) {
            return true;
        }

        return false;
    }
}