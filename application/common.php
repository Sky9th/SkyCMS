<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 返回错误状态的数组
 * @param bool $msg
 * @param string $url
 * @param int $code
 * @return array
 */
function error($msg = false, $url = '', $code = 0){
    if( !$msg ){
        $msg = '系统繁忙，请稍后再试';
    }
    $result = [
        'code' => $code,
        'msg'  => $msg,
        'url'  => $url
    ];
    return $result;
}

/**
 * 返回正确状态的数组
 * @param string $msg
 * @param mixed $data
 * @param string $url
 * @return array
 */
function success($msg = '', $data = '', $url = ''){
    $result = [
        'code' => 1,
        'msg'  => $msg,
        'data' => $data,
        'url'  => $url
    ];
    return $result;
}

/**
 * 4.2.检测后台用户是否登陆并返回其用户ID
 * @return int|mixed
 */
function is_login(){
    $user_id = session(md5('user_id')) ;
    $user = new \app\common\model\common\User();
    if( empty($user_id) ){
        $username = cookie(md5('username'));
        if( empty($username) ){
            return 0;
        }
        $password = cookie( md5('password') );
        $exist = $user->where(array('username'=>$username))->find();
        if( $password == sha1( $username.$exist['password'].$_SERVER['HTTP_USER_AGENT']) ){
            $session = new \app\admin\logic\Session();
            $session->do_login($exist,false);
            return $exist['id'];
        }else{
            return 0;
        }
    }
    $last_session_id = $user->where((array('id'=>$user_id)))->value('last_login_session');
    if( $last_session_id != session_id() ){
        if( session( md5('username') ) ){
            return -2;
        }else{
            return 0;
        }
    }
    if (empty($user_id)) {
        return 0;
    } else {
        return $user_id;
    }
}


/**
 * 记录日志
 * @param $type int 0:资源类日志,1:行为类日志,3:自定义日志
 * @param int $id  int数据变动的主键ID
 * @param string $method  string资源类日志的操作类型或行为规则的标识
 * @param array|string $model 操作模型或者表 | array 行为规则传入数据
 * @param string $rule  日志规则
 * @param bool $delay  延迟写入
 */
function app_log($type, $id, $method, $model = false, $rule = '',  $delay = false){
    $logs = new \app\common\model\sys\Logs();
    if( $type == 0 ){
        return $logs->resourceLog($id, $method, $model, $delay);
    }else if( $type == 1 ){
        return $logs->actionLog($id, $method, $model, $delay);
    }else{
        return $logs->customLog($method, $rule);
    }
}


/**
 * 获取客户端ip
 * @return null|string
 */
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
 * 获取当前模块信息
 * @return mixed
 */
function  get_current_action_info(){
    $module = request()->module();
    $src = strtolower(request()->controller() . '/' . request()->action());
    $model = new \app\common\model\sys\Module();
    $info = $model->where('module', $module)->where('src', $src)->where('status', 1)->field('title,intro,src')->find();
    if (!$info) {
        $src = request()->controller() . '/index';
        $info = $model->where('module', $module)->where('src', $src)->where('status', 1)->field('title,intro,src')->find();
    }
    return $info;
}

/**
 * 验证码<img>快捷生成
 * @param string $id
 * @param int $w 宽
 * @param int $h 高
 * @param int $fs 字体大小
 * @param int $lt 验证码位数
 */
function captcha_make($name = '', $w = 240, $h = 60, $fs = 30, $lt = 5){
    $src = url('common/verify/index',array(
        'name' => $name,
        'w' => $w,
        'h' => $h,
        'fs' => $fs,
        'lt' => $lt,
    ));
    $alt = config('WEB_TITLE').'验证码';
    $img = '<img src="'.$src.'" _src="'.$src.'" class="captcha_verify" onclick="captcha_refresh(this)" alt="'.$alt.'" />';
    return $img;
}

/**
 * 获取图片
 * @param $id
 * @param bool $html
 * @param bool $first
 * @param string $width
 * @param string $height
 * @return string|array
 */
function get_image($id, $html = false, $first = false , $width = '', $height  = ''){
    if( !is_numeric($id) ){
        if( is_array($id) ){
            $ids = $id;
        }else{
            $ids = explode(',',$id);
        }
        if( $first ){
            return get_image($ids[0], $html, false, $width, $height);
        }
        return get_images($ids);
    }else {
        $model = new \app\common\model\common\File();
        $file = $model->find($id);
        $src = str_replace('\\','/', $file['src']);
        if ($file) {
            if (is_file("./uploads/images/" . $src)) {
                if (!$html) {
                    $img = '/uploads/images/' . $src;
                } else {
                    $img = '<img src="/uploads/images/' . $src . '" alt="' . $file['title'] . '"  width="'.$width.'"  height="'.$height.'" >';
                }
                return $img;
            }

        }
    }
    return '';
}



