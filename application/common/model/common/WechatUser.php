<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/6/19
 * Time: 15:46
 */
namespace app\common\model\common;

use think\Model;

class WechatUser extends Model{

    protected $table = 'common_wechat_user';

    public function getNicknameAttr($value){
        return json_decode($value);
    }

    public function setNicknameAttr($value){
        return json_encode($value);
    }

}