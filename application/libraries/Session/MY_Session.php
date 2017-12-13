<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Session extends CI_Session
{
    function __construct()
    {
        parent::__construct();
    }

    public function sess_update()
    {
        if (!$this->input->is_ajax_request()) {
            return parent::sess_update();
        }
    }

    public function __get($property)
    {
        if (property_exists(get_instance(), $property)) {
            return $this->$property;
        }

        return parent::__get($property);
    }
}