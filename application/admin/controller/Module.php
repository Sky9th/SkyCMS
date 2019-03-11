<?php

namespace app\admin\controller;

use app\common\model\sys\Module as _Model;
use app\admin\logic\Module as _Logic;

class Module extends Resource
{

    protected function initialize(){
        $this->_position = 'top';
        $this->_model = new _Model();
        $this->_logic = new _Logic($this->_model, new \app\common\validate\Module());
        $this->_fields = [
            'checkbox' ,
            '编号' => [
                'field' => 'id',
                'type' => 'text',
            ],
            '标题' => [
                'field' => 'title',
                'type' => 'text',
            ],
            '标识' => [
                'field' => 'name',
                'type' => 'text',
            ],
            '真实路径' => [
                'field' => 'src',
                'type' => 'text',
            ],
            '排序' => [
                'field' => 'sort',
                'type' => 'text',
            ],
            '更新时间' => [
                'field' => 'update_time',
                'type' => 'text',
            ],
            '状态' => [
                'field' => 'status_badge',
                'type' => 'text',
            ],
            '操作' => [
                'type' => 'btn',
            ],
        ];
        //只有普通路由才能作为父节点
        $parent = $this->_model->where(['pid'=>0,'status'=>1,'type'=>0])->field('id,title')->select();
        $pid[0] = '无';
        foreach ($parent as $key => $value) {
            $pid[$value['id']] = $value['title'];
        }
        $this->_forms = [
            [
                'common' => [
                    'name'=>[
                        'title' => '唯一标识',
                        'type' => 'input',
                        'placeholder' => '唯一标识',
                        'validate' => 'require'
                    ],
                    'title'=>[
                        'title' => '名称',
                        'type' => 'input',
                    ],
                    'src'=>[
                        'title' => '真实路径',
                        'type' => 'input',
                    ],
                    'pid'=>[
                        'title' => '父节点',
                        'type' => 'select',
                        'config' =>[
                            'list' => $pid
                        ]
                    ],
                    'module' => [
                        'title' => '绑定模块',
                        'type' => 'input'
                    ],
                    'icon'=>[
                        'title' => '选择图标',
                        'type' => 'icon',
                    ],
                    'color'=>[
                        'title' => '选择颜色',
                        'type' => 'color',
                    ],
                    'intro'=>[
                        'title' => '简介',
                        'type' => 'textarea',
                    ],
                    'param'=>[
                        'title' => '参数',
                        'type' => 'textarea',
                    ],
                    'sort'=>[
                        'title' => '排序',
                        'type' => 'input',
                    ],
                    'visible'=>[
                        'title' => '是否可见',
                        'type' => 'radio',
                        'config' =>[
                            'list' => [
                                1=>'是',
                                0=>'否',
                            ]
                        ]
                    ],
                    'status'=>[
                        'title' => '状态',
                        'type' => 'radio',
                        'config' =>[
                            'list' => [
                                1=>'启用',
                                0=>'禁用',
                            ]
                        ]
                    ],
                    'table'=>[
                        'title' => '数据表',
                        'type' => 'input',
                    ],
                ],
                'bind' => 'type'
            ],
            [
                'title' => '普通路由',
                'value' =>'0',
                'group' => [
                ]
            ],
            [
                'title' => '资源路由',
                'value' =>'1',
                'group' =>[
                    'resource'=>[
                        'title' => '资源操作',
                        'type' => 'textarea',
                        'config' =>[
                            'placeholder' => "['create|创建'=>'get&/','access|授权'=>'method&route','方法|名称'=>'方式&路由']"
                        ]
                    ],
                    'log'=>[
                        'title' => '日志',
                        'type' => 'textarea',
                    ],
                ]
            ],
        ];
    }

}
