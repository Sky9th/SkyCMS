<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/24
 * Time: 13:34
 */

namespace app\admin\widget;

use think\Controller;
use think\Db;

class Form extends Controller {

    private $data = [];

    private static $config  = [
        'value'=>'',    //值
        'placeholder'=>'',  //说明
        'validate'=>'',   //前端验证
        'hidden'=>'',    //是否隐藏表单
        'disabled'=>'',    //是否可用
        'prefix'=> '' ,    //ID前缀
        'pid'=>'',    //联动数据父键
        'table'=>'',    //联动数据父键
        'json'=>'',    //联动数据数据源
        'nolayout'=>'',    //去除父层
        'readonly'=>'',    //去除父层
        'data' => '',
        'wechat' => '',
        'width' => '',
        'height' => '',
    ];

    /**
     * 初始化配置数组
     * @param $config
     * @return array
     */
    private static function get_config($config = []){
        if ( !isset($config['prefix'])  ){
            $config['prefix'] = uniqid('form_').'_';
        }
        return array_merge(self::$config,$config);
    }

    /**
     * 构造表单
     * @param array $forms
     * @param array $data 传入表单的默认值
     * @param bool $data 传入表单的默认值
     * @return mixed|string
     */
    public function make($forms, $data = [], $page = false){
        $this->data = $data;
        $end = end($forms);
        if( isset($end['common']) || isset($end['group']) ) {
            $content  = $this->group($forms, $data);
        }else{
            $content  = $this->simple($forms, $data);
        }
        if( !$page ) {
            return $content;
        }else{

            return $this->fetch('admin@form/page', ['content'=>$content]);
        }
    }

    /**
     * 构造简单表单
     * @param $data array|object 表单默认值
     * @param $forms
              [
                 'name' => 'name',
                 'title' => '唯一标识',
                 'type' => 'input',
                 'placeholder' => '唯一标识',
              ],
              [
                 'name' => 'title',
                 'title' => '名称',
                 'type' => 'input',
              ]
     * @return string
     */
    public function simple($forms, $data = []){
        $content = '';
        foreach ($forms as $key => $value) {

            if( method_exists($this,$value['type'])){
                $config = [];
                if( !empty($data) ){
                    if( is_object($data) ){
                        $data = $data->getData();
                    }
                    $config['data'] = $data;
                }
                if( isset( $data[$key] ) ){
                    $config['value'] = $data[$key];
                }
                if( isset( $value['config'] ) && is_array( $value['config'] ) ){
                    $config = array_merge($config,$value['config']);
                }

                $content .= call_user_func(array($this, $value['type']),$key,$value['title'],$config);

            }
        }

        return $content;
    }

    /**
     * 构造分组表单
     * @param $data array|object 表单默认值
     * @param $forms
     *          //简单结构
    [
    'name' => 'name',
                    'title' => '唯一标识',
                    'type' => 'input',
                    'placeholder' => '唯一标识',
                    ],
                    [
                    'name' => 'title',
                    'title' => '名称',
                    'type' => 'input',
                    ],
                ];
                //分组结构
                [
                    'common' => [
                            [
                            'name' => 'name',
                            'title' => '唯一标识',
                            'type' => 'input',
                            'placeholder' => '唯一标识',
                            'validate' => 'require'
                            ]
                    ]
                ],
                [
                    'title' => '基础配置',
                    'group' => [
                        [
                            'name' => 'title',
                            'title' => '名称',
                            'type' => 'input',
                        ]
                    ]
                ],
                [
                    'title' => '普通路由',
                    'group' => [
                        [
                            'name' => 'src',
                            'title' => '真实路径',
                            'type' => 'input',
                        ]
                    ]
                ];
                //分组互不干扰结构：每次只提交选中分组表单以及公共表单
                [
                    'common' => [
                        [
                            'name' => 'name',
                            'title' => '唯一标识',
                            'type' => 'input',
                            'placeholder' => '唯一标识',
                            'validate' => 'require'
                        ]
                    ],
                    'bind' => 'type'  //分组tab绑定的字段
                ],
                [
                    'title' => '普通路由',
                    'value' =>'0',  //分组tab绑定的值
                    'group' => [
                        [
                            'name' => 'src',
                            'title' => '真实路径',
                            'type' => 'input',
                        ],
                    ],
     *          ],
                [
                    'title' => '资源路由',
                    'value' =>'1',
                    'group' => [
                        [
                            'name' => 'src',
                            'title' => '真实路径',
                            'type' => 'input',
                        ],
                    ]
                ]
     * @return mixed
     */
    public function group($forms, $data){
        $common = '';
        $form = [];
        foreach ($forms as $key => $value) {
            if( isset( $value['common'] ) ||  isset( $value['bind'] ) ) {
                if( isset($value['common']) ) {
                    $common = $this->simple($value['common'], $data);
                }
                if( isset($value['bind']) ) {
                    if( isset($data[$value['bind']]) ){
                        $param['bind_selected'] = $data[$value['bind']];
                    }
                    $bind = $value['bind'];
                    $param['bind'] = $bind;
                }
            }else {
                $form[$key] = $value;
                $form[$key]['content'] = $this->simple($value['group'], $data);
            }
        }
        if( !isset($param['bind_selected']) && isset($forms[0]['bind']) ){
            $param['bind_selected'] = current($form)['value'];
        }
        $param['form'] = $form;
        $param['common'] = $common;
        return $this->fetch('admin@form/group', $param);
    }

    /**
     * 返回对应表单HTML结构
     * @param $template
     * @param $param
     * @return mixed
     */
    public function show($template, $param){
        $content = $this->fetch($template,$param);
        if( $param['config']['nolayout'] == true ){
            return $content;
        }else {
            $param = array_merge($param, [ 'content' =>$content] );
            return $this->fetch('admin@form/form_group', $param);
        }
    }

