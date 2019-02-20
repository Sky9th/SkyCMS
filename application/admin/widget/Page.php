<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/26
 * Time: 13:27
 */

namespace app\admin\widget;

use think\Controller;

class Page extends Controller{

    /**
     * 构造数据展示页面
     * @param array $fields 列表所展示字段
     * @param object $logic 数据逻辑层
     * @param array $config 页面配置
     * @param array $control 数据操作
     * @param string $position 表单类型
     * @return mixed
     */
    public function make($fields, $logic, $config = [], $control = [], $position = 'top' , $js = '', $css = ''){
        $model = '';
        if( isset($logic->_model) ) {
            $model = explode('\\',get_class($logic->_model));
            $model = end($model);
        }
        $default_config = [
            'quick' => '',
            'search' => '',
            'status' => '',
            'thead' => '',
            'tbody' => '',
            'page' => '',
        ];
        $config = array_merge($default_config,$config);
        $tool = new Tool();
        if( $control !== false ){
            if( !$control ) {
                $control = [
                    'add',
                    'edit',
                    'delete',
                    'enable',
                    'disabled'
                ];
            }
            $config['quick'] = $tool->btn_quick($control, $model, $position);
        }

        if( $config['search'] !== false ){
            $config['search'] = $tool->filter($config['search'], $config['status']);
        }

        $config['thead'] = $this->thead($fields);
        list(
            $config['tbody'],
            $config['page']
            ) = $this->tbody($logic, $fields, $control, $position);
        $config['_js_'] = $js;
        $config['_css_'] = $css;
        return $this->fetch('admin@page/index',$config);
    }


    /**
     * 构造列表页表头
     * @param $fields
     */
    public function thead($fields){
        $thead = '';
        $checkbox = $last = '';
        foreach ($fields as $key => $value) {
            if( !is_array($value) ){
                if( $value == 'checkbox' ) {
                    $checkbox = <<<html
              <th class="table__checkbox checkbox">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input checkbox-all" id="checkbox0" data-name="ids[]">
                  <label class="custom-control-label" for="checkbox0"></label>
                </div>
              </th>
html;
                }else{
                    $thead .= "<th>$key</th>";
                }
            }else {
                switch ($value['type']) {
                    case 'btn' :
                            $last = '<th>操作</th>';

                        break;
                    case 'text' :
                        $width = '';
                        if( isset($value['width']) ){
                            $width  = "width:{$value['width']}%";
                        }
                        $thead .= "<th style='{$width}'>$key</th>";
                        break;
                }
            }
        }

        $thead = "{$checkbox}{$thead}{$last}";
        return $thead;
    }

    /**
     * 构造数据展示页表格数据
     * @param $logic
     * @param $fields
     * @param $control
     * @param $position
     * @return array
     */
    public function tbody($logic, $fields, $control, $position){

        $model = '';
        if( isset($logic->_model) ) {
            $model = explode('\\',get_class($logic->_model));
            $model = end($model);
        }
        $btn = '';
        if( $control !== false ){
            $tool = new Tool();
            $btn = $tool->btn_edit($control, $model, $position);
        }

        $data = $logic->_list();
        $page = '';
        $rows = [];
        if( is_array($data) ){
            $rows = $data;
        }else{
            $class = get_class($data);
            if( $class == 'BootstrapPage\Bootstrap' ){
                $rows = $data;
                $page = $data->render();
            }else if( $class == 'think\model\Collection' ){
                $rows = $data;
            }
        }
        $sort = [];
        foreach ($fields as $key => $value) {
            if( isset($value['field']) ) {
                $sort[$key] = $value['field'];
            }else if( !is_array($value) && $value != 'checkbox' && $value != 'btn' ){
                $sort[$key] = $value;
            }
        }
        $td = $this->rows($rows, $sort, $fields, $btn);
        return [ $td, $page ];
    }

    /**
     * 生成数据列
     * @param $rows
     * @param $sort
     * @param $fields
     * @param $btn
     * @param int $level
     * @param string $ladder
     * @return string
     */


