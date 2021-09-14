<?php

class InitPlugin extends Migration {

    public function up() {
        DBManager::get()->exec("
            ALTER TABLE `brainstorms`
            ADD COLUMN `start` int(11) DEFAULT NULL AFTER `text`
        ");
    }

    public function down() {
        DBManager::get()->exec("
            ALTER TABLE `brainstorms`
            DROP COLUMN `start`
        ");
    }

}