/**
 * 批量获取图片
 * @param $id
 * @param bool $html
 * @param bool $first
 * @param string $width
 * @param string $height
 * @return string|array
 */
function get_images($ids){
    $imgs = [];
    $model = new \app\common\model\common\File();
    $file = $model->where('id','in', $ids)->select();
    foreach ( $file as $key=>$value){
        if (is_file("./uploads/images/" . $value['src'])) {
            $imgs[] = '/uploads/images/' . str_replace('\\','/',$value['src']);
        }
    }
    return $imgs;
}

/**
 * 获取附件
 * @param $id
 * @param $html bool 是否返回HTML
 * @param $prefix bool 是否返回前缀
 * @return string
 */
function get_file($id, $html = false, $prefix = true){
    $model = new \app\common\model\common\File();
    $file = $model->find($id);
    if( !$file ){
        if( $html ){
            return '<i class="fa fa-file"></i><p>暂无文件</p>';
        }else{
            return false;
        }
    }
    if( !$html ){
        $pre = '';
        if( $prefix ){
            if( in_array( $file['ext'], explode(',', config('static.extension')['image'] )) ){
                $pre =  request()->root().'/uploads/images/';
            }else{
                $pre =  request()->root().'/uploads/files/';
            }
        }
        return $pre.str_replace('\\','/',$file['src']);
    }
    if( in_array( $file['ext'], explode(',', config('static.extension')['image'] )) ){
        $html = '<img src="'.request()->root().'/uploads/images/'.$file['src'].'" alt="'.$file['title'].'"> <p>'.$file['title'].'</p>';
    }else{
        switch($file['ext']){
            case 'docx':
            case 'doc':
                $_c = '-word-o';
                break;
            case 'xlsx':
            case 'xls':
                $_c = '-excel-o';
                break;
            case 'zip':
            case 'rar':
            case '7z':
                $_c = '-zip-o';
                break;
            case 'pdf':
                $_c = '-pdf-o';
                break;
            case 'ppt':
                $_c = '-powerpoint-o';
                break;
            case 'mp3':
            case 'amr':
                $_c = '-sound-o';
                break;
            case 'mp4':
            case 'flv':
            case 'avi':
                $_c = '-movie-o';
                break;
            default:
                $_c = '';
        }
        $html = '<i class="fa fa-file'.$_c.'"></i><p>'.$file['title'] .'</p>';
    }
    return $html;
}

/**
 * 批量获取附件
 * @param $id
 * @param $html bool 是否返回HTML
 * @param $prefix bool 是否返回前缀
 * @return string
 */
function get_files($id, $html = false, $prefix = true){
    $model = new \app\common\model\common\File();
    $file = $model->find($id);
    if( !$file ){
        if( $html ){
            return '<i class="fa fa-file"></i><p>暂无文件</p>';
        }else{
            return false;
        }
    }
    if( !$html ){
        $pre = '';
        if( $prefix ){
            if( in_array( $file['ext'], explode(',', config('static.extension')['image'] )) ){
                $pre =  request()->root().'/uploads/images/';
            }else{
                $pre =  request()->root().'/uploads/files/';
            }
        }
        return $pre.str_replace('\\','/',$file['src']);
    }

    switch($file['ext']){
        case 'jpg':
        case 'png':
        case 'gif':
            $_c = '-picture-o';
            break;
        case 'docx':
        case 'doc':
            $_c = '-word-o';
            break;
        case 'xlsx':
        case 'xls':
            $_c = '-excel-o';
            break;
        case 'zip':
        case 'rar':
        case '7z':
            $_c = '-zip-o';
            break;
        case 'pdf':
            $_c = '-pdf-o';
            break;
        case 'ppt':
            $_c = '-powerpoint-o';
            break;
        case 'mp3':
        case 'amr':
            $_c = '-sound-o';
            break;
        case 'mp4':
        case 'flv':
        case 'avi':
            $_c = '-movie-o';
            break;
        default:
            $_c = '';
    }
    $html = '<i class="fa-size fa fa-file'.$_c.'"></i><p>'.$file['title'] .'</p>';
    return $html;
}


