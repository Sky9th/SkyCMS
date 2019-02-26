<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/6/12
 * Time: 10:33
 */
namespace app\wechat\controller;

class Index {

    public function Index(){
        app('wechat')->server();
    }

}