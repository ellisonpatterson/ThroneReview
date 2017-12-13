<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User implements ArrayAccess
{
    private $user = array(
        'user_id' => 0
    );

    public function __construct()
    {
        $this->load->model('user_model');

        $this->setup();
    }

    public function setup(array $userInfo = array())
    {
        $userId = $this->session->user_id;

        if ($userId) {
            $conditions = array(
                'user_id' => $userId,
                'provider_id' => $this->session->provider_id
            );

            $this->providers->loadProviderLibrary($this->session->provider_id);
        } elseif (!empty($userInfo)) {
            $conditions = $userInfo;
        }

        if (!empty($conditions)) {
            $userInfo = $this->user_model->getUser(array(
                'join' => $this->user_model::FETCH_USER_PROVIDER,
            ) + $conditions);
        }

        $user = array_merge($this->user, $userInfo);
        foreach ($user as $key => $value) {
            if (@unserialize($value) !== false) {
                $user[$key] = unserialize($value);
            }
        }

        $this->user = $user;
    }

    public function isLoggedIn()
    {
        if (empty($this->user['user_id'])) {
            return false;
        }

        return true;
    }

	public function toArray()
	{
		return $this->user;
	}

	public function get($name)
	{
		if (array_key_exists($name, $this->user)) {
			return $this->user[$name];
		} else {
			return false;
		}
	}

	public function offsetExists($offset)
	{
		return isset($this->user[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->user[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->user[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->user[$offset]);
	}

    public function __get($property)
    {
        if (property_exists(get_instance(), $property)) {
            return get_instance()->$property;
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if ($value = $this->get($property)) {
            return $value;
        }

        return false;
    }
}