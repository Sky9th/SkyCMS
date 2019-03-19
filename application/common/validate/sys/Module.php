<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/19
 * Time: 13:47
 */
namespace app\common\validate\sys;

use think\Db;
use think\Request;
use think\Validate;

class Module extends Validate{

    protected $rule = [
        'name|标识' => 'require',
        'title|名称' => 'require',
        'type|模块类型' => 'require',
        'src|真实路径' => 'require',
        'visible|是否可见' => 'require',
        'module|模块' => 'require',
];

    protected $scene = [
        'normal'  => ['name','title','intro','type','icon','color','visible','module'],
        'resource'  =>  ['name','title','intro','type','src','icon','color','visible','resource','log','module'],
    ];


    protected function checkExist($value, $rule){
        $id = '';
        $routInfo = Request::instance()->routeInfo();
        if( isset( $routInfo['var'][$rule] ) ){
            $id = $routInfo['var'][$rule];
        }
        if( $id ){
            $map['id'] = ['neq',$id];
        }
        $map['module'] = input('module');
        $map['name'] = $value ;
        $count = db('sys_module')->where($map)->select();
        if( $count ){
            return '标识已存在';
        }else{
            return true;
        }
    }

}