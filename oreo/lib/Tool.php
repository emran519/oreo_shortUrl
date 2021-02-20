<?php


namespace oreo\lib;

/**
 * Class Tool 工具类
 * @package oreo\lib
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */
class Tool
{

    /**
     * 生成验证码
     * @param bool $interfere 干扰元素，设置雪花点
     * @param bool $interfereTwo 干扰元素，设置横线
     * @param int $width 图片长度
     * @param int $height 图片高度
     */
    public static function captcha($interfere = true, $interfereTwo = true, $width = 100, $height = 30){
        $image = imagecreatetruecolor($width, $height);
        $bgcolor = imagecolorallocate($image,255,255,255); //#ffffff
        imagefill($image, 0, 0, $bgcolor);
        $captcha_code = "";
        for($i=0;$i<4;$i++){
            $fontsize = 6;
            $fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120));      //0-120深颜色
            $fontcontent = rand(0,9);
            $captcha_code .= $fontcontent;
            $x = ($i*100/4)+rand(5,10);
            $y = rand(5,10);
            imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
        }
        $_SESSION['captcha'] = $captcha_code;
        if($interfere){
            for($i=0;$i<200;$i++){
                $pointcolor = imagecolorallocate($image,rand(50,200), rand(50,200), rand(50,200));
                imagesetpixel($image, rand(1,99), rand(1,29), $pointcolor);
            }
        }
        if($interfereTwo){
            for($i=0;$i<4;$i++){
                $linecolor = imagecolorallocate($image,rand(80,220), rand(80,220),rand(80,220));
                imageline($image,rand(1,99), rand(1,29),rand(1,99), rand(1,29),$linecolor);
            }
        }
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

}