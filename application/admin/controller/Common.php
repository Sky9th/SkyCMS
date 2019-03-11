<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/19
 * Time: 17:54
 */
namespace app\admin\controller;

use app\admin\logic\Session;
use Auth\Auth;
use think\Controller;

class Common extends Controller {

    protected $admin_info = [];

    public function __construct(){
        parent::__construct();
        if( is_login() <= 0 ){
            $url = url('admin/login/index');
            if( is_login() == -2 ){
                $this->error('您的账号已在别处登陆',$url);
            }
            $this->redirect( $url );
        }
        $Session = new Session();
        $info = $Session->info();
        $this->admin_info = $info;
        $rules = $info['rules'];
        //节点权限验证
        if( in_array('0',$rules) ){
            //存在最高权限标识即跳过权限验证
        }else{
            $moduel = request()->module();
            $controller = request()->controller();
            $action = request()->action();
            if( strtolower($controller) == 'index' && ( strtolower($action) == 'index' || strtolower($action) == 'system' ) ){
                //首页及系统信息跳过验证
            }else{
                //实例化Auth权限验证类
                $auth = new Auth();
                $passport = $auth->check($moduel.'/'.$controller.'/'.$action, is_admin() );
                if( !$passport ){
                    $this->error('无该模块的使用权限');
                }
            }
        }

        $this->assign('action_info', get_current_action_info());
    }
}