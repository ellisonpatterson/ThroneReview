<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ivory\GoogleMap\Service\Place\Detail\PlaceDetailService;
use Ivory\GoogleMap\Service\Place\Detail\Request\PlaceDetailRequest;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Service\Serializer\SerializerBuilder;

class Googlemapsservice
{
    private $client;
    private $messageFactory;

    public function __construct()
    {
        $this->load->config('googlemaps');

        $this->client = new Client();
        $this->messageFactory = new GuzzleMessageFactory();
    }

    public function getPlaceById($placeId)
    {
        $service = new PlaceDetailService(
            $this->client,
            $this->messageFactory
        );

        $service->setKey($this->config->item('googlemaps_apikey'));
        return $service->process(new PlaceDetailRequest($placeId));
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return get_instance()->$property;
    }
}