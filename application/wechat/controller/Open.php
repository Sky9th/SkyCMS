<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/5/19
 * Time: 17:12
 */

namespace app\wechat\controller;

use app\admin\controller\Common;
use app\admin\widget\Form;
use app\wechat\logic\Error;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\HttpException;

class Open extends Common{

    public function set(){
        if(request()->isPost()){
            $error = [];
            $post = input('post.');
            unset($post['_method']);
            foreach ($post as $key => $value) {
                $file = '../application/extra/'.$key.'.php';
                $conf = [];
                if( is_file($file) ) {
                    $conf = require $file;
                }
                $conf = array_merge($conf, $value);

                $str = my_var_export($conf);

                $settingstr = "<?php \n return ".$str." \n ?>";
                $size = file_put_contents($file, $settingstr);
                if( !$size ){
                    $error[] = $key;
                }
            }

            if( $error ){
                return error(lang('fail'));
            }else{

                $config = config('wechat');
                $wechat = Factory::officialAccount($config);
                try{
                    $accessToken = $wechat->access_token; // EasyWeChat\Core\AccessToken 实例
                    $token = $accessToken->getToken(true); // token 字符串
                }catch (HttpException $e){
                    return Error::error($e);
                }
                if( !isset($token['access_token']) ){
                    return error('无法从微信服务器获取信息,请确认配置信息是否正确');
                }
                return success(lang('success'),url('index'));


            }
        }
        $form = new Form();
        $forms = [
            'wechat[title]' => [
                'title' => '公众号名称',
                'type' => 'input'
            ],
            'wechat[account]' => [
                'title' => '公众号账号',
                'type' => 'input'
            ],
            'wechat[oid]' => [
                'title' => '公众号原始ID',
                'type' => 'input'
            ],
            'wechat[token]' => [
                'title' => '微信公众平台TOKEN',
                'type' => 'input'
            ],
            'wechat[app_id]' => [
                'title' => '微信公众平台AppId',
                'type' => 'input'
            ],
            'wechat[secret]' => [
                'title' => '微信公众平台AppSecret',
                'type' => 'input'
            ],
            'wechat[mch_app_id]' => [
                'title' => '微信支付主商户APPID',
                'type' => 'input'
            ],
            'wechat[mch_id]' => [
                'title' => '微信支付商户号',
                'type' => 'input'
            ],
            'wechat[key]' => [
                'title' => '微信支付Key',
                'type' => 'input'
            ],
            'wechat[sub_mch_id]' => [
                'title' => '微信支付子服务商商户号',
                'type' => 'input'
            ],
            'wechat[sub_app_id]' => [
                'title' => '微信支付子服务商AppID',
                'type' => 'input'
            ],
            'wechat[encrypt]' => [
                'title' => '加密方式',
                'type' => 'select',
                'config' => [
                    'list' => [
                        0=>'明文',
                        1=>'混合',
                        2=>'安全',
                    ]
                ]
            ],
            'wechat[aes_key]' => [
                'title' => '微信公众平台EncodingAESKey',
                'type' => 'input'
            ],
            'wechat[type]' => [
                'title' => '类型',
                'type' => 'select',
                'config' => [
                    'list' => [
                        '认证订阅号',
                        '认证服务号',
                    ]
                ]
            ],
            'wechat[debug]' => [
                'title' => '日志',
                'type' => 'radio',
                'config' => [
                    'list' => [
                        1 =>[
                            'title' => '是',
                            'value' => 1,
                            'checked' => true
                        ],
                        0 =>'否',
                    ]
                ]
            ],
        ];
        $conf = [
            'log' => [
                'level' => 'debug',
                'file'  => './easywechat.log',
            ],
        ];
        $config = config('wechat.');
        foreach ($config as $key => $value) {
            $conf[ "wechat[$key]" ] = $value;
        }
        $content = $form->make($forms, $conf);
        return view('', ['form'=>$content]);
    }

}