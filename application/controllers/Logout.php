<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->user->isLoggedIn()) {
            redirect('/login/');
        }
    }

    public function index()
    {
        if ($this->input->method() == 'post') {
            $userData = $this->session->userdata();
            foreach ($userData as $key => $value) {
                if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
                    $this->session->unset_userdata($key);
                }
            }

            $this->session->sess_destroy();
        }

        redirect('/');
    }
}
