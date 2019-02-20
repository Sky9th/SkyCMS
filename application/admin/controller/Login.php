<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\controller;

use app\admin\logic\Session;
use think\Controller;

class Login extends Controller {

    public function index(){
        return view();
    }

    public function login(){
        //获取用户表单提交信息
        $username = input('post.username');
        $password = input('post.password');
        $code = input('post.code');
        $auto = input('post.auto');

        $user = new Session();
        $res = $user->login($username, $password, $code, $auto);
        return $res;

    }
    public function logout(){
        if( Session::logout() ){
            $this->success(lang('logout success'));
        }else{
            $this->success(lang('logout error'));
        }
    }

}