    /**
     * 构造一个简单input
     * config  string value 传值
     *         string placeholder 说明
     *         array validate 验证
     *         bool hidden 验证
     *         bool disabled 是否可用
     *         string prefix ID前缀
     * @param $name
     * @param $title
     * @param $config
     * @return \think\response\View
     */
    public function input($name, $title, $config , $type = 'input'){
        $config = self::get_config($config);
        return $this->show('admin@form/input',['name'=>$name, 'title'=>$title, 'config'=>$config, 'type'=>$type]);
    }
    public function password($name, $title, $config){
        return $this->input($name,$title,$config,'password');
    }

    /**
     * 构造时间输入框
     * config  string value 传值
     *         string placeholder 说明
     *         array validate 验证
     *         bool hidden 验证
     *         bool disabled 是否可用
     *         string prefix ID前缀
     * @param $name
     * @param $title
     * @param $config
     * @return \think\response\View
     */
    public function date($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/date',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }
    public function time($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/time',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }
    public function range($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/range',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个select
     * config  array list 选项
     *               [
     *                  title => value,
     *                  title => [
     *                               value,     //值
     *                               selected,  //是否选中
     *                               disabled,  //是否可用
     *                          ]
     *               ]
     *          array group => [   //分组选项
     *                            'title' => group_name
     *                            'list' => list
     *                        ]  //分组选项
     *         array validate 验证
     *         bool disabled 是否可用
     *         bool multi 是否多选
     * @param $name
     * @param $title
     * @param $list
     * @param $config
     * @return \think\response\View
     */
    public function select($name, $title, $config){
        $config = self::get_config($config);
        $data = [];
        if( $config['table'] ){
            $na = isset($config['name']) ? $config['name'] : 'title';
            $pk = isset($config['pk']) ? $config['pk'] : 'id';
            $list = Db::table($config['table'])->field("$pk,$na")->where('status',1)->select();
            foreach ($list as $key=>$value) {
                $data[$value[$pk]] = $value[$na];
            }
            $config['list'] = $data;
        }

        return $this->show('admin@form/select',['name'=>$name, 'title'=>$title, 'config'=>$config ]);
    }

    /**
     * 构造联动下拉列表
     * config array [
                        'table' => 'Article_category',   //数据来源表
                        'pid' => 'pid'  //父键
     *              ]
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function linkage_select($name, $title, $config){
        $config = self::get_config($config);
        $arr = [];
        if(isset($config['value']) && $config['value']){
            $arr = rollback_linkage($config['table'], $config['value'], $config['pid']);
        }
        return $this->show('admin@form/linkage_select',['name'=>$name, 'title'=>$title, 'config'=>$config, 'arr'=>$arr]);
    }

    /**
     * 构造一个textaera
     * config  string value 传值
     *         string placeholder 说明
     *         array validate 验证
     *         bool disabled 是否可用
     *         string prefix ID前缀
     *         int rows 行数
     * @param $name
     * @param $title
     * @param $config
     * @return \think\response\View
     */
    public function textarea($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/textarea',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个radio
     * config  array list 选项
     *                [
                            [
                                'value' => '1',
                                'title' => '是',
                            ],
                            [
                                'value' => '2',
                                'title' => '否',
                                'checked' => true,
                            ],
                            [
                                'value' => '3',
                                'title' => '或',
                                'disabled' => true,
                            ],
                      ],
     *         string placeholder 说明
     *         string prefix ID前缀
     * @param $name
     * @param $title
     * @param $config
     * @return \think\response\View
     */
    public function radio($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/radio',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个checkbox
     * config  array list 选项
     *                [
                            [
                                'value' => '1',
                                'title' => '是',
                                'checked' => true,
                            ],
                            [
                                'value' => '2',
                                'title' => '否',
                                'checked' => true,
                            ],
                            [
                                'value' => '3',
                                'title' => '或',
                                'disabled' => true,
                            ],
                      ],
     *         string placeholder 说明
     *         string prefix ID前缀
     * @param $name
     * @param $title
     * @param $config
     * @return \think\response\View
     */
    public function checkbox($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/checkbox',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 自定义表单回调
     * @param $name
     * @param $title
     * @param $config
     * @param $form
     * @return mixed
     */
    public function callback($name, $title, $config){
        $config = self::get_config($config);
        $config['name'] = $name;
        $config['title'] = $title;
        return call_user_func($config['callback'],$config);
    }

    /**
     * 构造一个富文本编辑器
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function ueditor($name, $title, $config){
        $config = self::get_config($config);
        return $this->fetch('admin@form/ueditor',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个单图片上传
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function image($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/image',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个多图片上传
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function images($name, $title, $config){
        $config = self::get_config($config);
        $config['images'] = $config['value'] ? explode(',', $config['value']) : [];
        return $this->show('admin@form/images',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个单音频上传
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function audio($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/audio',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个单视频上传
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function video($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/video',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个图标选择器
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function icon($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/icon',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造一个颜色选择器
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function color($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/color',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }


    /**
     * 构造一个文件上传器
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function file($name, $title, $config){
        $config = self::get_config($config);
        return $this->show('admin@form/file',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }

    /**
     * 构造多个文件上传器
     * @param $name
     * @param $title
     * @param $config
     * @return mixed
     */
    public function files($name, $title, $config){
        $config = self::get_config($config);
        $config = self::get_config($config);
        $config['images'] = $config['value'] ? explode(',', $config['value']) : [];
        return $this->show('admin@form/files',['name'=>$name, 'title'=>$title, 'config'=>$config]);
    }


    /**
     * 获取表单传值
     * @return array
     */
    public function getData(){
        return $this->data;
    }

}