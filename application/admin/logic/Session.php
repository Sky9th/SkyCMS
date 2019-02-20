<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/16
 * Time: 10:03
 */
namespace app\admin\logic;

use app\common\model\common\User;
use app\common\model\sys\Role;

class Session {

    /**
     *  登陆流程
     *  验证码
     *  查询用户名密码是否正确
     *     ——》 1.正确
     *            1.1.注册登陆状态
     *            1.2.记录日志
     *     ——》 2.错误
     *            2.1 登陆频率
     *            2.2 记录日志
     * */
    public function login($username, $password, $code, $auto){
        if( !captcha_check($code , 'login') ){
            return error(lang('verify error'));
        }

        $user = new User();
        $exist = $user->where('type',0)->where('username', $username)->where('password', md5($password))->find();
        if( count($exist) == 1 ){ //用户密码正确
            //注册登录状态
            if( $this->do_login($exist, $auto) ){
                app_log(1, 0,'login');
                return success(lang('login success'),'', url('admin/index/index'));
            }
        }
        app_log(1, 0 , 'login_fail', ['username'=>$username, 'password'=>$password ] );
        return error(lang('login fail'));
    }

    /**
     * 注册登陆状态
     * @param $data
     * @param $auto
     * @return bool
     */
    public function do_login($data, $auto){
        session(md5('user_id'),$data['id']);
        if( $auto ){
            cookie(md5('username'),$data['username'], 3600*24*3 );
            cookie(md5('password'), sha1( $data['username'].$data['password'].$_SERVER['HTTP_USER_AGENT']), 3600*24*3 );
        }
        $user = new User();
        $user->save( ['last_login_session' => session_id() , 'last_login_time' => time() ] , [ 'id' => $data['id'] ] );
        if( session(md5('user_id')) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 退出登陆
     * @return bool
     */
    static public function logout(){
        session(md5('user_id'), null);
        cookie(md5('username'), null);
        cookie(md5('password'), null);
        if( !session(md5('user_id')) && !cookie(md5('username')) && !cookie(md5('password')) ){
            return true;
        }
        return false;
    }

    /**
     * 获取管理员信息
     * @param $admin_id
     * @return array
     */
    public function info($admin_id = ''){
        $admin_id = $admin_id ? : is_login() ;
        $user = new User();
        $admin = $user->where('id', $admin_id)->find();
        $rules = [];
        foreach ($admin['auth'] as $key => $value){
            if(  $value['rules'] == '0' || !empty($value['rules']) ){
                $_r = $value['rules'];
            }else{
                $_r = $this->getParentRules($value['pid']);
            }
            $admin['auth'][$key]['rules'] = $_r;
            $rules = array_merge($rules,explode(',', $_r));
        }
        $admin['rules'] = $rules;
        return $admin;
    }

    /**
     * 递归查询上级权限
     * @param $pid
     * @return mixed
     */
    protected function getParentRules($pid){
        $role = new Role();
        $roles = $role->find($pid);
        if( $roles && empty($roles['rules']) ){
            $roles = $this->getParentRules($pid);
        }
        return $roles['rules'];
    }


}