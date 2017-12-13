<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Location extends CI_Migration
{
    public function up()
    {
        $this->db->query('CREATE EXTENSION IF NOT EXISTS cube;');
        $this->db->query('CREATE EXTENSION IF NOT EXISTS earthdistance;');

        $this->dbforge->add_field(array(
            'location_id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'place_id' => array(
                'type' => 'varchar',
                'constraint' => 60,
            ),
            'name' => array(
                'type' => 'varchar',
                'constraint' => 60,
            ),
            'address' => array(
                'type' => 'varchar',
                'constraint' => 255,
            ),
            'latitude' => array(
                'type' => 'decimal',
                'constraint' => '11,8'
            ),
            'longitude' => array(
                'type' => 'decimal',
                'constraint' => '11,8'
            )
        ));

        $this->dbforge->add_key('location_id', true);
        $this->dbforge->add_key(array('latitude', 'longitude'));

        $this->dbforge->create_table('location');

        $this->db->query('CREATE INDEX ON location USING gist (ll_to_earth(latitude, longitude));');
    }

    public function down()
    {
        $this->dbforge->drop_table('location');
    }
}