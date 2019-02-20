<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/25
 * Time: 15:18
 */

namespace app\admin\widget;

use think\Controller;
use think\Db;

class Tool extends Controller{

    /**
     * 构造页面左上方快捷式操作栏
     * @param $control
                $control = [
                    'add',   顶部弹出式小型表单界面
                    'insert',    右侧弹出式大型表单界面
                    'delete',    批量删除
                    'enable',    批量启用
                    'disabled',   批量禁用
                ];
     * @return mixed
     */
    public function btn_quick($control, $model, $position){
        $action = '';
        foreach ($control as $item) {
            if( $item != 'edit' && !is_array($item) ){
                switch($item){
                    case 'delete' :
                        $name = '批量删除';
                        $action .= '<li><a class="dropdown-item" href="javascript:" onclick="ajax_'.$item.'s();return false;" >'.$name.'</a></li>';
                        break;
                    case 'enable' :
                        $name = '批量启用';
                        $action .= '<li><a class="dropdown-item" dropdown-item href="'.url(request()->module().'/'.$model.'/status',['status'=>1]).'" onclick="ajax_submit( $(\'#form-work-table\').serialize(), this.href, \'put\' );return false;" >'.$name.'</a></li>';
                        break;
                    case 'disabled' :
                        $name = '批量禁用';
                        $action .= '<li><a class="dropdown-item" dropdown-item href="'.url(request()->module().'/'.$model.'/status',['status'=>0]).'" onclick="ajax_submit( $(\'#form-work-table\').serialize(), this.href, \'put\' );return false;" >'.$name.'</a></li>';
                        break;
                }
            }else if( is_array($item) ){
                if( isset($item['batch']) ) {
                    $_a = $item['batch'];
                    $item['name'] = '批量' . $item['name'];
                    unset($item['action']);
                    foreach ($item as $k => $v) {
                        $_a = str_replace("%{$k}%", $v, $_a);
                    }
                    $action .= $_a;
                }
            }
        }
        $param['action'] = $action;
        $param['type'] = '';
        $param['disabled'] = '';
        if( in_array('add',$control) ){
            $param['type'] = 'add';
        }else{
            $param['disabled'] = 'disabled';
        }
        $param['model'] = $model;
        $param['position'] = $position;

        return $this->fetch('admin@tool/quick',$param);
    }

