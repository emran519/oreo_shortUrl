<?php

namespace oreo\lib;

/**
 * Class View 模板引擎
 * @package oreo\lib
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */
class View {

    /**
     * @var array
     */
    private static $tplVar = array();

    /**
     * 创建缓存目录
     * @author oreo
     */
    private static function tpl(){
        //检查缓存目录
        if (!file_exists(RUNTIME . 'cache/tpl')) {
            mkdir(RUNTIME . 'cache/tpl', 0777, true);
        }
    }

    /**
     * 设置值
     * @param array $value
     */
    public static function assign(array $value) {
        self::$tplVar = $value;
    }

    /**
     * 输出模板内容
     * @param string $filename
     * @return null
     */
    public static function display(string $filename = '') {
        self::tpl();
        $cache = self::compile($filename);
        if ($cache) {
            responseType("html");
            return $cache;
        }
        return false;
    }

    /**
     * 返回编译内容
     * @param $filename
     * @return string
     */
    public static function compile(string $filename = '') {
        $path = BASE_PATH . 'view/' . $filename . Config::get('app.url_html_suffix');
        if (!is_file($path)) {
            echo '</br>template load error';
            return false;
        }
        $cache = RUNTIME . 'cache/tpl/' . md5($path) . '.php';
        return self::fetch($path, $cache);
    }

    /**
     * 生成编译文件并返回
     * @param string $path 目录
     * @param string $cache 缓存文件
     * @return
     *
     */
    private static function fetch(string $path, string $cache) {
        $fileData = file_get_contents($path);
        if (!file_exists($cache) || filemtime($path) > filemtime($cache)) {
            $pattern = array(
                '/\{(\$[\w\[\]\']+)\}/',
                '/{break}/',
                '/{continue}/',
                '/{if (.*?)}/',
                '/{\/if}/',
                '/{elseif (.*?)}/',
                '/{else}/',
                '/{foreach (.*?)}/',
                '/{\/foreach}/',
                "/{include '(.*?)'}/",
                '/{\:(.*?)}/',
                '/{fun:(.*?)}/'
            );
            $replace = array(
                '<?php echo ${1};?>',
                '<?php break;?>',
                '<?php continue;?>',
                '<?php if(${1}):?>',
                '<?php endif;?>',
                '<?php elseif(${1}):?>',
                '<?php else:?>',
                '<?php foreach(${1}):?>',
                '<?php endforeach;?>',
                '<?php view()->display("${1}");?>',
                '<?php echo ${1};?>',
                '<?php echo ${1};?>'
            );
            $cacheData = preg_replace($pattern, $replace, $fileData);
            @file_put_contents($cache, $cacheData);
        } else {
            $cacheData = file_get_contents($cache);
        }
        $pattern = array(
            '/__HOME__/'
        );
        $replace = array(
            __HOME__
        );
        $cacheData = preg_replace($pattern, $replace, $cacheData);
        preg_match_all('/\{\$([a-zA-Z0-9]+)\}/', $fileData, $tmp);
        for ($i = 0; $i < sizeof($tmp [1]); $i++) {
            if (!isset (self::$tplVar [$tmp [1] [$i]])) {
                self::$tplVar [$tmp [1] [$i]] = '';
            }
        }
        ob_start();
        extract(self::$tplVar);
        eval ('?>' . $cacheData);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}