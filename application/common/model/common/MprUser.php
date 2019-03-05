<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/6/19
 * Time: 15:46
 */
namespace app\common\model\common;

use think\Model;

class MprUser extends Model{

    protected $table = 'common_mpr_user';

    public function getNicknameAttr($value){
        return json_decode($value) ?  json_decode($value) : $value;
    }

    public function setNicknameAttr($value){
        return json_encode($value);
    }

}