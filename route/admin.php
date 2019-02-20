<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/16
 * Time: 15:36
 */

\think\facade\Route::rest([
    'status' => ['PUT', '/status/:status/[:id]', 'status'],
    'detail' => ['GET', '/detail/:id', 'detail'],
]);

try{
    $src = db('sys_module')->where(['route'=>1,'type'=>0,'status'=>1])->field('id,name,src,module')->select();
    foreach ($src as $item) {
        $module = $item['module'] ? $item['module'].'/':'admin/';
        $rule = $module.$item['name'];
        $route =  $module.$item['src'];
        \think\facade\Route::rule($rule,$route);
    }

    $resource = db('sys_module')->field('name,resource,module')->where(['type'=>1,'status'=>1])->select();
    foreach ($resource as $key => $value) {
        $module = $value['module'] ? $value['module'].'/':'admin/';
        $rest = $module.$value['name'].'s';
        $control = $module.$value['name'];

        \think\facade\Route::resource( $rest , $control );
        \think\facade\Route::get($rest, $control.'/index');
        /*\think\facade\Route::get($rest.'/create', $control.'/create');
        \think\facade\Route::post($rest, $control.'/save');
        \think\facade\Route::get($rest.'/:id', $control.'/read');
        \think\facade\Route::get($rest.'/:id/edit', $control.'/edit');
        \think\facade\Route::put($rest.'/:id', $control.'/update');
        \think\facade\Route::delete($rest.'/:id', $control.'/delete');
        \think\facade\Route::put($rest.'/status/:status/[:id]', $control.'/status');
        \think\facade\Route::get($rest.'/detail/:id', $control.'/detail');*/

        if( $value['resource'] ) {
            @eval('$_act=' . $value['resource'] . ';');
            if (!empty($_act)) {
                $param = [];
                foreach ($_act as $k => $v) {
                    $_r = explode('|', $k);
                    if( count($_r)>1 ){
                        $k = $_r[0];
                    }
                    $type = explode('&', $v);
                    if( !isset($type[1]) ){
                        continue;
                    }
                    if( $type[0] == '' ){
                        $type[0] = 'any';
                    }
                    call_user_func(array('\think\facade\Route', $type[0]), $rest . $type[1], $control . '/' . $k);
                    //$only['only'][] = $k;
                }
                //\think\Route::resource($rest, $control, $only);
            }
        }
    }
}catch (Exception $e){
}

return [
    /** 管理后台路由 start */
    /** 登陆模块路由注册 start */
    'admin/login/auth' =>  'admin/login/login',
    'admin/logout' => 'admin/login/logout',
    'admin/login' => 'admin/login/index',
    'admin/account' => 'admin/login/account',
    'admin/password' => 'admin/login/password',
    /** 登陆模块路由注册 end */
    'admin/system' => 'admin/index/system',
    'admin$' => 'admin/index/index',
    /** 管理后台路由 end */
];