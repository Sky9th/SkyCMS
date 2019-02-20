<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\logic;

use app\admin\model\User as AdminModel;

class Admin extends Resource {

    /**
     * 资源类控制器固定列表
     * @return object collect 返回collect对象数据集
     */
    public function _list(){
        $map = $this->_filter();
        if( isset($map['title']) ){
            $map['realname|nickname|username'] = $map['title'];
            unset($map['title']);
        }
        $map['type'] = 0;
        $res = $this->_model->with(['auth'])->where($map)->order('id desc')->paginate(15);
        foreach ($res as $key => $value) {
            $roles_name = '';
            foreach ($value->getData('auth')->toArray() as $item) {
                $roles_name .= $roles_name ? ','.$item['title'] : $item['title'];
            }
            $res[$key]->roles_name = $roles_name;
        }
        return $res;
    }

}
