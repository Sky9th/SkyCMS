<?php
/**
 * Author: Sky9th
 * Date: 2017/4/2
 * Time: 17:11
 */
namespace app\common\model\sys;

use app\common\model\Sys;

class Logs extends Sys {

    protected $table = 'sys_logs';

    protected $log_data ;
    protected $log ;
    protected $action ;

    static $format =
        'save:{:get_admin_info("username")}新增了({$id}){$title}{__title__}'.PHP_EOL.
         'update:{:get_admin_info("username")}更新了({$id}){$title}{__title__}'.PHP_EOL.
         'delete:{:get_admin_info("username")}删除了{[{$id}{$title}{__title__}]}'.PHP_EOL.
         'status:{:get_admin_info("username")}{$status|config("static.status_name")}了{[{$id}{$title}{__title__}]}';

    public function admin(){
        return $this->belongsTo('app\common\model\common\User');
    }

    public function action(){
        return $this->belongsTo('app\common\model\sys\Action');
    }

    public function module(){
        return $this->belongsTo('app\common\model\sys\module');
    }

    public function getStatusAttr($value)
    {
        $status = config('static.status');
        return $status[$value];
    }

    public function actionLog($id, $name, $model, $delay = false){
        $_action = new Action();
        $action = $_action->where('name', $name)->find();
        if( $action ) {
            $this->log = $action['log'];
            if ($model) {
                if (is_array($model)) {
                    $this->log_data = $model;
                } else if (is_string($model)) {
                    $this->log_data = Db::table($model)->where(['id' => ['in', $id]])->select();
                } else if( is_object($model) ){
                    $this->log_data = $model->where(['id' => ['in', $id]])->select();
                }
                $remark = $this->parseLog($this->log);
            } else {
                $this->log_data = [];
                $remark = $this->parseLog($this->log);
            }
        }else{
            $action['id'] = '0';
            $remark = '日志记录失败';
        }
        $this->data([
            'admin_id' => is_login(),
            'action_id' => $action['id'],
            'action_ip' => get_client_ip(),
            'model' => $name,
            'record_id' => $id,
            'remark' => $remark,
            'status' => 1,
            'create_time' => time()
        ]);
        if( $delay ){
            return $this;
        }
        $this->save();
        return true;
    }

    public function resourceLog($id, $method, $model, $delay = false){
        if( is_object($model) ){
            $table = $model->getTable();
        }else{
            $table = $model;
        }

        $action = db('sys_module')->where('table' , $table )->find();
        if( !$action ){
            return true;
        }
        if( !$action['log'] ){
            $action['log'] = self::$format;
        }
        $formats = explode(PHP_EOL, $action['log']);
        $format = [];
        foreach ($formats as $key => $value) {
            list($k, $v) = explode(':', $value, 2);
            $format[$k] = $v;
        }
        if(is_array($id)){
            $id = implode(',', $id);
        }
        $this->log = $format[$method];
        $this->log_data = db($table)->where( ['id' => ['in', $id ]] )->select();
        $this->action = $action;
        $remark = $this->parseLog($this->log);
        $this->data([
            'admin_id' => is_login(),
            'module_id' => $action['id'],
            'action_ip' => get_client_ip(),
            'model' => $table,
            'record_id' => $id,
            'remark' => $remark,
            'status' => 1,
            'create_time' => time()
        ]);
        if( $delay ){
            return $this;
        }
        $this->save();
    }

    /**
     * 转义日志模板
     * @param $log
     * @param $data
     */
    private function parseLog($log){

        preg_match_all('/\{\[(.*?)\]\}/', $log, $volist);
        if( count($volist[0]) > 0 ){
            $volist = $this->parseVolist($volist);
            $log = strtr($log, $volist);
        }

        $var = $this->parseVar();
        $function = $this->parseFunction();

        $log = strtr($log, $var);
        $log = strtr($log, $function);

        return $log;

    }

    /**
     * @param $var
     * @return array
     */
    private function parseVar($log = '', $data = []){
        if( !$log ){
            $log = $this->log;
        }
        preg_match_all('/\{\$(.*?)\}/', $log, $var);
        preg_match_all('/\{__(.*?)__\}/', $log, $act);
        $res = [];
        foreach ($var[0] as $key => $value ) {
            $res[ $value ] = $this->getLogData($var[1][$key], $data)   ;
        }
        foreach ($act[0] as $key => $value ) {
            $res[ $value ] = $this->getActionData($var[1][$key])   ;
        }
        return $res;
    }

    /**
     * @param $var
     * @return array
     */
    private function parseFunction($log = '' , $data = []){
        if( !$log ){
            $log = $this->log;
        }
        if( !$data ){
            $data = $this->log_data;
        }
        if( $data ){
            $key =  key($data) ;
            if( $key === 0 ){
                $data = $data[0];
            }
        }
//            eval("\${$key}='$value';");
        preg_match_all('/\{:(.*?)\}/', $log, $function);
        $res = [];
        foreach ($function[0] as $key => $value ) {
            preg_match_all('/\{\:((.*?)\((.*?)\))\}/',$value, $fun);
            $fun_name = $fun[2][0];
            $fun_method = $fun[1][0];
            $fun_param = $fun[3][0];
            $param = explode(',', $fun_param);
            foreach ($param as $k => $v) {
                if(  strstr($v,'$') ){
                    $name = str_replace('$','', $v);
                    eval("{$v}='$data[$name]';");
                }
            }
            if( function_exists($fun_name) )
                eval('$res[ $value ] = '.$fun_method.';');
        }
        return $res;
    }

    /**
     * @param $var
     * @return array
     */
    private function parseVolist($volist){
        $replace = [];
        foreach ($volist[1] as $key => $value ) {
            $res = '';
            foreach ($this->log_data as $item) {
                $var = $this->parseVar($value,$item);
                $function = $this->parseFunction($value);
                $log = strtr($value, $var);
                $log = strtr($log, $function);
                $res .= "[$log]" ;
            }
            $replace[ $volist[0][$key] ] = "$res";
        }
        return $replace;
    }


    private function getLogData($name, $data = []){
        if( !$data ){
            if( $this->log_data ) {
                if( is_array( $this->log_data ) ){
                    $data = current($this->log_data);
                }else if( is_object($this->log_data) ){
                    $data = current($this->log_data->toArray());
                }else{
                    $data = $this->log_data;
                }
            }
        }
        if( isset( $data[$name] ) )
            return $data[$name];
        return '';
    }

    private function getActionData($name){
        if( isset( $this->action[$name] ) )
            return $this->action[$name];
        return '';
    }

}
