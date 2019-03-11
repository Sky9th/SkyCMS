<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/5/12
 * Time: 9:53
 */

namespace app\common\controller;

use EasyWeChat\Factory;
use think\Controller;
use think\Db;
use think\Log;

class Common extends Controller{

    /**
     * 关联选择回滚
     * @param $table
     * @param $id
     * @param string $pid
     * @param string $key
     * @return array
     */
    public function rollback_linkage($table,$id, $pid = 'pid', $key = 'title'){
        $child = Db::table($table)->where('pid',$id)->count();
        if( $child > 0 ){
            return error('请选择至最底层选项');
        }
        $res = rollback_linkage($table, $id, $pid);
        $breadcrumb = '';
        foreach ($res as $k => $value) {
            foreach ($value as $item) {
                if( isset($item['selected']) && $item['selected'] == true ){
                    $breadcrumb .= $breadcrumb ? '/'.$item[$key]  : $item[$key];
                }
            }
        }
        return success($breadcrumb);
    }

    /**
     * 获取图片
     * @param $id
     * @param bool $html
     * @return array|string
     */
    public function getImage($id, $html = false){
        return get_image($id, $html);
    }

    /**
     * 获取地区
     * @param $id
     * @return array
     */
    public function get_area($id){
        return success( db('area')->where('pid', $id)->select() );
    }

    /**
     * 发送消息
     */
    public function send_msg(){
        session_write_close();
        $queue = \db('notice')->where('status', 0 )->select();
        foreach ($queue as $key => $value) {
            $msg = \db('notice_receiver')->where('notice_id', $value['id'])->where('status',0)->select();
            foreach ($msg as $k => $v) {
                switch ($v['type']){
                    case '1':
                        $mail = new Email();
                        $res = $mail->send($value['title'], $value['content'], $v['contact']);
                        if( $res ){
                            \db('notice_receiver')->where('id', $v['id'])->update(['status'=>1,'send_time'=>time()]);
                        }
                        break;
                    case '2':
                        $sms = new Sms();
                        $res = $sms->dysms($v['contact'], $value['param'], $value['tmp']);
                        if( $res['status'] == '1' ){
                            \db('notice_receiver')->where('id', $v['id'])->update(['status'=>1,'send_time'=>time()]);
                        }else{
                            Log::error($res);
                        }
                        break;
                    case '3':
                        $app = Factory::officialAccount(config('wechat'));
                        $openid = \db('wechat_user')->where('id', $v['contact'])->value('openid');
                        $data = json_decode($value['data'], true);
                        $wtmp = $value['wtmp'];
                        $url = isset($data['url']) ? $data['url'] : '';
                        unset($data['url']);
                        $res = $app->template_message->send([
                            'touser' => $openid,
                            'template_id' => $wtmp,
                            'url' => $url,
                            'data' => $data,
                        ]);
                        if( $res['errcode'] == '0' ){
                            \db('notice_receiver')->where('id', $v['id'])->update(['status'=>1,'send_time'=>time()]);
                        }else{
                            Log::error($res);
                        }
                        break;
                    default:
                        break;
                }
            }
            $count = \db('notice_receiver')->where('notice_id', $value['id'])->where('status',0)->count();
            if( $count == 0 ){
                if( function_exists($value['callback']) )
                    call_user_func($value['callback']);
                \db('notice')->where('id', $value['id'])->update(['status'=>1]);
            }
        }
    }

}