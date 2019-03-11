<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/5/19
 * Time: 11:22
 */

namespace app\admin\controller;

use think\Db;

class Extra extends Common {

    /**
     * 联动搜索数据源 ----- 数据库表模式
     * @param $table
     * @param $id
     * @param string $pid
     */
    public function linkage($table, $id, $pid = 'pid', $title = 'title'){
        if(request()->isAjax()) {
            if (!in_array(strtolower($table), config('static.linkage'))) {
                return false;
            }
            $result[] = ['id' => '', 'text' => '请选择'];
            $data = Db::table($table)->where([$pid => $id])->select();
            if (empty($data)) {
                $result = [];
            }
            foreach ($data as $key => $value) {
                $result[] = [
                    'id' => $value['id'],
                    'text' => $value[$title]
                ];
            }
            return success($result);
        }
    }

    public function icon(){
        return view();
    }

}