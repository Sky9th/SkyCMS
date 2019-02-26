<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/10/6
 * Time: 13:54
 */
namespace app\wechat\logic;

use EasyWeChat\Kernel\Exceptions\HttpException;

class Error {

    static $error = [
        '40001' => 'AppSecret错误',
        '40002' => '不合法的凭证类型',
        '40003' => '不合法的OpenID',
        '40004' => '不合法的媒体文件类型',
        '40005' => '不合法的文件类型',
        '40006' => '不合法的文件大小',
        '40007' => '不合法的媒体文件id',
        '40008' => '不合法的消息类型',
        '40009' => '不合法的图片文件大小',
        '40010' => '不合法的语音文件大小',
        '40011' => '不合法的视频文件大小',
        '40012' => '不合法的缩略图文件大小',
        '40013' => 'APPID无效',
        '40125' => 'APPSECERT无效',

        '43004' => '需要接收者关注',
        '43005' => '需要好友关系',
        '45009' => '接口调用超过限制',
        '45011' => 'API调用太频繁，请稍候再试',
        '45047' => '客服接口下行条数超过上限',
        '48001' => 'api功能未授权',
        '48002' => '粉丝拒收消息（粉丝在公众号选项中，关闭了“接收消息”）',
        '48004' => 'api接口被封禁，请登录mp.weixin.qq.com查看详情',
    ];

    /**
     * @param HttpException $exception
     * @return array|bool
     */
    static function error($exception){
        $message = $exception->getMessage();
        if( !strpos($message, 'errcode') ){
            throw new $exception;
        }
        $result = explode(':', $message, 2);
        if( !isset($result[1]) ){
            throw new $exception;
        }
        $err = json_decode($result[1],true);
        if(isset(self::$error[$err['errcode']])){
            return error(self::$error[$err['errcode']]);
        }else{
            return error($err['errmsg']);
        }

    }

}