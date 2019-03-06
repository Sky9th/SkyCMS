<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/7
 * Time: 10:16
 */
namespace app\common\model;

use think\Model;

class Common extends Model{

    public $allowField = true;
    public $visibleField = [];
    public $order = 'id desc';

    public function getStatusTextAttr($value, $data)
    {
        $status = config('static.status_name');
        return $status[$data['status']];
    }

    public function getStatusBadgeAttr($value, $data)
    {
        $status = config('static.status_badge');
        return $status[$data['status']];
    }

    public function getImage($image_id){
        if(!$image_id){
            return '';
        }
        return 'http://'.$_SERVER['HTTP_HOST'].get_image($image_id);
    }

}