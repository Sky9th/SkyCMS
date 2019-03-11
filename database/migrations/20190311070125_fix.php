<?php

use think\migration\Migrator;

class Fix extends Migrator
{
    /**
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */

    public function up(){

        $table = $this->table('common_files');
        $table->rename('common_file');

        $modules = $this->table('sys_module');
        $modules->changeColumn('name', 'string', ['limit' => 100])
            ->save();
    }
}
