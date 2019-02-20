<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\logic;
use think\Model;

class Resource extends Model {

    public $_model ;
    public $_validate ;

    public function __construct($model = '', $validate = ''){
        parent::__construct();
        $this->_model = $model;
        $this->_validate = $validate;
    }


    /**
     * 资源类控制器固定列表
     * @return object collect 返回collect对象数据集
     */
    public function _list(){
        $map = $this->_filter();
        if(in_array('sort', $this->_model->getTableFields())){
            $sort = 'sort desc, id desc';
        }else{
            $sort = 'id desc';
        }
        return $this->_model->where($map)->order($sort)->paginate(15);
    }

    /**
     * 资源类控制器固定新增数据
     * @return array
     */
    public function _save(){
        $data = input('post.');
        $check = $this->_validate->scene('save')->check($data);
        return $this->_activeRecord($data, $check);
    }

    /**
     * 资源类控制器固定更新数据
     * @param $id
     * @return array
     */
    public function _update($id){
        $data = input('put.');
        $data['id'] = $id;
        $check = $this->_validate->scene('update')->check($data);
        return $this->_activeRecord($data, $check);
    }

    /**
     * 资源类控制器固定删除数据
     * @param string|array $ids
     * @param function $ids
     * @return array
     */
    public function _delete($ids){
        if( method_exists($this->_model, '_before_delete') ){
            $res = $this->_model->_before_delete($ids);
            if( $res !== true ){
                return $res;
            }
        }
        $app_log = app_log(0,  $ids, 'delete', $this->_model, '', true);
        if( $this->_model->where(['id'=>['in',$ids]])->delete() ){

            if( method_exists($this->_model, '_after_delete') ){
                $res = $this->_model->_after_delete($ids);
                if( $res !== true ){
                    return $res;
                }
            }

            $app_log->save();
            return success(lang('delete success'),'','self');
        }else{
            return error(lang('fail'));
        }
    }

    /**
     * 资源类控制器固定启用禁用
     * @param $ids
     * @param $status
     * @return array
     */
    public function _status($ids, $status){
        if( method_exists($this->_model, '_before_status') ){
            $res = $this->_model->_before_status($ids, $status);
            if( $res !== true ){
                return $res;
            }
        }
        if( $this->_model->save(['status'=>$status],['id'=>['in',$ids]]) ){
            app_log(0,  $ids, 'status', $this->_model);
            return success(lang('success'),'','self');
        }else{
            return error(lang('fail'));
        }
    }

    /**
     * 数据记录变更
     * @param $data
     * @param string $url
     * @param string $method
     * @return array
     */
    public function _record($data, $url = '', $method = ''){
        if( $url ){
            $data['url'] = $url;
        }
        $check = $this->_validate->check($data);
        return $this->_activeRecord($data, $check, $method);
    }

    /**
     * 数据更新
     * @param $data
     * @param $check
     * @param string $method
     * @return array
     */
    protected function _activeRecord($data, $check, $method = ''){
        if( isset($data['url']) && $data['url'] != '' ){
            $url = $data['url'];
            unset($data['url']);
        }else{
            $url = 'self';
        }
        if( !$method ) {
            if (isset($data['id']) && is_numeric($data['id'])) {
                $method = 'edit';
            } else {
                $method = 'add';
            }
        }
        if( !$check ){
            return error($this->_validate->getError());
        }
        else{
            if( method_exists($this->_model, '_before_write') ){
                $res = $this->_model->_before_write($data);
                if( $res !== true ){
                    return $res;
                }
            }

            if( isset($data['id']) && is_numeric($data['id']) ){

                if( method_exists($this->_model, '_before_update') ){
                    $res = $this->_model->_before_update($data);
                    if( $res !== true ){
                        return $res;
                    }
                }

                $effect = $this->_model->allowField(true)->save($data,['id'=>$data['id']]);
                if( $effect>0 ) {
                    if (method_exists($this->_model, '_after_update')) {
                        $res = $this->_model->_after_update($data);
                        if ($res !== true) {
                            return $res;
                        }
                    }
                    app_log(0, $data['id'], 'update', $this->_model);
                }
                $id = $data['id'];

            }else{

                if( method_exists($this->_model, '_before_save') ){
                    $res = $this->_model->_before_save($data);
                    if( $res !== true ){
                        return $res;
                    }
                }

                $effect = $this->_model->allowField(true)->save($data);
                if( $effect>0 ) {
                    $id = $this->_model->getLastInsID();
                    if (method_exists($this->_model, '_after_save')) {
                        $res = $this->_model->_after_save($data);
                        if ($res !== true) {
                            return $res;
                        }
                    }
                    app_log(0, $id, 'save', $this->_model);
                }
            }

            if( $effect > 0 ){
                if (method_exists($this->_model, '_after_write')) {
                    $res = $this->_model->_after_write($data);
                    if ($res !== true) {
                        return $res;
                    }
                }
                return success(lang($method.' success'), $id, $url);
            }else{
                return error(lang('error'));
            }
        }
    }

    /**
     * 默认搜索参数构造
     * @return array
     */
    public function _filter(){
        $map = [];
        $get = input('get.');
        if( isset( $get['keyword'] ) && $get['keyword'] != '' ){
            $map['title'] = ['like',"%{$get['keyword']}%"];
        }
        if( ( isset( $get['start']) && $get['start'] != '' ) && ( isset( $get['end']) && $get['end'] != '' ) ){
            $map['create_time'] = ['between',[ strtotime($get['start']), strtotime($get['end']) ]];
        }else if( isset( $get['start']) && $get['start'] != ''  ){
            $map['create_time'] = [ 'gt', strtotime($get['start']) ];
        }else if( isset( $get['end']) && $get['end'] != '' ){
            $map['create_time'] = [ 'lt', strtotime($get['end']) ];
        }
        if( isset( $get['search_status'] ) && $get['search_status'] != ''){
            $map['status'] = $get['search_status'];
        }
        return $map;
    }

}
