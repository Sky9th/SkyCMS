<?php

namespace app\admin\controller;

use app\common\model\sys\Role as _Model;
use app\admin\logic\Resource as _Logic;
use Auth\Auth;
use think\Db;

class Role extends Resource
{

    protected function initialize(){

        $this->_model = new _Model();
        $this->_logic = new _Logic($this->_model,new \app\common\validate\Role());
        $this->_fields = [
            'checkbox' ,
            '编号' => [
                'field' => 'id',
                'type' => 'text',
            ],
            '角色' => [
                'field' => 'title',
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
        $this->_forms = [
            'title'=>[
                'title' => '名称',
                'type' => 'input',
            ],
            'pid'=>[
                'title' => '父节点',
                'type' => 'linkage_select',
                'config' =>[
                    'table' => 'sys_role',
                    'pid' => 'pid'
                    //无限联动
                ]
            ],
            'rules'=>[
                'title' => '权限节点',
                'type' => 'callback',
                'config' => ['callback' => [ $this, 'rules' ] ]
            ],
            'status'=>[
                'title' => '状态',
                'type' => 'radio',
                'config' =>[
                    'list' => [
                        '禁用' ,
                        '启用' ,
                    ]
                ]
            ],
        ];
    }

    public function rules($param){
        $parents = [];
        if( isset( $param['data']['pid'] ) ) {
            $parents = $this->getParentRules($param['data']['pid']);
        }
        $ids = explode(',',$param['value']);
        $data = [];
        $module = Db::table('sys_module')->order('sort desc')->select();
        $tree = list_to_tree($module);
        foreach ($tree as $key => $value) {
            $data[] = $this->makeTreeRules($value, $ids, $parents);
        }
        foreach ($data as $key => $value) {
            $json[] = json_encode($value);
        }
        return $this->fetch('rules',['config'=>$param,'json'=>$json]);
    }

    public function makeTreeRules($value, $ids , $parents){
        $item = [
            'title' => $value['title'],
            'key' => $value['id']
        ];
        if( in_array($value['id'], $ids) ){
            $item['select'] = true;
        }else{
            if( !in_array($value['id'], $parents) && !empty($parents) ){
                $item['hideCheckbox'] = true;
            }
        }

        if( $value['type'] == '1' ){
            $item['children'] = $this->makeResourceRules($value, $ids);
        }
        if( isset($value['_child']) && !empty($value['_child'] ) ){
            foreach ($value['_child'] as $_item) {
                $item['children'][] = $this->makeTreeRules($_item, $ids, $parents);
            }
        }

        return $item;
    }

    private function getParentRules($pid){
        $role = Db::table('role')->where('id', $pid)->find();
        $ids = [];
        if( !empty($role['rules']) ) {
            $ids = explode(',', $role['rules']);
        }
        if( $role['pid'] != '0' && !empty($role) ){
            $ids = array_merge($ids,$this->getParentRules($role['pid']));
        }
        return $ids;
    }

    private function makeResourceRules($value, $ids, $id = ''){
        if( !$id ){
            $id = $value['id'];
        }
        $resource = [];
        $item['isFolder'] = true;
        if( $value['resource'] ){
            eval('$ext = '.$value['resource'].';');
        }
        $set = Auth::getResourceSet();
        if( isset($ext) ){
            foreach ($ext as $key=>$item) {
                if( !in_array($key,$set) )
                    array_push($set, $key);
            }
        }
        foreach ($set as $k=>$v) {
            if( in_array($v, [ 'save','update','read' ] ) ){
                continue;
            }
            switch ($v) {
                case 'index':
                    $title = $value['title'].'列表';
                    break;
                case 'create':
                    $title = $value['title'].'新建';
                    break;
                case 'edit':
                    $title = $value['title'].'编辑';
                    break;
                case 'delete':
                    $title = $value['title'].'删除';
                    break;
                case 'status':
                    $title = $value['title'].'审核';
                    break;
                case 'detail':
                    $title = $value['title'].'详情';
                    break;
                default:
                    list($rest,$title) = explode('|', $v );
                    $title = $value['title'].$title;
            }
            $item = [
                'title' => $title,
                'key' => $id.'.'.count($resource)
            ];
            if( in_array($item['key'], $ids) ){
                $item['select'] = true;
            }
            $resource[] = $item;
        }
        return $resource;
    }


}