/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = '0') {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 判断是否为中文
 * @param $str
 * @return bool
 */
function is_chinese($str){
    if(!preg_match("[^\x80-\xff]","$str")){
        return true;
    }else{
        return false;
    }
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    $str = str_replace('&nbsp;', '', $str);
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix && mb_strlen($str , $charset) > $length ? $slice.'...' : $slice;
}



/**
 * 4.1 返回管理员信息
 * @param string $field
 * @return mixed
 */
function get_admin_info($field = ''){
    $admin_id = is_login();
    $model = new \app\common\model\common\User();
    if( !$field ){
        $info = $model->find($admin_id);
    }else {
        $info = $model->where('id' ,$admin_id)->value($field);
    }
    return $info;
}

/**
 * 4.2 查询管理员是否属于某个组
 * @param $group
 * @param string $id
 * @return mixed
 */
function get_admin_group($group_id, $id = ''){
    if( !$id ){
        $id = is_login();
    }
    $admin = \app\common\model\common\User::get($id);
    $exist = $admin->auth()->where('role_id', $group_id)->count();
    return $exist;
}

/**
 * 数据库操作，传入数组自动判断更新或者插入数据，并根据影响的表将对应Module进行日志记录
 * @param $model_name string|object 模型名称 [ 模块/模型 ]
 * @param $data array 传入数据
 * @param string $validate_name string 验证器名称 [ 模块/验证器 ]
 * @param string $scene string 验证场景
 * @param string $pk string 主键字段名
 */
if( !function_exists('O') ) {
    function O($model_name, $data, $url = '',$validate_name = '', $scene = true, $pk = 'id')
    {
        $model = self_class_exist('\\app'.'\\'. $model_name );
        $validate = self_class_exist(str_replace('model','validate','\\app'.'\\'. $model_name  ));

        if( !$model ){
            return error('模型不存在');
        }
        if( $validate ) {
            $check = $validate->scene($scene)->check($data);
            if (!$check) {
                return error($validate->getError());
            }
        }
        $map = [];
        if( isset($data[$pk]) && $data[$pk] ){
            $map[$pk] = $data[$pk];
        }
        $res = $model->allowField(true)->save($data, $map);
        if( $res ){
            if( isset($data[$pk]) && $data[$pk] ){
                $id = $data[$pk];
                return success(lang('update success'),['id'=>$id],$url);
            }else{
                $id = $model->id;
                return success(lang('create success'),['id'=>$id], $url);
            }
        }
        return error(lang('error'));
    }
}

/**
 * 判断类是否存在，存在则返回该类
 * @param $class
 * @return bool
 */
function self_class_exist($class)
{
    if (class_exists($class)) {
        return new $class;
    } else {
        return false;
    }
}

/**
 * 获取配置子级数据
 * @param $key
 * @param $name
 * @return mixed
 */
function get_config($key, $name){
    if(isset(config($name)[$key]))
        return config($name)[$key];
    return $key;
}

/**
 * 获取数据配置的键值
 * @param $val
 * @param $name
 * @return bool|int|string
 */
function get_config_val($val, $name){
    $config = config($name);
    foreach ($config as $key => $value) {
        if( $value == $val ){
            return $key;
        }
    }
    return false;
}

/**
 * 配置字符串生成
 * @param $var
 * @param int $level
 * @return string
 */
function my_var_export($var, $level = 1){
    $str = '['.PHP_EOL;
    $indent = '';
    $space = '  ';
    for($i = 0 ; $i < $level ; $i++){
        $indent .= $space;
    }
    foreach ($var as $key => $value) {
        if(is_array($value)){
            $level++;
            $_s = my_var_export($value, $level);
            $str .= "$indent\"$key\"" . '=>' . $_s .','. PHP_EOL;
        }else {
            $str .= "$indent\"$key\"" . '=>"' . $value . '",' . PHP_EOL;
        }
        $level = 1 ;
    }
    return $str.$indent.']' ;
}


/**
 * 根据最底层id复原各级联动数组
 * @param string $table 数据来源表
 * @param int $value 底层id
 * @param string $pid 父键
 * @param array $result 数据集
 * @param bool $first 是否首次调用递归
 * @return array
 */
function rollback_linkage($table, $value, $pid = 'pid', $result = [], $first = true){
    $cur = \think\Db::table($table)->find($value);
    $data = \think\Db::table($table)->where(['pid' => $cur[$pid]])->select();
    array_unshift($data,['id'=>'','title'=>'无']);
    if( $data && $cur ){
        $last = \think\Db::table($table)->where(['id' => $cur[$pid]])->find();
        foreach ($data as $key => $v) {
            if( $v['id'] == $cur['id'] ){
                $data[$key]['selected'] = true;
            }
        }
        if( $last ) {
            $result = array_merge($result,rollback_linkage($table, $last['id'], $pid, $result, false));
        }
    }
    $result[$value] = $data;
    if( $first ){
        $data = \think\Db::table($table)->where(['pid' => $cur['id']])->select();
        array_unshift($data,['id'=>'','title'=>'无']);
        $result[$cur['pid']] = $data;
    }
    return $result;

}