<?php

use think\migration\Migrator;

class Init extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $this->sys_module();
        $this->sys_action();
        $this->sys_auth();
        $this->sys_logs();
        $this->sys_role();
        $this->common_area();
        $this->common_files();
        $this->common_sms();
        $this->common_user();
    }

    /**
     * 模块表
     */
    public function sys_module(){
        $table = $this->table('sys_module',['engine'=>'MyISAM']);
        $table
            ->addColumn('pid', 'integer', array('limit' => 11, 'default'=> 0,'comment'=>'模块父类'))
            ->addColumn('module', 'string', array('limit' => 20,'comment'=>'所属模块'))
            ->addColumn('type', 'integer', array('limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'comment'=>'模块类型'))
            ->addColumn('title', 'string', array('limit' => 20, 'comment'=>'模块名称'))
            ->addColumn('name', 'string', array('limit' => 20, 'comment'=>'路由标识'))
            ->addColumn('src', 'string', array('limit' => 255, 'comment'=>'真实路径','null'=>true))
            ->addColumn('param', 'text', array('comment'=>'参数','null'=>true))
            ->addColumn('icon', 'string', array('limit' => 20, 'comment'=>'模块图标','null'=>true))
            ->addColumn('color', 'string', array('limit' => 20, 'comment'=>'模块色彩','null'=>true))
            ->addColumn('rule', 'text', array( 'comment'=>'路由规则','null'=>true))
            ->addColumn('condition', 'text', array('comment'=>'验证条件','null'=>true))
            ->addColumn('table', 'string', array('limit' =>255, 'comment'=>'资源表','null'=>true))
            ->addColumn('resource', 'string', array('limit' =>255, 'comment'=>'资源规则','null'=>true))
            ->addColumn('log', 'text', array( 'comment'=>'日志格式','null'=>true))
            ->addColumn('intro', 'text', array( 'comment'=>'简介','null'=>true))
            ->addColumn('visible', 'integer', array('limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'comment'=>'是否显示在导航栏中'))
            ->addColumn('route', 'integer', array('limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'default'=> 1, 'comment'=>'是否启用路由'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('sort', 'integer', array('limit' => 11, 'default'=> 0, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit' => 4, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 行为节点表
     */
    public function sys_action(){
        $table = $this->table('sys_action',['engine'=>'MyISAM']);
        $table
            ->addColumn('title', 'string', array('limit' => 20, 'comment'=>'行为名称'))
            ->addColumn('name', 'string', array('limit' => 20, 'comment'=>'路由标识'))
            ->addColumn('condition', 'text', array('comment'=>'验证条件','null'=>true))
            ->addColumn('log', 'text', array('comment'=>'日志格式'))
            ->addColumn('intro', 'text', array('comment'=>'简介','null'=>true))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit' => 4, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 用户权限组关联表
     */
    public function sys_auth(){
        $table = $this->table('sys_auth',['engine'=>'MyISAM']);
        $table
            ->addColumn('user_id', 'integer', array('limit' => 11, 'comment'=>'用户ID'))
            ->addColumn('role_id', 'integer', array('limit' => 11, 'comment'=>'角色组ID'))
            ->create();
    }

    /**
     * 系统日志表
     */
    public function sys_logs(){
        $table = $this->table('sys_logs',['engine'=>'MyISAM']);
        $table
            ->addColumn('action_id', 'integer', array('limit' => 11, 'comment'=>'行为ID'))
            ->addColumn('module_id', 'integer', array('limit' => 11, 'comment'=>'模块ID'))
            ->addColumn('user_id', 'integer', array('limit' => 11, 'comment'=>'用户ID'))
            ->addColumn('record_id', 'string', array('limit' => 50, 'comment'=>'数据自增ID'))
            ->addColumn('action_ip', 'string', array('limit' => 50, 'comment'=>'IP地址'))
            ->addColumn('model', 'string', array('limit' => 50, 'comment'=>'行为模型'))
            ->addColumn('remark', 'text', array('comment'=>'日志备注'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit' => 4, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 角色组表
     */
    public function sys_role(){
        $table = $this->table('sys_role',['engine'=>'MyISAM']);
        $table
            ->addColumn('pid', 'integer', array('limit' => 11, 'comment'=>'父角色组'))
            ->addColumn('title', 'string', array('limit' => 50, 'comment'=>'角色组名称'))
            ->addColumn('rules', 'text', array('comment'=>'权限节点'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit' => 4, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 地区表
     */
    public function common_area(){
        $table = $this->table('common_area',['engine'=>'MyISAM']);
        $table
            ->addColumn('pid', 'integer', array('limit' => 11, 'comment'=>'父地区'))
            ->addColumn('title', 'string', array('limit' => 50, 'comment'=>'地区名称'))
            ->addColumn('sort', 'integer', array('default'=>0 ,'comment'=>'排序'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit' => 4, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 文件表
     */
    public function common_files(){
        $table = $this->table('common_files',['engine'=>'MyISAM']);
        $table
            ->addColumn('user_id', 'integer', array('limit' => 11, 'comment'=>'用户ID'))
            ->addColumn('pid', 'integer', array('limit' => 11, 'comment'=>'文件夹ID'))
            ->addColumn('title', 'string', array('limit' => 50, 'comment'=>'文件名'))
            ->addColumn('src', 'string', array('limit' => 255, 'comment'=>'文件路径'))
            ->addColumn('ext', 'string', array('limit' => 20, 'comment'=>'文件类型'))
            ->addColumn('description', 'string', array('limit' => 255, 'comment'=>'文件描述'))
            ->addColumn('media_id', 'string', array('limit' => 255, 'comment'=>'微信媒体ID'))
            ->addColumn('url', 'string', array('limit' => 255, 'comment'=>'文件url'))
            ->addColumn('folder', 'integer', array('limit'=>1, 'default'=>0, 'comment'=>'是否为文件夹'))
            ->addColumn('sort', 'integer', array('default'=>0 ,'comment'=>'排序'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit'=>1, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 短信记录表
     */
    public function common_sms(){
        $table = $this->table('common_sms',['engine'=>'MyISAM']);
        $table
            ->addColumn('phone', 'string', array('limit' => 20, 'comment'=>'手机号码'))
            ->addColumn('tpl', 'string', array('limit' => 20, 'comment'=>'短信模板ID'))
            ->addColumn('content', 'text', array('comment'=>'短信内容'))
            ->addColumn('code', 'string', array('limit'=>20, 'comment'=>'返回报文编码'))
            ->addColumn('msg', 'text', array('comment'=>'返回报文编码内容'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit'=>1, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    /**
     * 短信记录表
     */
    public function common_user(){
        $table = $this->table('common_user',['engine'=>'MyISAM']);
        $table
            ->addColumn('type', 'integer', array('limit'=>1, 'comment'=>'用户类型'))
            ->addColumn('minip_user_id', 'integer', array('limit' => 11, 'comment'=>'小程序用户id'))
            ->addColumn('wechat_user_id', 'integer', array('limit' => 11, 'comment'=>'微信用户id'))
            ->addColumn('nickname', 'string', array('limit'=>50, 'comment'=>'昵称'))
            ->addColumn('realname', 'string', array('limit'=>20, 'comment'=>'姓名'))
            ->addColumn('phone', 'string', array('limit'=>255, 'comment'=>'手机号码'))
            ->addColumn('mail', 'string', array('limit'=>255, 'comment'=>'电子邮箱'))
            ->addColumn('username', 'string', array('limit'=>20, 'comment'=>'用户名'))
            ->addColumn('password', 'char', array('limit'=>32, 'comment'=>'密码'))
            ->addColumn('seed', 'integer', array('limit'=>4,'comment'=>'随机种子'))
            ->addColumn('remark', 'string', array('limit'=>255, 'comment'=>'备注'))
            ->addColumn('last_login_session', 'string', array('limit'=>255, 'comment'=>'最后登陆SESSION'))
            ->addColumn('last_login_time', 'integer', array('limit'=>11, 'comment'=>'最后登陆时间'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'comment'=>''))
            ->addColumn('status', 'integer', array('limit'=>1, 'default'=> 1, 'comment'=>''))
            ->create();
    }

    public function down(){
        $this->table('sys_module')->drop();
        $this->table('sys_action')->drop();
        $this->table('sys_auth')->drop();
        $this->table('sys_logs')->drop();
        $this->table('sys_role')->drop();
        $this->table('common_area')->drop();
        $this->table('common_files')->drop();
        $this->table('common_sms')->drop();
        $this->table('common_user')->drop();
    }
}
