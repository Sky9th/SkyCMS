<?php

return [
    // 全局变量规则
    '__pattern__' => [
        'name'  => '\w+',
        'year'  => '\d{4}',
        'month' => '\d{2}',
    ],

    '/$' => 'index/index',
    'verify/[:name]/[:w]/[:h]/[:fs]/[:lt]' => ['common/verify/index', [] ,[ 'w'=>'\d+','h'=>'\d+','fs'=>'\d+','lt'=>'\d+' ]],  //验证码路由
    'rollback_linkage/:table/:id/[:pid]/[:key]' => 'common/common/rollback_linkage',  //联动数据
    'image/:id/[:html]' => 'common/common/getImage',
    'area/:id' => 'common/common/get_area',
    'send_msg' => 'common/common/send_msg',
];
