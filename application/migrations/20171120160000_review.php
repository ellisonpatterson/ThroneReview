<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Review extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'review_id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'location_id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true
            ),
            'user_id' => array(
                'type' => 'int',
                'constraint' => 10,
                'unsigned' => true
            ),
            'rating' => array(
                'type' => 'int',
                'constraint' => 1,
                'unsigned' => true
            ),
            'review' => array(
                'type' => 'text',
            ),
            'added' => array(
                'type' => 'timestamp',
            ),
            'updated' => array(
                'type' => 'timestamp',
                'null' => true
            ),
        ));

        $this->dbforge->add_field('CONSTRAINT "fk_review_location_id" FOREIGN KEY ("location_id") REFERENCES "location" ("location_id")');
        $this->dbforge->add_field('CONSTRAINT "fk_review_user_id" FOREIGN KEY ("user_id") REFERENCES "user" ("user_id")');
        $this->dbforge->add_field('CONSTRAINT check_rating CHECK (rating >= 0 AND rating <= 5)');
        $this->dbforge->add_field('UNIQUE ("location_id", "review_id")');

        $this->dbforge->add_key('review_id', true);
        $this->dbforge->add_key('location_id');
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('review');
    }

    public function down()
    {
        $this->dbforge->drop_table('review');
    }
}