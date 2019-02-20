<?php
/**
 * 此类为资源控制器基础类，所有有关数据插入，更新，删除以及禁用启用应继承此类，通过下述固定的资源控制器方法进行数据操作。
 * 通过以下方法操作能够与日志类进行关联
 * 如果有额外的操作方法，需在模块能另行添加相应的路径，并手动添加相应的日志
 */

namespace app\admin\controller;
use app\admin\widget\Form;
use app\admin\widget\Page;
use app\admin\widget\Tool;

class Resource extends Common
{

    //控制器绑定模型层
    protected $_model ;
    //控制器绑定逻辑层
    protected $_logic ;
    //构造表格参数
    protected $_fields ;
    //构造表单参数
    protected $_forms ;
    //额外参数
    protected $_config  = [];
    //引入js
    protected $_js ;
    //引入css
    protected $_css ;
    //表单类型
    protected $_position = 'right' ;
    //按钮设置
    protected $_btn = [] ;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $page = new Page();
        $content = $page->make($this->_fields, $this->_logic, $this->_config, $this->_btn, $this->_position, $this->_js, $this->_css);
        return $content;
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $form = new Form();
        $content = $form->make($this->_forms);
        if( $this->_position == 'page' ){
            return view('admin@page/form', ['content' => $content, '_js_' => $this->_js, '_css_' => $this->_css ] );
        }else {
            return $content;
        }
    }

    /**
     * 保存新建的资源
     *
     * @return
     */
    public function save()
    {
        return $this->_logic->_save();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $this->_model->get($id);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = $this->_model->find($id)->getData();
        $form = new Form();
        if( $this->_position == 'page' ){
            $this->_forms['id'] =[
                'title' => '',
                'type' => 'input',
                'config' => [
                    'value' => $id,
                    'hidden' => true
                ]
            ];
            $content = $form->make($this->_forms, $data);
            $tool = new Tool();
            $breadcrumb = $tool->breadcrumb();
            return view('admin@page/form', ['content' => $content, 'breadcrumb' => $breadcrumb , '_js_' => $this->_js, '_css_' => $this->_css ] );
        }else {
            $content = $form->make($this->_forms, $data);

            return $content;
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update($id)
    {
        return $this->_logic->_update($id);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id = '')
    {
        if( $id ){
            $ids = $id;
        }else{
            $ids = input('delete.');
            $ids = $ids['ids'];
        }
        return $this->_logic->_delete($ids);
    }

    /**
     * 启用或禁用指定资源
     *
     * @param $status
     * @return mixed
     */
    public function status($status, $id = ''){
        if( $id ){
            $ids = $id;
        }else{
            $ids = input('put.');
            $ids = $ids['ids'];
        }
        return $this->_logic->_status($ids, $status);
    }

    /**
     * @param $id
     * @return \think\Response
     */
    public function detail($id){
        return $this->edit($id);
    }

}