<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/18
 * Time: 15:09
 */
namespace app\common\validate\client;

use think\Validate;

class MeetingAttend extends Validate {

    protected $rule = [
        'realname|姓名'  =>  'require|length:0,20',
        'phone|手机'  =>  'require|unique:client_meeting_attend|length:0,20',
        'position' => 'length:0,20'
    ];

    protected $message = [
        'phone.unique' => '重复报名'
    ];

}