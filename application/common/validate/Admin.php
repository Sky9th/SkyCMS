<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/19
 * Time: 13:47
 */
namespace app\common\validate;
use think\Db;
use think\Validate;

class Admin extends Validate{

    protected $rule = [
        'username|用户名' => 'require',
        'password|密码' => 'require',
        'repassword|确认密码'=>'require|confirm:password',
        'oldpassword|旧密码' => 'require|passwordRight',
        'realname|姓名' => 'require|length:2,10',
        'nickname|昵称' => 'length:2,10',
        'mail|邮箱' => 'email',
    ];

    protected $message = [
        'oldpassword.passwordRight' => '旧密码错误',
    ];

    protected $scene = [
        'save' => [ 'username','password','repassword','realname','nickname','mail' ],
        'update' => [ 'username','realname','nickname','mail' ],
        'password'  =>  ['password','repassword','oldpassword'],
        'account'  =>  ['nickname','mail'],
    ];

    public function passwordRight($value){
        $password = Db::table('admin')->where('id',is_admin())->value('password');
        if( md5($value) == $password ){
            return true;
        }
        return false;
    }

}