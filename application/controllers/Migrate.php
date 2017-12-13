<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            exit('This can only be used via CLI: php index.php migrate');
        }

        $this->load->library('migration');
    }

    public function index()
    {
        if ($this->migration->current() === FALSE) {
            show_error($this->migration->error_string());
        }
    }
}