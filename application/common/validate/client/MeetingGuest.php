<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/18
 * Time: 15:09
 */
namespace app\common\validate\client;

use think\Validate;

class MeetingGuest extends Validate {

    protected $rule = [
        'title|嘉宾姓名'  =>  'require|length:0,50',
        'position|嘉宾职位' =>  'require|length:0,50',
        'company|嘉宾单位' =>  'require|length:0,100',
        //'avatar|嘉宾照片' =>  'require',
        'description|嘉宾简介' =>  'length:0,255',
    ];


}