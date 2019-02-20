<?php
namespace app\common\controller;

class Verify
{

    /**
     * 生成验证码
     * @param string $name
     * @param int $w
     * @param int $h
     * @param int $fs
     * @param int $lt
     * @return \think\Response
     */
    public function index($name = '', $w = 240, $h = 60, $fs = 30, $lt = 5){
        $w = $w <= 330 ? $w : 330 ;
        $h = $h <= 60 ? $h : 60 ;
        $fs = $fs <= 30 ? $fs : 30 ;
        $lt = $lt <= 6 ? $lt : 6 ;

        $config = array(
            'imageW' => $w,
            'imageH' => $h,
            'length' => $lt,
            'fontSize' => $fs,
        );
        return captcha($name , $config);
    }

    /**
     * 验证码校验
     * @param string $code
     * @param string $id
     * @return bool
     */
    public function check($code='', $id = ''){
        return captcha_check($code,$id);
    }

}
