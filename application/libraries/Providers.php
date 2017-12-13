<?php

class Providers
{
    public function loadProviderLibrary($providerId, $providerName = 'provider')
    {
        if (class_exists(ucfirst($providerId))) {
            $this->load->library(strtolower($providerId), null, $providerName);
            return true;
        }

        return false;
    }

    public function __get($property)
    {
        return get_instance()->$property;
    }
}