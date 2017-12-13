<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use GeoIp2\Database\Reader;

class Geoip2
{
    private $reader;

    public function __construct()
    {
        $this->load->config('geoip2');
        $this->reader = new Reader($this->config->item('geoip2_database'));
    }

    public function city($ip = false)
    {
        return $this->reader->city($this->resolveIp($ip));
    }

    public function resolveIp($ip = false)
    {
        return (!$ip ? $_SERVER['REMOTE_ADDR'] : $ip);
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return get_instance()->$property;
    }
}