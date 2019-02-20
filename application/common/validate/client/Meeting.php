<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/18
 * Time: 15:09
 */
namespace app\common\validate\client;

use think\Validate;

class Meeting extends Validate {

    protected $rule = [
        'title|会议名称'  =>  'require|length:0,50',
        'short_title|会议简称' =>  'require|length:0,20',
        'hold_date|举办日期' =>  'require',
        'address|会场地址' =>  'require',
        'location|会场定位' =>  'require',
    ];


}