    private function rows($rows, $sort, $fields, $btn, $level = 0, $ladder = 'title'){
        $td = '';
        foreach ($rows as $key=>$value) {
            $td .= '<tr>';
            if( in_array('checkbox',$fields) ){
                $td .= <<<html
<td class="table__checkbox checkbox">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input checkbox-all" id="checkbox{$value['id']}"  value="{$value['id']}" name="ids[]" >
                  <label class="custom-control-label" for="checkbox{$value['id']}"></label>
                </div>
              </td>
html;
            }

            foreach ($sort as $k=>$v) {
                $front = '';
                if( $level > 0 && $v == $ladder ){
                    $last = '';
                    $keys = array_keys($rows);
                    $last_key = end($keys);
                    if( $key == $last_key ){
                        $last = ' ladder-front-last';
                    }
                    $front = '<i class="ladder-front ladder-front-'.$level.$last.'"></i>';
                }
                if(strstr($v, '.')){
                    $_s = explode('.', $v);
                    if(count($_s)>2){
                        $td .= <<<html
                <td>{$front}{$value[$_s[0]][$_s[1]][$_s[2]]}</td>
html;
                    }else{
                        $td .= <<<html
                <td>{$front}{$value[$_s[0]][$_s[1]]}</td>
html;
                    }
                }else{
                    if( isset($fields[$k]['callback']) ){
                        $obj = $fields[$k]['callback'];
                        $str = call_user_func(array($obj[0], $obj[1]),$value[$v]);

                        $td .= <<<html
                <td>{$front}{$str}</td>
html;
                    } else if( isset($fields[$k]['function']) ){
                        $fun = $fields[$k]['function'];
                        if( is_array($fun) ){
                            $function = $fun[0];
                            $fun[0] = $value[$v];
                            $param = $fun;
                            $str = call_user_func_array($function,$param);
                        }else{
                            $str = call_user_func($fun,$value[$v]);
                        }

                        $td .= <<<html
                <td>{$front}{$str}</td>
html;
                    }else{

                        $td .= <<<html
                <td>{$front}{$value[$v]}</td>
html;
                    }
                }
            }
            if( $btn ) {
                $td .= str_replace('__ID__', $value['id'], $btn);
            }
            $td .= '</tr>';

            if( isset($value['_child']) && !empty($value['_child']) ){
                $level++ ;
                $td .= $this->rows($value['_child'] ,$sort, $fields, $btn, $level);
                $level = 0;
            }
        }

        return $td;
    }

    /**
     * 生成数据表格
     * @param $fields
     * @param $data
     * @return string
     */
    public function dataTable($fields, $data = []){
        $thead = $this->thead($fields);
        $btn = '';
        foreach ($fields as $key => $value) {
            if( isset($value['type']) && $value['type'] == 'btn' && isset($value['btn']) ){
                $control = $value['btn'];
                $tool = new Tool();
                $btn = $tool->control($control, true, 'btn-xs');
                break;
            }
        }
        // $btn = '<td class="text-right">'.$btn.'</td>';

        $page = '';
        $rows = [];
        if( is_array($data) ){
            $rows = $data;
        }else{
            $class = get_class($data);
            if( $class == 'think\paginator\driver\Bootstrap2' ){

                $rows = $data;
                $page = $data->render();
            }else if( $class == 'think\model\Collection' ){
                $rows = $data;
            }
        }
        $sort = [];
        foreach ($fields as $key => $value) {
            if( isset($value['field']) ) {
                $sort[$key] = $value['field'];
            }
        }
        $tbody = $this->rows($rows, $sort, $fields, $btn);
        $content = "<div class='col-xs-12'><form action=\"\" id=\"form-work-table\" class=\"mb-3\">
                        <table class=\"table table-hover\">
                            <thead>
                             {$thead}
                            </thead>
                            <tbody>
                            {$tbody}
                            </tbody>
                        </table>
                    </form>
                    <div class=\"row\">
                        {$page}
                    </div></div>";
        return $content;
    }


}