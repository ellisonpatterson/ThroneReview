<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_User_Provider extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'user_id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'provider_id' => array(
                'type' => 'varchar',
                'constraint' => 10,
            ),
            'external_id' => array(
                'type' => 'varchar',
                'constraint' => 50,
            ),
            'extra' => array(
                'type' => 'bytea',
            ),
        ));

        $this->dbforge->add_field('CONSTRAINT "fk_user_provider_user_id" FOREIGN KEY ("user_id") REFERENCES "user" ("user_id")');

        $this->dbforge->add_key('user_id', true);
        $this->dbforge->create_table('user_provider');
    }

    public function down()
    {
        $this->dbforge->drop_table('user_provider');
    }
}