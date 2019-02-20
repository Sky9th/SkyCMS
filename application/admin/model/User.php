<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\model;


class User extends \app\common\model\common\User {

    protected $resultSetType = 'collection';

    protected $type = [
        'last_login'  =>  'timestamp',
    ];

    public function auth(){
        return $this->belongsToMany('app\common\model\sys\Role','sys_auth');
    }

    public function getSexAttr($value){
        return $value ? '女' : '男' ;
    }

    public function getAvatarAttr($value, $data){
        if( $value == '0' ){
            return '/static/common/images/avatar'.$data['sex'].'.png';
        }else{
            return get_image($value);
        }
    }

    public function setPasswordAttr($value){
        return md5($value);
    }

    public function relationAdminRole($id,$ids){
        $_id = explode(',',$id);
        foreach ($_id as $item) {
            $self = self::get($item);
            $self->auth()->detach();
            if(!$self->auth()->attach($ids)){
                return error(lang('fail'));
            }
        }
        app_log(0, $id, 'access', 'admin');
        return success(lang('success'),'self');
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
