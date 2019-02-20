<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */

namespace app\admin\controller;

use app\admin\logic\Module;
use app\admin\widget\Form;
use app\admin\widget\Page;
use app\common\model\common\User;
use app\common\model\sys\Logs;

class Index extends Common
{

    public function index(){
        $form = new Form();
        $forms = [
            'username'=>[
                'title' => '用户名',
                'type' => 'input',
                'config' => [
                    'readonly' => true
                ]
            ],
            'nickname'=>[
                'title' => '昵称',
                'type' => 'input',
            ],
            'avatar'=>[
                'title' => '头像',
                'type' => 'image',
                'config' => [
                    'width'=>'32px',
                    'height'=>'32px'
                ],
            ],
            'sex'=>[
                'title' => '性别',
                'type' => 'radio',
                'config' =>[
                    'list' => ['男','女']
                ]
            ],
            'phone'=>[
                'title' => '电话',
                'type' => 'input',
            ],
            'mail'=>[
                'title' => '邮箱',
                'type' => 'input',
            ],

        ];
        $password = [
            'oldpassword'=>[
                'title' => '旧密码',
                'type' => 'password',
            ],
            'password'=>[
                'title' => '密码',
                'type' => 'password',
            ],
            'repassword'=>[
                'title' => '确认密码',
                'type' => 'password',
            ],

        ];
        $admin_form = $form->make($forms, User::get(is_login()));
        $password_form = $form->make($password);
        return view('', [
            'admin_info'=> $this->admin_info,
            'sidebar'=> Module::tree() ,
            'admin_form'=> $admin_form ,
            'password_form'=> $password_form ,
        ]);
    }

    public function system()
    {
        $sysinfo = array(
            'os' => php_uname() , //获取服务器标识的字串
            'version' => PHP_VERSION, //获取PHP服务器版本
            'action' => php_sapi_name() , //获取Apache服务器版本
            'time' => date("Y-m-d H:i:s", time()), //获取服务器时间
            'max_upload' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled", //最大上传
            'max_ex_time' => ini_get("max_execution_time") . "秒", //脚本最大执行时间
            'mysql_version' => $this->_mysql_version(),
            'mysql_size' => $this->_mysql_db_size(),
        );
        return view('system',[
            'sysinfo' => $sysinfo,
        ]);
    }

    public function log(){
        $page = new Page();
        $fields =[
            '编号' => [
                'field' => 'id',
                'type' => 'text',
            ],
            '管理员' => [
                'field' => 'admin.username',
                'type' => 'text',
            ],
            '行为' => [
                'field' => 'action.title',
                'type' => 'text',
            ],
            'ip' => [
                'field' => 'action_ip',
                'type' => 'text',
            ],
            '操作日志' => [
                'field' => 'remark',
                'type' => 'text',
                'width' => '50'
            ],
            '操作时间' => [
                'field' => 'create_time',
                'type' => 'text',
            ],
            '状态' => [
                'field' => 'status',
                'type' => 'text',
            ]
        ];
        $content = $page->make($fields, new \app\admin\logic\Resource(new Logs()), [], false);
        return $content;
    }

    public function config(){
        if(request()->isPost()){
            $error = [];
            $post = input('post.');
            unset($post['_method']);
            foreach ($post as $key => $value) {
                $file = '../config/'.$key.'.php';
                $conf = require $file;
                $conf = array_merge($conf, $value);
                $settingstr = "<?php \n return ".var_export($conf,true)." \n ?>";
                $size = file_put_contents($file, $settingstr);
                if( !$size ){
                    $error[] = $key;
                }
            }
            if( $error ){
                return error(lang('fail'));
            }else{
                app_log(1, '', 'set_config');
                return success(lang('success'));
            }
        }
        $form = new Form();
        $forms = [
            [
                'title' => '网站配置',
                'group' => [
                    'web[title]' => [
                        'title' => '网站标题',
                        'type' => 'input'
                    ],
                    'web[host]' => [
                        'title' => '主办单位',
                        'type' => 'input'
                    ],
                    'web[keyword]' => [
                        'title' => '网站关键词',
                        'type' => 'input'
                    ],
                    'web[description]' => [
                        'title' => '网站描述',
                        'type' => 'textarea'
                    ],
                    'web[copyright]' => [
                        'title' => '版权信息',
                        'type' => 'input'
                    ],
                    'web[beian]' => [
                        'title' => '备案信息',
                        'type' => 'input'
                    ],
                ],
            ],
            [
                'title' => '后台配置',
                'group' => [
                    'cms[title]' => [
                        'title' => '后台标题',
                        'type' => 'input'
                    ],
                    'cms[desc]' => [
                        'title' => '后台描述',
                        'type' => 'textarea'
                    ],
                ],
            ],
            [
                'title' => '邮箱配置',
                'group' => [
                    'mail[title]' => [
                        'title' => 'SMTP服务器地址',
                        'type' => 'input'
                    ],
                    'mail[passport]' => [
                        'title' => '邮箱帐号',
                        'type' => 'input'
                    ],
                    'mail[password]' => [
                        'title' => '邮箱密码',
                        'type' => 'input'
                    ],
                    'mail[address]' => [
                        'title' => '发件人地址',
                        'type' => 'input'
                    ],
                    'mail[name]' => [
                        'title' => '发件人称谓',
                        'type' => 'input'
                    ],
                ],
            ],
            [
                'title' => '短信配置',
                'group' => [
                    'sms[sign]' => [
                        'title' => '阿里大于签名',
                        'type' => 'input'
                    ],
                    'sms[key]' => [
                        'title' => '阿里大于appkey',
                        'type' => 'input'
                    ],
                    'sms[secret]' => [
                        'title' => '阿里大于secretkey',
                        'type' => 'input'
                    ],
                ],
            ],

        ];
        $config = [
            'web' => config('web.'),
            'cms' => config('cms.'),
            'sms' => config('sms.'),
            'mail' => config('mail.'),
        ];
        $data = [];
        foreach ($config as $key => $value) {
            foreach ($value as $k=>$v) {
                $_k = $key."[$k]";
                $_v = $v;
                $data[$_k] = $_v;
            }
        }
        $content = $form->make($forms, $data);
        return view('', ['form'=>$content]);
    }

    public function clear(){
        $fileDel[] = RUNTIME_PATH ;
        foreach ($fileDel as $key => $value) {
            if (file_exists($value)) {
                del_dir($value);
            } else {
                $this->error('缓存目录不存在');
            }
        }
        return success(lang('success'), 'self');
    }

    private function _mysql_version()
    {
        $version = db()->query("select version() as ver");
        return $version[0]['ver'];
    }

    private function _mysql_db_size()
    {
        $sql = "SHOW TABLE STATUS FROM `" . config('database.database')."`";
        $tblPrefix = config('database')['prefix'];
        if ($tblPrefix != null) {
            $sql .= " LIKE '{$tblPrefix}%'";
        }
        $row = db()->query($sql);
        $size = 0;
        foreach ($row as $value) {
            $size += $value["Data_length"] + $value["Index_length"];
        }
        return round(($size / 1048576), 2) . 'M';
    }



}
