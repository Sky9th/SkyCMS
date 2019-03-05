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

class Mpr extends Common{


    public function set(){
        if(request()->isPost()){
            $error = [];
            $post = input('post.');
            unset($post['_method']);
            foreach ($post as $key => $value) {
                $file = '../config/'.$key.'.php';
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
                return success(lang('success'),url('index'));
            }
        }
        $form = new Form();
        $forms = [
            'mpr[title]' => [
                'title' => '小程序名称',
                'type' => 'input'
            ],
            'mpr[app_id]' => [
                'title' => '小程序AppId',
                'type' => 'input'
            ],
            'mpr[secret]' => [
                'title' => '小程序AppSecret',
                'type' => 'input'
            ],
            'mpr[token]' => [
                'title' => '小程序TOKEN',
                'type' => 'input'
            ],
            'mpr[aes_key]' => [
                'title' => '小程序EncodingAESKey',
                'type' => 'input'
            ],
            'mpr[debug]' => [
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
        $config = config('mpr.');
        foreach ($config as $key => $value) {
            $conf[ "mpr[$key]" ] = $value;
        }
        $content = $form->make($forms, $conf);
        return view('open/set', ['form'=>$content]);
    }

}