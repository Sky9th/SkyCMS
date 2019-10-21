<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/21
 * Time: 11:25
 */
namespace app\common\component;

use EasyWeChat\Factory;

/**
 * 小程序对接
 * Class Mpr
 * @property Factory::F $app
 * @package app\common\component
 */
class Mpr {

    public $app ;

    public function __construct(){
        config('app_trace', false);
        $this->app = Factory::miniProgram(config('mpr.'));
    }

    public function register($code){
        $info = $this->app->auth->session($code);
        dump($info);
    }
}