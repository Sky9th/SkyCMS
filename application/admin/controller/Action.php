<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/5/8
 * Time: 16:19
 */
namespace app\admin\controller;

class Action extends Resource{

    protected function initialize(){
        $this->_model = new \app\common\model\sys\Action();
        $this->_logic = new \app\admin\logic\Resource($this->_model);
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
            '更新时间' => [
                'field' => 'update_time',
                'type' => 'text',
            ],
            '状态' => [
                'field' => 'status',
                'type' => 'text',
            ],
            '操作' => [
                'type' => 'btn',
            ],
        ];
        $this->_forms = [
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
            'condition'=>[
                'title' => '验证条件',
                'type' => 'textarea',
            ],
            'log'=>[
                'title' => '行为日志',
                'type' => 'textarea',
            ],
            'intro'=>[
                'title' => '简介',
                'type' => 'textarea',
            ],
            'status'=>[
                'title' => '状态',
                'type' => 'radio',
                'config' =>[
                    'list' => [
                        [
                            'title' => '启用',
                            'value' => 1,
                        ],
                        [
                            'title' => '禁用',
                            'value' => 0,
                        ],
                    ]
                ]
            ],
        ];
    }

}