<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */
namespace app\admin\logic;

use app\common\model\sys\Module as ModuleModel;
use app\common\validate\sys\Module as ModuleValidate;

class Module extends Resource{

    public function initialize(){
        $this->_model = new ModuleModel();
        $this->_validate = new ModuleValidate();
    }

    public function _list(){
        $map = $this->_filter();
        $list = $this->_model->where($map)->append(['status_badge'])->order('sort desc')->select()->toArray();
        $tree = list_to_tree($list);
        return $tree;
    }

    public function _save(){
        $type = input('post.type');
        switch($type){
            case '0':
                $scene = 'normal';
                break;
            case '1':
                $scene = 'resource';
                break;
            case '2':
                $scene = 'module';
                break;
            default:
                $scene = '';
        }
        $res = $this->_validate->scene($scene)->check(input('post.'));
        return $this->_activeRecord(input('post.'), $res);
    }

    public function _update($id){
        $type = input('post.type');
        switch($type){
            case '0':
                $scene = 'normal';
                break;
            case '1':
                $scene = 'resource';
                break;
            case '2':
                $scene = 'module';
                break;
            default:
                $scene = '';
        }
        $data = input('put.');

        $data['id'] = $id;
        $res = $this->_validate->scene($scene)->check($data);
        return $this->_activeRecord($data, $res);
    }

    static public function tree(){

        $admin = new Session();
        $info = $admin->info();
        $auth = $info['auth'];
        $roles = $info['rules'];
        $super = false;
        foreach ($auth as $key => $value) {
            if( $value['id'] == '1' ){
                $super = true;
                break;
            }
        }
        $module = new ModuleModel();
        $list = $module->where('status',1)->where('visible',1)->order('sort desc')->select();
        foreach ($list as $key => $value) {
            if( in_array($value['id'], $roles)){
                $list[$key]['access'] = true;
            }else{
                $list[$key]['access'] = false;
            }
            if( empty($value['icon']) ){
                if( is_chinese($value['title']) ){
                    $front = msubstr($value['title'],0,1,'utf-8',false);
                }else{
                    $front = strtoupper(msubstr($value['title'],0,1,'utf-8',false));
                    $front .= strtolower(msubstr($value['title'],1,1,'utf-8',false));
                }
                $list[$key]['thumbnail'] = "{$front}";
            }else{
                $list[$key]['thumbnail'] = '<i class="'.$value['icon'].'"></i>';
            }
            $ext = [];
            if( $value['param'] ) {
                $param = explode(PHP_EOL,$value['param']);
                foreach ($param as $k => $v) {
                    list($name, $data) = explode(':', $v);
                    $ext[$name] = $data;
                }
            }
            $list[$key]['param'] = $ext;
            if($value['type'] == '0'){
                $list[$key]['url'] = url(($value['module'] ? : 'admin') . '/' . $value['src'], $ext);
            }else{
                $list[$key]['url'] = ($value['module'] ? : 'admin') . '/' . $value['name'].'s';
            }
        }
        $list = $list->toArray();
        $tree = list_to_tree($list);
        if (!$super) {
            foreach ($tree as $key => $value) {
                if ( !isset($value['_child']) && $value['access'] == false ) {
                    unset($tree[$key]);
                } else if( isset($value['_child'])  ){
                    foreach ($value['_child'] as $k => $v) {
                        if (!$v['access']) {
                            unset($tree[$key]['_child'][$k]);
                        }
                    }
                    if( count($tree[$key]['_child']) == 0 ){
                        unset($tree[$key]);
                    }
                }
            }
        }
        return $tree;
    }

}
