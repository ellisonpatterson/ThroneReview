<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->user->isLoggedIn()) {
            redirect('/');
        }
    }

    public function index()
    {
		$this->load->view(wrapper_view(), array(
            'content' => $this->load->view('login/content', '', true),
        ));
    }

	public function providers($providerId = false)
	{
        if ($providerId && $this->providers->loadProviderLibrary($providerId)) {
            if ($this->provider->isAuthRequest()) {
                if ($authDetails = $this->provider->authenticate()) {
                    $this->provider->setupUser($authDetails);
                }

                redirect('/');
            }

            redirect($this->provider->getRedirectUrl());
        }
	}
}
