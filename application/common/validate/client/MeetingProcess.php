<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/18
 * Time: 15:09
 */
namespace app\common\validate\client;

use think\Validate;

class MeetingProcess extends Validate {

    protected $rule = [
        'title|议程介绍'  =>  'require|length:0,255',
        'start_time|议程开始时间' =>  'require',
        'end_time|议程结束时间' =>  'require',
    ];


}