    /**
     * 构建数据行内快捷操作栏
     * @param $control
              [
                    'edit',  //编辑
                    'delete',  //删除
                    'enable',   //启用
                    'disabled',  //禁用
                    'detail',  //查看详情
              ]
     * @return string
     */
    public function btn_edit($control, $model, $position){

        $action = '';
        $disabled = '';
        if( !in_array('edit', $control) ){
            $disabled = 'disabled';
        }
        $count = count($control);
        $num = 2;
        if($count > $num){
            $edit_class='class="btn btn-success"';
            $class='class="dropdown-item"';
        }else{
            $edit_class ='class="btn btn-secondary"';
            $class='class="btn btn-secondary"';
        }

        $edit = '<button type="button" '.$edit_class.' onclick="open_edit_form(\''.$position.'\',\''.url(\request()->module().'/'.$model.'/edit',['id'=>'__ID__']).'\',\''.url(\request()->module().'/'.$model.'/update',['id'=>'__ID__']).'\')" '.$disabled.' >编辑</button>';


        foreach ($control as $item) {
            if( $item != 'edit' && !is_array($item) ){
                switch($item){
                    case 'delete' :
                        $name = '删除';
                        $action .= '<a '.$class.'href="'.url(request()->module().'/'.$model.'/delete',['id'=>'__ID__']).'" onclick="ajax_'.$item.'(this);return false;" >'.$name.'</a>';
                        break;
                    case 'enable' :
                        $name = '启用';
                        $action .= '<a '.$class.' href="'.url(request()->module().'/'.$model.'/status',['status'=>1,'id'=>'__ID__']).'" onclick="ajax_submit( \'\', this.href, \'put\' );return false;" >'.$name.'</a>';
                        break;
                    case 'disabled' :
                        $name = '禁用';
                        $action .= '<a '.$class.' href="'.url(request()->module().'/'.$model.'/status',['status'=>0,'id'=>'__ID__']).'" onclick="ajax_submit( \'\', this.href, \'put\' );return false;" >'.$name.'</a>';
                        break;
                    case 'detail' :
                        $name = '查看';
                        $action .= '<a '.$class.' href="'.url(request()->module().'/'.$model.'/detail',['id'=>'__ID__']).'">'.$name.'</a>';
                        break;
                    default :
                        $name = '';
                }
            }else if( is_array($item) ){
                if( isset($item['single']) ) {
                    $_a = $item['single'];
                    unset($item['action']);
                    foreach ($item as $k => $v) {
                        $_a = str_replace("%{$k}%", $v, $_a);
                    }
                    $action .= $_a;
                }
            }
        }


      if($count > $num){
         $btn = <<<html
            <td style='text-align: right;' class='conceal'>
             <div class="btn-group  btn-group-sm">
              {$edit}

          <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
          </button>

          <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(-42px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                {$action}
          </div>
        </div>
            </td>
html;
      }else{
         $btn = <<<html
            <td style='text-align: right;'>
             <div class="btn-group btn-collection mr-3">
              {$edit}
              {$action}
            </div>
            </td>
html;
      }
        return $btn;
    }

    /**
     * 构造按钮组
     * @param $btn
     * @param $simple
     * @return string
     */
    public function control($btn, $simple = false, $size = ''){
        $content = '';
        foreach ($btn as $key => $value) {
            if( !isset($value[3]) ){
                $action = 'window.location.href=\''.$value[1].'\'';
            }else {
                $set = isset($value[4]) ? $value[4] : [];
                switch ($value[3]) {
                    case 'get':
                        $action = "ajax_submit('','$value[1]','get');return false;";
                        break;
                    case 'post':
                    case 'put':
                    case 'delete':
                        $id = isset($set['id']) ? $set['id'] : 'form-work-table';
                        $action = "ajax_form('$id','$value[1]','$value[3]');return false;";
                        break;
                    case 'open':
                        $width = isset($set['width']) ? $set['width'] : '';
                        $height = isset($set['height']) ? $set['height'] : '';
                        $action = "open_frame('$value[1]','$width','$height');return false;";
                        break;
                    default :
                        $action = 'window.location.href=' . $value[1];
                        break;
                }
            }
            $value[2] = isset($value[2]) && $value[2] ? $value[2] : 'btn-default' ;
            $content .= '<div class="btn '.$size.' '.$value[2].'" onclick="'.$action.'" >'.$value[0].'</div>';
        }
        if( $simple ){
            return '<div class="btn-group">'.$content.'</div>';
        }
        return '<div class="col-xs-12 col-md-6 col-lg-6"><div class="btn-group">'.$content.'</div></div>';
    }

    /**
     * 构造搜索面板
     * @param $search
     */
    public function filter($ext = '', $status = ''){
        if( !$status ){
            $status = [[
                'title'=>'全部',
                'value'=>'',
            ],[
                'title'=>'启用',
                'value'=>'1',
            ],[
                'title'=>'禁用',
                'value'=>'0',
            ]];
        }
        return $this->fetch('admin@tool/filter',['ext'=>$ext,'status'=>$status]);
    }

    /**
     * 消息通知
     * @param int|array $admin_id
     * @param $title string
     * @param $type int
     * @param $content string
     * @param $param array
     * @param $tmp string
     * @param $data array
     * @param $template string
     * @param $callback string
     * @param $is_mail bool
     * @param $is_phone bool
     * @return bool
     */
    public function notice($admin_id, $type, $title, $content, $param = [], $tmp = '', $data = [], $wtmp = '', $callback = '',$is_mail = true, $is_phone = true, $is_wechat = true){
        if( !is_array($admin_id) ){
            $admin_id = explode(',', $admin_id);
        }
        $admin = User::all($admin_id);
        $notice = new Notice();
        $msg = ['title'=>$title,'content'=>$content, 'type'=> $type, 'param' => json_encode($param), 'data' => json_encode($data),'wtmp' => $wtmp, 'callback' => $callback, 'tmp' => $tmp ];
        $res = $notice->save($msg);
        if( !$res ){
            return error();
        }
        $notice_id = $notice->getLastInsID();
        $data = [];
        foreach ($admin as $key => $value) {
            $_l = [
                'type' => 0,
                'uid' => $value['id'],
                'notice_id' => $notice_id,
                'contact' => '',
                'status' => 1
            ];
            $data[] = $_l;
            $_l['status'] = 0;
            $mail = $value['mail'];
            if( $mail && $is_mail ){
                $_l['type'] = 1;
                $_l['contact'] = $mail;
                $data[] = $_l;
            }
            $phone = $value['phone'];
            if( $phone && $is_phone ){
                $_l['type'] = 2;
                $_l['contact'] = $phone;
                $data[] = $_l;
            }
            $wechat_user_id = $value['wechat_user_id'];
            if( $wechat_user_id && $is_wechat ){
                $_l['type'] = 3;
                $_l['contact'] = $wechat_user_id;
                $data[] = $_l;
            }
        }
        $res = Db::table('notice_receiver')->insertAll($data);
        if( $res ){
            app_log(1, $notice_id, 'notice_send_success', $msg);
            return true;
        }else{
            app_log(1,0, 'notice_send_fail', $msg);
            return false;
        }
    }

}