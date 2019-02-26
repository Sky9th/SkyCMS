<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/6/19
 * Time: 15:46
 */
namespace app\wechat\logic;

use app\admin\logic\Resource;
use app\wechat\model\WechatUser as UserModel;

class User extends Resource {

    public function __construct($model = '', $validate = '')
    {
        parent::__construct($model, $validate);
        if( !$model ){
            $this->_model = new UserModel();
        }
    }

    /**
     * 判断用户是否已存在
     * @param $openid
     * @return bool
     */
    public function exist($openid){
        $id = $this->_model->where(['openid'=>$openid])->value('id');
        if( $id ){
            return $id;
        }else{
            return false;
        }
    }

    /**
     * 注册微信用户
     * @param $user
     * @return false|int
     */
    public function register($user){
        $exist = $this->exist($user['openid']);
        if( $exist ){
            $res = $this->_model->allowField(true)->save($user,['openid'=>$user['openid']]);
            if( $res ){
                $id = $exist;
                return $id;
            }
        }else{
            $res = $this->_model->allowField(true)->save($user);
            if( $res ){
                $id = $this->_model->getLastInsID();
                return $id;
            }
        }
        return false;
    }

    /**
     * 用户取消关注
     * @param $openid
     * @return false|int
     */
    public function unsubscribe($openid){
        $res = $this->_model->allowField(true)->save(['subscribe'=>0],['openid'=>$openid]);
        return $res;
    }

    /**
     * 查询用户微信资料
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function info($id){
        return $this->_model->where('id',$id)->find();
    }

}