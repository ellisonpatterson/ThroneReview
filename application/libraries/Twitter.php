<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter extends Abstractprovider
{
    public function loadExtensions()
    {
        parent::loadExtensions();

        $this->load->config('twitter');
    }

    public function startProviderInstance()
    {
        if ($this->isAuthRequest()) {
            $this->providerInstance = new TwitterOAuth(
                $this->config->item('consumer_key'),
                $this->config->item('consumer_secret'),
                $this->session->oauth_token,
                $this->session->oauth_token_secret
            );
        } else {
            $this->providerInstance = new TwitterOAuth(
                $this->config->item('consumer_key'),
                $this->config->item('consumer_secret')
            );
        }
    }

    public function getRedirectUrl()
    {
        $request = $this->request('oauth', 'oauth/request_token', array('oauth_callback' => current_url()));
        $this->session->set_userdata(array(
            'oauth_token' => $request['oauth_token'],
            'oauth_token_secret' => $request['oauth_token_secret']
        ));

        return $this->request('url', 'oauth/authorize', array('oauth_token' => $request['oauth_token']));
    }

    public function isAuthRequest()
    {
        if ($this->input->get('oauth_token') && $this->input->get('oauth_verifier')) {
            return true;
        }

        return false;
    }

    public function checkIfValidToken()
    {
        if (empty($this->session->token)) {
            return false;
        }

        if (!empty($this->input->get('oauth_token')) && $this->session->oauth_token !== $this->input->get('oauth_token')) {
            return false;
        }

        return true;
    }

    public function authenticate()
    {
        if ($this->checkIfValidToken()) {
            return true;
        }

        $accessToken = $this->request('oauth', 'oauth/access_token', array('oauth_verifier' => $this->input->get('oauth_verifier')));
        if (empty($accessToken)) {
            return false;
        }

        $accessToken['external_id'] = $accessToken['user_id'];
        $accessToken['name'] = $accessToken['screen_name'];
        unset($accessToken['user_id'], $accessToken['screen_name']);

        $this->session->set_userdata($accessToken);

        return $accessToken;
    }

    public function getUserInfo()
    {
        return array(
            'external_id' => $this->session->external_id,
            'name' => $this->session->name,
        );
    }

    public function request($method, $endpoint, array $params = array())
    {
        try {
            return $this->providerInstance->{strtolower($method)}($endpoint, $params);
        } catch(TwitterOAuthException $e) {
            log_message('error', '[Twitter OAuth] Code: ' . $e->getCode() . ' | Message: ' . $e->getMessage());
        }

        return false;
    }
}