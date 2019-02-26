<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule'    => 1,
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end'   => '}',
    // 视图输出字符串内容替换
    'tpl_replace_string'  =>  [
        '__STATIC__'=> '/static',
        '__AIMG__'=> '/static/flat/img',
        '__AJS__'=> '/static/flat/js',
        '__ACSS__' => '/static/flat/css',
        '__ASSET__' => '/static/flat/assets',
        '__DIST__'=> '/static/dist/admin/',
        '__DIMG__'=> '/static/dist/admin/img',
        '__DIMG__IMG__'=> '/static/dist/admin/img',
        '__DJS__'=> '/static/dist/admin/js',
        '__DCSS__' => '/static/dist/admin/css',
        '__DVENDOR__' => '/static/dist/admin/vendor',
        '__CJS__' =>  '/static/common/js',
        '__CCSS__' =>  '/static/common/css',
        '__CIMG__' =>  '/static/common/images',
        '__CASSET__' =>  '/static/common/asset',
        '__UPLOAD__' => '/uploads',
    ]
];
