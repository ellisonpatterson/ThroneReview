<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Facebook\Facebook as FB;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookBatchResponse;
use Facebook\Helpers\FacebookCanvasHelper;
use Facebook\Helpers\FacebookJavaScriptHelper;
use Facebook\Helpers\FacebookPageTabHelper;
use Facebook\Helpers\FacebookRedirectLoginHelper;

class Facebook extends Abstractprovider
{
    private $fbHelper;

    public function loadExtensions()
    {
        parent::loadExtensions();

        $this->load->config('facebook');
    }

    public function startProviderInstance()
    {
        $this->providerInstance = new FB([
            'app_id' => $this->config->item('facebook_app_id'),
            'app_secret' => $this->config->item('facebook_app_secret'),
            'default_graph_version' => 'v2.5'
        ]);

        $this->fbHelper = $this->providerInstance->getRedirectLoginHelper();

    }

    public function getRedirectUrl()
    {
        return $this->fbHelper->getLoginUrl(current_url(), $this->config->item('facebook_permissions'));
    }

    public function isAuthRequest()
    {
        if ($this->input->get('code') && $this->input->get('state')) {
            return true;
        }

        return false;
    }

    public function checkIfValidToken()
    {
        $sessionData = $this->session->userdata();

        if (!empty($sessionData['token']) && $sessionData['token_expiration'] > (time() + 30) || !empty($sessionData['token']) && !$sessionData['token_expiration']) {
            $this->providerInstance->setDefaultAccessToken($sessionData['token']);
            return true;
        }

        return false;
    }

    public function authenticate()
    {
        if ($this->checkIfValidToken()) {
            return $this->session->userdata();
        }

        try {
            $accessToken = $this->fbHelper->getAccessToken();
        } catch (FacebookSDKException $e) {
            log_message('error', '[Facebook Open Graph SDK] Code: ' . $e->getCode() . ' | Message: ' . $e->getMessage());
            return false;
        }

        if (empty($accessToken)) {
            log_message('error', '[Facebook Open Graph SDK] No access token found.');
            return null;
        }

        if (!$accessToken->isLongLived()) {
            try {
                $accessToken = $this->providerInstance->getOAuth2Client()->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                log_message('error', '[Facebook Open Graph SDK] Code: ' . $e->getCode() . ' | Message: ' . $e->getMessage());
                return null;
            }
        }

        $this->providerInstance->setDefaultAccessToken($accessToken->getValue());

        $authData = array(
            'token' => $accessToken->getValue(),
            'token_expiration' => $accessToken->getExpiresAt()->getTimestamp(),
            'provider_id' => $this->getProviderId()
        );

        $this->session->set_userdata($authData);

        return $authData;
    }

    public function getUserInfo()
    {
        $userInfo = $this->request('get', '/me?fields=id,name,email');

        return array(
            'external_id' => $userInfo['id'],
            'name' => $userInfo['name'],
            'email' => $userInfo['email']
        );
    }

    public function request($method, $endpoint, array $params = array(), $token = null)
    {
        try {
            $response = $this->providerInstance->{strtolower($method)}($endpoint, $params, $token);
            return $response->getDecodedBody();
        } catch(FacebookResponseException $e) {
            log_message('error', '[Facebook Open Graph SDK] Code: ' . $e->getCode() . ' | Message: ' . $e->getMessage());
        } catch (FacebookSDKException $e) {
            log_message('error', '[Facebook Open Graph SDK] Code: ' . $e->getCode() . ' | Message: ' . $e->getMessage());
        }

        return false;
    }
}