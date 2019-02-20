<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/18
 * Time: 15:09
 */
namespace app\common\validate\client;

use think\Validate;

class Department extends Validate {

    protected $rule = [
        'title|公司名称'  =>  'require|length:0,255',
    ];


}