<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Place\Autocomplete;
use Ivory\GoogleMap\Map;

use Ivory\GoogleMap\Overlay\Animation;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\Marker;
use Ivory\GoogleMap\Overlay\MarkerShape;
use Ivory\GoogleMap\Overlay\MarkerShapeType;
use Ivory\GoogleMap\Overlay\Symbol;
use Ivory\GoogleMap\Overlay\SymbolPath;

class Googlemaps extends Map
{
    private $resolverPrefixes = array('builder', 'overlay', 'control', 'autocomplete');

    private $resolvers = array(
        'coordinate' => 'Ivory\GoogleMap\Base\Coordinate',
        'autocomplete' => 'Ivory\GoogleMap\Place\Autocomplete',
        'event' => 'Ivory\GoogleMap\Event\Event',
        'bount' => 'Ivory\GoogleMap\Base\Bound',
        'point' => 'Ivory\GoogleMap\Base\Point'
    );

    private $builders = array();
    private $builderResolvers = array(
        'map' => 'Ivory\GoogleMap\Helper\Builder\MapHelperBuilder',
        'autocomplete' => 'Ivory\GoogleMap\Helper\Builder\PlaceAutocompleteHelperBuilder',
        'api' => 'Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder',
    );

    private $overlays = array();
    private $overlayResolvers = array(
        'marker' => 'Ivory\GoogleMap\Overlay\Marker',
        'infoWindow' => 'Ivory\GoogleMap\Overlay\InfoWindow',

        'infoWindowType' => 'Ivory\GoogleMap\Overlay\InfoWindowType',
        'animation' => 'Ivory\GoogleMap\Overlay\Animation',
        'icon' => 'Ivory\GoogleMap\Overlay\Icon',
        'markerShape' => 'Ivory\GoogleMap\Overlay\MarkerShape',
        'markerShapeType' => 'Ivory\GoogleMap\Overlay\MarkerShapeType',
        'symbol' => 'Ivory\GoogleMap\Overlay\Symbol',
        'symbolPath' => 'Ivory\GoogleMap\Overlay\SymbolPath',    
    );

    private $controls = array();
    private $controlResolvers = array(
        'mapType' => 'Ivory\GoogleMap\Control\MapTypeControl',
        'rotate' => 'Ivory\GoogleMap\Control\RotateControl',
        'scale' => 'Ivory\GoogleMap\Control\ScaleControl',
        'streetView' => 'Ivory\GoogleMap\Control\StreetViewControl',
        'zoom' => 'Ivory\GoogleMap\Control\ZoomControl',
        'fullscreen' => 'Ivory\GoogleMap\Control\FullscreenControl',
        'custom' => 'Ivory\GoogleMap\Control\CustomControl',

        'position' => 'Ivory\GoogleMap\Control\ControlPosition',
        'mapTypeStyle' => 'Ivory\GoogleMap\Control\MapTypeControlStyle',
        'mapTypeId' => 'Ivory\GoogleMap\MapTypeId',
    );

    private $events = array();

    private $autocomplete;
    private $autocompleteResolvers = array(
        'type' => 'Ivory\GoogleMap\Place\AutocompleteType',
        'componentType' => 'Ivory\GoogleMap\Place\AutocompleteComponentType',
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->library('geoip2');
        $this->load->config('googlemaps');

        $this->buildMapDefaults();
    }

    public function buildMapDefaults()
    {
        $this->addLibraries($this->config->item('googlemaps_libraries'));

        $this->setVariable('map');
        $this->builder('api')->setKey($this->config->item('googlemaps_apikey'));
    }

    public function setUserLocationAsCenter()
    {
        $record = $this->geoip2->city();
        $this->setCenter(new $this->resolveCoordinate($record->location->latitude, $record->location->longitude));
    }

    public function builder($builderType, \Closure $action = null)
    {
        $resolver = 'resolveBuilder' . $builderType;
        if (!$builderNamespace = $this->$resolver) {
            return false;
        }

        if (empty($this->builders[$builderType])) {
            $this->builders[$builderType] = $builderNamespace::create();
        }

        if ($action instanceof \Closure) {
            $result = $action($this->builders[$builderType], $this);
            return ($result === null ? $this->builders[$builderType] : $result);
        }

        return $this->builders[$builderType];
    }

    public function builders(array $builders)
    {
        $actions = array();
        foreach ($builders as $builderType => $action) {
            if (is_int($builderType) || !$action instanceof \Closure) {
                $builderType = $action;
            }

            $actions[$builderType] = $this->builder($builderType, $action);
        }

        return $actions;
    }

