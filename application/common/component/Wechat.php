<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/21
 * Time: 11:25
 */
namespace app\common\component;

use app\common\model\common\Files;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use think\facade\Log;

class Wechat {

    public $app ;

    public function __construct(){
        config('app_trace', false);
        $this->app = Factory::officialAccount(config('wechat'));
    }

    /**
     * 微信服务器的交互处理
     */
    public function server(){
        $app = $this->app;
        $server = $this->app->server;
        $server->push(function($message) use($app) {
            if(config('app.app_debug')){
                Log::info($message);
            }
            switch ($message['MsgType']) {
                case 'event':
                    switch ($message['Event']) {
                        case 'subscribe':
                            if(isset($message['EventKey']) && $message['EventKey']){
                                $key = str_replace('qrscene_','',$message['EventKey']);
                                //TODO 扫码关注
                            }
                            $info = $app->user->get($message['FromUserName']);
                            $logic = new \app\wechat\logic\User();
                            $logic->register($info);
                            $conf = config('wechat.subscribe');
                            return $this->reply($conf['type'],$conf['value']);
                        case 'SCAN':
                            //TODO 扫码关注
                            break;
                        case 'unsubscribe':
                            $logic = new \app\wechat\logic\User();
                            $logic->unsubscribe($message['FromUserName']);
                            break;
                        default:
                            # code...
                            break;
                    }
                    break;
                case 'text':
                    break;
                case 'image':
                    break;
                case 'voice':
                    break;
                case 'video':
                    break;
                case 'location':
                    break;
                case 'link':
                    break;
                default:
            }
            $default = config('wechat.default');
            return $this->reply($default['type'],$default['value']);
        });
        $response = $server->serve();
        $response->send();
    }

    /**
     * 发送模版消息
     * @param $openid
     * @param $url
     * @param $param
     * @param $tmp
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function template($openid, $url, $param, $tmp){
        if( !is_array($param) ){
            $param = json_decode($param);
        }
        config('app_trace', false);
        $res = $this->app->template_message->send([
            'touser' => $openid,
            'template_id' => $tmp,
            'url' => $url,
            'data' => $param,
        ]);
        return $res;
    }

    /**
     * 回复微信消息
     * @param $type
     * @param $value
     * @return Image|News|Video|Voice|array|bool
     */
    public function reply($type, $value){
        $files = new Files();
        switch ($type) {
            case 'text':
                return $value;
                break;
            case 'image':
                $file = $files->find($value);
                $reply = new Image($file['media_id']);
                return $reply;
            case 'video':
                $file = $files->find($value);
                $reply = new Video($file['media_id'], ['title' => $file['title'], 'description' => $file['description']]);
                return $reply;
            case 'audio':
                $file = $files->find($value);
                $reply = new Voice($file['media_id']);
                return $reply;
            case 'article':
                //TODO 回复图文消息
                /*$reply = [];
                $article = \app\wechat\model\Article::get($value,'article_item');
                foreach ($article['article_item'] as $item) {
                    $reply[] = new NewsItem([
                        'title' => $item['title'],
                        'description' => $item['digest'],
                        'url' => $item['url'],
                        'image' => request()->domain().get_image($item['cover'], false)
                    ]);
                }
                $news = new News($reply);
                return $news;*/
            default:
                return false;
        }
    }

}