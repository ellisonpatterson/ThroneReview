<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Session extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'varchar',
                'constraint' => 128
            ),
            'ip_address' => array(
                'type' => 'varchar',
                'constraint' => 48
            ),
            'timestamp' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true,
            ),
            'data' => array(
                'type' => 'text',
                'default' => ''
            )
        ));

        $this->dbforge->add_key(array(
            'id',
            'ip_address'
        ), true);

        $this->dbforge->create_table('session');
    }

    public function down()
    {
        $this->dbforge->drop_table('session');
    }
}