    public function autocomplete(\Closure $action = null)
    {
        $this->autocomplete = new $this->resolveAutocomplete();
        $this->autocomplete->setVariable('autocomplete');

        if ($action instanceof \Closure) {
            return $action($this->autocomplete, $this);
        }

        return $this->autocomplete;
    }

    public function event($eventId, \Closure $action = null, $instance, $trigger, $handle, $capture = false)
    {
        if (empty($this->events[$eventId])) {
            $this->events[$instance][$eventId] = new $this->resolveEvent($instance, $trigger, $handle, $capture);
        }

        if ($action instanceof \Closure) {
            $result = $action($this->events[$instance][$eventId], $this);
            return ($result === null ? false : $result);
        }

        return $this->events[$eventId][$instance];
    }

    public function events(array $events)
    {
        $actions = array();
        foreach ($events as $eventId => $event) {
            $arguments = array_merge(array(
                'eventId' => $eventId,
                'action' => null,
                'instance' => null,
                'trigger' => null,
                'handle' => null,
                'capture' => false
            ), $event);

            $actions[$eventId] = call_user_func_array(array($this, 'event'), array_values($arguments));
        }

        return $actions;
    }

    public function overlay($overlayId, $overlayType, \Closure $action = null, array $params = array())
    {
        if (empty($this->overlays[$overlayType][$overlayId])) {
            $overlayResolver = 'resolveOverlay' . ucfirst($overlayType);
            $overlayResolver = $this->$overlayResolver;

            $reflection = new ReflectionClass($overlayResolver);
            $paramsList = array();

            array_map(function($param) use(&$paramsList) {
                $paramsList[$param->name] = ($param->isOptional() ? $param->getDefaultValue() : false);
            }, $reflection->getConstructor()->getParameters());

            $params = array_values(array_merge($paramsList, $params));
            $this->overlays[$overlayType][$overlayId] = new $overlayResolver(...$params);
        }

        if ($action instanceof \Closure) {
            $result = $action($this->overlays[$overlayType][$overlayId], $this);
            return ($result === null ? false : $result);
        }

        return $this->overlays[$overlayType][$overlayId];
    }

    public function overlays(array $overlays)
    {
        $actions = array();
        foreach ($overlays as $overlayType => $overlayIds) {
            foreach ($overlayIds as $overlayId => $overlay) {
                $params = array_merge(array(
                    'action' => null,
                    'params' => array()
                ), $overlay);

                $actions[$overlayType][$overlayId] = $this->overlay($overlayId, $overlayType, $params['action'], $params['params']);
            }
        }

        return $actions;
    }

    public function control($controlId, $controlType, \Closure $action = null, array $params = array())
    {
        if (empty($this->controls[$controlType][$controlId])) {
            $controlResolver = 'resolveControl' . ucfirst($controlType);
            $controlResolver = $this->$controlResolver;

            $reflection = new ReflectionClass($controlResolver);
            $paramsList = array();

            array_map(function($param) use(&$paramsList) {
                $paramsList[$param->name] = ($param->isOptional() ? $param->getDefaultValue() : false);
            }, $reflection->getConstructor()->getParameters());

            $params = array_values(array_merge($paramsList, $params));
            $this->controls[$controlType][$controlId] = new $controlResolver(...$params);
        }

        if ($action instanceof \Closure) {
            $result = $action($this->controls[$controlType][$controlId], $this);
            return ($result === null ? false : $result);
        }

        return $this->controls[$controlType][$overlayId];
    }

    public function controls(array $controls)
    {
        $actions = array();
        foreach ($controls as $controlType => $controlIds) {
            foreach ($controlIds as $controlId => $control) {
                $params = array_merge(array(
                    'action' => null,
                    'params' => array()
                ), $control);

                $actions[$controlType][$controlId] = $this->control($controlId, $controlType, $params['action'], $params['params']);
            }
        }

        return $actions;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists(get_instance(), $property)) {
            return get_instance()->$property;
        }

        if ($this instanceof Googlemaps && strncmp($property, 'resolve', strlen('resolve')) === 0) {
            $property = str_replace('resolve', '', $property); $resolver = false;

            foreach ($this->resolverPrefixes as $prefix) {
                if (strpos(strtolower($property), $prefix) !== false) {
                    $checkProperty = lcfirst(str_replace($prefix, '', lcfirst($property)));
                    if (empty($checkProperty)) {
                        break;
                    }

                    $property = $checkProperty;
                    $resolver = $prefix . 'Resolvers';
                    break;
                }
            }

            if (!$resolver) {
                return (!empty($this->resolvers[lcfirst($property)]) ? $this->resolvers[lcfirst($property)] : null);
            }

            if (property_exists($this, $resolver)) {
                return (!empty($this->$resolver[$property]) ? $this->$resolver[$property] : null);
            }
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
}