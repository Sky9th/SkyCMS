<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/5/11
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\widget\Page;

class Admin extends Resource{

    protected function initialize(){

        $this->_js = '<script src="__AJS__/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script><script src="__ASSET__/jquery-dynatree/jquery.dynatree.min.js" type="text/javascript"></script>';
        $this->_css = '<link href="__ASSET__/jquery-dynatree/skin/ui.dynatree.css" rel="stylesheet" type="text/css" media="screen"/>';
        $this->_position = 'top';
        $this->_model = new \app\admin\model\User();
        $this->_logic = new \app\admin\logic\Admin($this->_model, new \app\common\validate\Admin());
        $this->_fields = [
            'checkbox' ,
            '编号' => [
                'field' => 'id',
                'type' => 'text',
            ],
            '账号' => [
                'field' => 'username',
                'type' => 'text',
            ],
            '所属组' => [
                'field' => 'roles_name',
                'type' => 'text',
                'width' => '20'
            ],
            '姓名' => [
                'field' => 'realname',
                'type' => 'text',
            ],
            '昵称' => [
                'field' => 'nickname',
                'type' => 'text',
            ],
            '邮箱' => [
                'field' => 'mail',
                'type' => 'text',
            ],
            '最后登陆时间' => [
                'field' => 'last_login',
                'type' => 'text',
            ],
            '创建时间' => [
                'field' => 'create_time',
                'type' => 'text',
            ],
            '状态' => [
                'field' => 'status',
                'type' => 'text',
            ],
            '操作' => [
                'type' => 'btn',
                'width' => '10'
            ],
        ];
        $this->_forms = [
            'username'=>[
                'title' => '账号',
                'type' => 'input',
            ],
            'password'=>[
                'title' => '密码',
                'type' => 'password',
                'config' => [
                    'value' => '',
                    'placeholder' => '为空则不修改密码'
                ]
            ],
            'repassword'=>[
                'title' => '确认密码',
                'type' => 'password',
            ],
            'realname'=>[
                'title' => '姓名',
                'type' => 'input',
            ],
            'nickname'=>[
                'title' => '昵称',
                'type' => 'input',
            ],
            'avatar'=>[
                'title' => '头像',
                'type' => 'image',
                'config' => [
                    'width'=>'32px',
                    'height'=>'32px'
                ],
            ],
            'sex'=>[
                'title' => '性别',
                'type' => 'radio',
                'config' =>[
                    'list' => ['男','女']
                ]
            ],
            'phone'=>[
                'title' => '电话',
                'type' => 'input',
            ],
            'mail'=>[
                'title' => '邮箱',
                'type' => 'input',
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


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(){
        $page = new Page();
        $control = [
            'add',
            'edit',
            'delete',
            'enable',
            'disabled',
            [
                'name'=>'授权',
                'batch'=>'<li><a class="dropdown-item" href="'.url('admin/admin/access').'" onclick="open_modal(this.href);return false;">%name%</a></li>',
                'single'=>'<li><a class="dropdown-item" href="'.url('admin/admin/access',['id'=>'__ID__']).'"  onclick="open_modal(this.href);return false;">%name%</a></li>'
            ],
        ];
        $content = $page->make($this->_fields, $this->_logic, [], $control, $this->_position, $this->_js, $this->_css);
        return $content;
    }

    public function access($id = ''){
        if( request()->isPost() ){
            $ids = explode(',', input('post.access_ids'));
            if( !$id ){
                $id = input('post.access_id');
            }
            if( !$id ){
                return error('请选择要操作的管理员');
            }
            return $this->_model->relationAdminRole($id,$ids);
        }
        $role = new \app\admin\logic\Role(new \app\admin\model\Role());
        $roles = $role->_list();
        foreach ($roles as $key => $value) {
            $item = [
                'key' => $value['id'],
                'title' => $value['title']
            ];
            if( isset($value['_child']) ){
                $item['unselectable'] = true;
                foreach ($value['_child'] as $k=>$v) {
                    $child_item = [
                        'key' => $v['id'],
                        'title' => $v['title']
                    ];
                    $item['children'][] = $child_item;
                }
            }
            $data[] = $item;
        }
        $param = [];
        if( $id ){
            $param['id'] = $id;
        }
        return view('', [ 'data' => json_encode($data), 'param' => $param]);
    }

}