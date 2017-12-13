<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Abstractprovider
{
    private $providerInstance;

    public function __construct()
    {
        $this->loadExtensions();
        $this->providerInstance = $this->startProviderInstance();
    }

    public function loadExtensions()
    {
        $this->load->helper('url');
    }

    public function getProviderInstance()
    {
        return $this->providerInstance;
    }

    abstract public function startProviderInstance();
    abstract public function getRedirectUrl();
    abstract public function isAuthRequest();

    public function getProviderId()
    {
        return strtolower(get_class($this));
    }

    public function setupUser(array $authDetails)
    {
        $providerUserInfo = $this->getUserInfo();

        $userInfo = array(
            'provider_id' => $this->getProviderId(),
            'external_id' => $providerUserInfo['external_id'],
            'extra' => serialize($authDetails)
        );

        $this->user->setup($userInfo);

        if (!$this->user['user_id']) {
            $this->db->insert('user', array(
                'name' => $providerUserInfo['name']
            ));

            $userId = $this->db->insert_id();
            $userInfo['user_id'] = $userId;

            $this->db->insert('user_provider', $userInfo);
        } else {
            $userId = $this->user['user_id'];

            $this->db->where('user_id', $userId);
            $this->db->update('user_provider', $userInfo);
        }

        $this->session->set_userdata('user_id', $userId);
    }

    public function getUserAvatar()
    {
        return 'https://avatars.io/' . $this->getProviderId() . '/' . $this->user->external_id;
    }

    abstract public function checkIfValidToken();
    abstract public function authenticate();

    abstract public function request($method, $endpoint, array $params = array());

    abstract public function getUserInfo();

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return get_instance()->$property;
    }
}