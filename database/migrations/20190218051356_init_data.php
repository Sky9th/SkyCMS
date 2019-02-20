<?php

use think\migration\Migrator;
use think\migration\db\Column;

class InitData extends Migrator
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
        $singleRow = '{ "id":1, "type":0, "minip_user_id":0, "wechat_user_id":0, "nickname":"", "realname":"", "phone":"", "username":"admin", "password":"e10adc3949ba59abbe56e057f20f883e", "seed":0, "mail":"", "remark":"", "last_login_session":"a58tf1vmeh0hv7tvr5dbi5e6nd", "last_login_time":1550201334, "create_time":0, "update_time":1550201334, "status":1 }';
        $insert = json_decode($singleRow,true);
        $table = $this->table('common_user');
        $table->insert($insert);
        $table->saveData();

        // inserting multiple rows
        $json = json_decode(file_get_contents( './database/data/common_area.json'), true);
        $area = $json['RECORDS'];
        $insert = [];
        foreach ($area as $item) {
            $insert[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'sort' => $item['sort'],
                'create_time' => time(),
                'update_time' => time(),
                'status' => 1
            ];
        }
        $this->table('common_area')->insert($insert)->save();

        // inserting multiple rows
        $json = json_decode(file_get_contents( './database/data/sys_module.json'), true);
        $modules = $json['RECORDS'];
        $this->table('sys_module')->insert($modules)->save();

        $role = [
            'id' => 1,
            'pid' => 0,
            'title' => '超级管理组',
            'rules' => 0,
            'create_time' => 0,
            'update_time' => 0
        ];
        $this->table('sys_role')->insert($role)->save();

        $role = [
            'user_id' => 1,
            'role_id' => 1
        ];
        $this->table('sys_auth')->insert($role)->save();

        $json = json_decode(file_get_contents( './database/data/sys_action.json'), true);
        $action = $json['RECORDS'];
        $this->table('sys_action')->insert($action)->save();


    }

    public function down(){
        $this->query('DELETE FROM common_user');
        $this->query('DELETE FROM common_area');
        $this->query('DELETE FROM sys_module');
        $this->query('DELETE FROM sys_role');
        $this->query('DELETE FROM sys_action');
    }
}
