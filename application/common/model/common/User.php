<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/7
 * Time: 10:13
 */
namespace app\common\model\common;

use app\common\model\Common;

class User extends Common {

    protected $table = 'common_user';

    public function auth(){
        return $this->belongsToMany('app\common\model\sys\Role','sys_auth');
    }

    public function department(){
        return $this->belongsToMany('app\common\model\client\Department', 'client_user_to_department');
    }

}