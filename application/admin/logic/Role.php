<?php
/**
 * Author: Sky9th
* Date: 2017/4/2
* Time: 17:11
*/

namespace app\admin\logic;

class Role extends Resource{

    public function _list(){
        $map = $this->_filter();
        $list = $this->_model->where($map)->order('id asc')->select()->toArray();
        $tree = list_to_tree($list);
        return $tree;
    }

}
