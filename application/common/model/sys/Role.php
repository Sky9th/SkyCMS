<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */
namespace app\common\model\sys;

use app\common\model\Sys;

class Role extends Sys {

    protected $table = 'sys_role';
    protected $resultSetType = 'collection';

    public function auth(){
        return $this->belongsToMany('User','auth');
    }

    public function getStatusAttr($value)
    {
        $status = config('static.status');
        return $status[$value];
    }

    public function _before_update($data){
        if( $data['id'] == '1' ){
            return error('超级管理者不允许修改');
        }
        return true;
    }

    public function _before_delete($data){
        if( !is_array($data) ){
            $data = explode(',', $data);
        }
        if( in_array('1', $data) || $data == '1' )
        {
            return error('超级管理者不允许删除');
        }
        return true;
    }

}
