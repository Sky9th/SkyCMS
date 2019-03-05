<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/21
 * Time: 11:25
 */
namespace app\common\component;

use app\common\model\common\MprUser;
use app\common\model\common\User;
use EasyWeChat\Factory;

/**
 * 小程序对接
 * Class Mpr
 * @property Factory::miniProgram $app
 * @package app\common\component
 */
class Mpr {

    public $app ;

    public function __construct(){
        config('app_trace', false);
        $this->app = Factory::miniProgram(config('mpr.'));
    }

    /**
     * 小程序注册用户信息
     * @param $info
     * @param $session_key
     * @return array|bool
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function register($info, $session_key){
        $session = cache($session_key);
        $decryptedData = $this->app->encryptor->decryptData($session['session_key'], $info['iv'], $info['encryptedData']);
        $mprUsers = new MprUser();
        $decryptedData['avatarurl'] = $decryptedData['avatarUrl'];
        $decryptedData['nickname'] = $decryptedData['nickName'];
        $res = $mprUsers->save($decryptedData, ['openid'=>$decryptedData['openId']]);
        if($res){
            return $decryptedData;
        }else{
            return false;
        }
    }

    /**
     * 小程序登录用户
     * @param $code
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login($code){
        $info = $this->app->auth->session($code);
        $openid = $info['openid'];
        $mprUsers = new MprUser();
        $exist = $mprUsers->where('openid', $openid)->find();
        $session = md5($exist['openid'].time());
        if ($exist) {
            $id = $exist['id'];
        } else {
            $mprUsers->save([
                'openid' => $info['openid']
            ]);
            $id = $mprUsers->id;
        }
        $users = new User();
        $user_id = $users->where('mpr_user_id', $id)->value('id');
        cache('mpr_'.$session, [
            'user_id' =>  $user_id,
            'mpr_user_id' =>  $id,
            'openid' => $info['openid'],
            'session_key' => $info['session_key']
        ], 3600 * 24);
        return 'mpr_'.$session;
    }
}