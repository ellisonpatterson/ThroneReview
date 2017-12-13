<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('googlemaps');
        $this->load->model('review_model');
    }

	public function index()
	{
        $this->googlemaps->setUserLocationAsCenter();
        $this->googlemaps->setAutoZoom(false);
        $this->googlemaps->setMapOptions(array(
            'zoom' => 15,
            'draggable' => true,
            'scrollwheel' => true
        ));

        $this->googlemaps->addStylesheetOptions(array(
            'width' => '100%',
            'height' => '100%'
        ));

        $autocomplete = $this->googlemaps->autocomplete();
        $autocomplete->setTypes([
            $this->googlemaps->resolveAutocompleteType::ESTABLISHMENT
        ]);
        $autocomplete->setInputAttributes(array(
            'class' => 'form-control my-2 mr-2 col-sm-6',
            'placeholder' => 'Search for establishment'
        ));

        $this->googlemaps->overlays(array(
            'marker' => array(
                'initialLocation' => array(
                    'action' => function($marker, $map) {
                        $marker->setVariable('current_location');
                        $marker->setOptions(array(
                            'flat' => true,
                            'title' => 'Approximate Location'
                        ));

                        $map->getOverlayManager()->addMarker($marker);
                    },
                    'params' => array(
                        'position' => $this->googlemaps->getCenter(),
                        'animation' => $this->googlemaps->resolveOverlayAnimation::DROP,
                        'icon' => new $this->googlemaps->resolveOverlayIcon('https://maps.gstatic.com/mapfiles/markers2/icon_green.png'),
                        'options' => array('clickable' => true)
                    )
                ),
                'activeLocation' => array(
                    'action' => function($marker, $map) {
                        $marker->setVariable('active_location');
                        $marker->setOptions(array(
                            'flat' => true,
                            'visible' => false
                        ));

                        $map->getOverlayManager()->addMarker($marker);
                    },
                    'params' => array(
                        'position' => $this->googlemaps->getCenter(),
                        'options' => array('clickable' => true)
                    )
                ),
            )
        ));

        $this->googlemaps->events(array(
            'acFirstLoad' => array(
                'action' => function($event, $map) use($autocomplete) {
                    $autocomplete->getEventManager()->addEventOnce($event);
                },
                'instance' => $this->googlemaps->getVariable(),
                'trigger' => 'idle',
                'handle' => "
                    function() {
                        window.placesService = new google.maps.places.PlacesService({$this->googlemaps->getVariable()});
                        {$autocomplete->getVariable()}.bindTo('bounds', {$this->googlemaps->getVariable()});

                        var bounds = {$this->googlemaps->getVariable()}.getBounds();
                        var center = {$this->googlemaps->getVariable()}.getCenter();
                        if (bounds && center) {
                            var radius = google.maps.geometry.spherical.computeDistanceBetween(center, bounds.getNorthEast());
                            window.userCenter = {
                                latitude: center.lat(),
                                longitude: center.lng(),
                                radius: radius
                            }
                        }

                        getBrowserLocation(function(position) {
                            {$this->googlemaps->overlays['marker']['activeLocation']->getVariable()}.setPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
                        });
                    }
                "
            ),
            'acTrigger' => array(
                'action' => function($event, $map) use($autocomplete) {
                    $autocomplete->getEventManager()->addEvent($event);
                },
                'instance' => $autocomplete->getVariable(),
                'trigger' => 'place_changed',
                'handle' => "
                    function() {
                        var place = {$autocomplete->getVariable()}.getPlace();
                        if (place.place_id) {
                            console.log(place.geometry.location);
                            {$this->googlemaps->overlays['marker']['activeLocation']->getVariable()}.setPosition(place.geometry.location);
                            {$this->googlemaps->getVariable()}.setCenter(place.geometry.location);

                            triggerModal('locations/overlay', {
                                place_id: place.place_id,
                            });
                        }
                    }
                "
            ),
            'mapClick' => array(
                'action' => function($event, $map) use($autocomplete) {
                    $map->getEventManager()->addEvent($event);
                },
                'instance' => $this->googlemaps->getVariable(),
                'trigger' => 'click',
                'handle' => "
                    function(event) {
                        console.log(event);
                        {$this->googlemaps->overlays['marker']['activeLocation']->getVariable()}.setVisible(true);
                        {$this->googlemaps->overlays['marker']['activeLocation']->getVariable()}.setPosition(event.latLng);

                        if (event.placeId) {
                            event.stop();
                            {$this->googlemaps->getVariable()}.setCenter(event.latLng);

                            triggerModal('locations/overlay', {
                                place_id: event.placeId,
                            });
                        }
                    }
                ",
            ),
            'mapBoundsChanged' => array(
                'action' => function($event, $map) use($autocomplete) {
                    $map->getEventManager()->addEvent($event);
                },
                'instance' => $this->googlemaps->getVariable(),
                'trigger' => 'bounds_changed',
                'handle' => "
                        debounce(function() {
                            if (window.loadedMarkers === undefined) {
                                window.loadedMarkers = [];
                            }

                            if (window.notLoadingBounds || window.notLoadingBounds === undefined) {
                                window.notLoadingBounds = false;

                                var bounds = {$this->googlemaps->getVariable()}.getBounds();
                                var center = {$this->googlemaps->getVariable()}.getCenter();
                                if (bounds && center) {
                                    var radius = google.maps.geometry.spherical.computeDistanceBetween(center, bounds.getNorthEast());
                                    fetchNearby(center, radius, function(response) {
                                        $.each(response, function(key, data) {
                                            if ($.inArray(data.place_id, window.loadedMarkers) === -1) {
                                                var marker = new google.maps.Marker({
                                                    title: data.name,
                                                    position: new google.maps.LatLng(data.latitude, data.longitude),
                                                    animation: google.maps.Animation.DROP,
                                                    place: {
                                                        placeId: data.place_id,
                                                        location: new google.maps.LatLng(data.latitude, data.longitude)
                                                    }
                                                });

                                                marker.addListener('click', function() {
                                                    triggerModal('locations/overlay', {
                                                        location_id: data.location_id,
                                                    });
                                                });

                                                marker.setMap({$this->googlemaps->getVariable()});
                                                window.loadedMarkers.push(data.place_id);
                                            }
                                        });
                                    });
                                }

                                window.notLoadingBounds = true;
                            }
                        }, 500)
                "
            )
        ));

        $builders = $this->googlemaps->builders(array(
            'map' => function($builder, $map) {
                $builder->getFormatter()->setDebug(true);
                $builder->getFormatter()->setIndentationStep(4);
                return $builder->build();
            },
            'autocomplete' => function($builder, $map) {
                $builder->getFormatter()->setDebug(true);
                $builder->getFormatter()->setIndentationStep(4);
                return $builder->build();
            },
            'api' => function($builder, $map) use ($autocomplete) {
                $builder->getFormatter()->setDebug(true);
                $builder->getFormatter()->setIndentationStep(4);
                return $builder->build()->render(array(
                    $map, $autocomplete
                ));
            }
        ));

        $this->googlemaps->controls(array(
            'custom' => array(
                'autocomplete' => array(
                    'action' => function($control, $map) {
                        $map->getControlManager()->addCustomControl($control);
                    },
                    'params' => array(
                        'position' => $this->googlemaps->resolveControlPosition::TOP_RIGHT,
                        'control' => '
                            var autocomplete = ' . json_encode($builders['autocomplete']->renderHtml($autocomplete)) . ';
                            $("body").append(autocomplete);
                            return document.getElementById("' . $autocomplete->getHtmlId() . '");
                        ',
                    )
                )
            )
        ));

		$this->load->view('wrapper', array(
            'content' => $this->load->view('dashboard/content', array(
                'mapsHtml' => $builders['map']->renderHtml($this->googlemaps),
                'recentReviews' => $this->review_model->getReviews(array(
                    'join' => $this->review_model::FETCH_USER | $this->review_model::FETCH_LOCATION,
                    'order' => 'added',
                    'direction' => 'DESC'
                ))
            ), true),
            'scripts' => $this->load->view('dashboard/scripts', array(
                'apiScript' => $builders['api'],
                'mapsScript' => $builders['map']->renderJavascript($this->googlemaps),
                'autocompleteScript' => $builders['autocomplete']->renderJavascript($autocomplete)
            ), true)
        ));
	}

    protected function setupMap()
    {
        
    }
}
