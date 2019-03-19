<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/7
 * Time: 13:21
 */
namespace app\common\model\client;

use app\common\model\Client;

class MagazineIssue extends Client {

    protected $table = 'client_magazine_issue';

    public function magazine(){
        return $this->belongsTo('magazine', 'magazine_id', 'id');
    }

    public function chapter(){
        return $this->hasMany('magazineIssueChapter', 'magazine_issue_id', 'id');
    }

    public function getCoverUrlAttr($value, $data){
        return $this->getImage($data['cover']);
    }

    public function getChapterCountAttr($value, $data){
        return $this->chapter()->where('magazine_issue_id', $data['id'])->count();
    }

}