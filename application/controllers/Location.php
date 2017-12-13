<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('googlemapsservice');
        $this->load->model('location_model');
        $this->load->model('review_model');
    }

	public function view($locationId)
	{
        $location = $this->location_model->getLocation(array(
            'location_id' => $locationId
        ));

        if (empty($location)) {
            redirect('/');
        }

        $reviews = $this->review_model->getReviews(array(
            'join' => $this->review_model::FETCH_USER,
            'location_id' => $locationId,
            'order' => 'added'
        ));

        $userReview = array();
        if ($this->user->isLoggedIn()) {
            $userReview = $this->review_model->getReview(array(
                'user_id' => $this->user->user_id,
                'location_id' => $locationId
            ));
        }

        // $apiDetails = $this->googlemapsservice->getPlaceById($location['place_id'])->getResult();

		$this->load->view('wrapper', array(
            'content' => $this->load->view('location/content', array(
                'title' => $location['name'],
                'averageRating' => $this->review_model->getAverageRating(array('location_id' => $locationId)),
                'userReview' => $userReview,
                'reviews' => $reviews,
                'location' => $location,
            ), true),
            'scripts' => $this->load->view('location/scripts', array(), true)
        ));
	}

	public function popular()
	{
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $locations = $this->location_model->getLocations(array(
            'join' => $this->location_model::FETCH_REVIEW,
            'avg_rating' => true,
            'has_avg_rating' => true,
            'order' => 'avg_rating',
            'group_by' => array('location_id', 'review_id'),
            'direction' => 'DESC',
            'limit' => 25
        ));

        return $this->output
        ->set_content_type('application/json')
        ->set_status_header(200)
        ->set_output(
            json_encode(
                $this->load->view('partial/locations', array(
                    'locations' => $locations
                ), true)
            )
        );
    }

	public function nearby()
	{
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $nearby = $this->input->get(array(
            'latitude',
            'longitude',
            'radius',
            'type'
        ));

        $locations = $this->location_model->getLocations(array(
            'join' => $this->location_model::FETCH_REVIEW,
            'avg_rating' => true,
            'group_by' => array('location_id', 'review_id'),
            'nearby' => $nearby
        ));

        if (!empty($nearby['type']) && $nearby['type'] == 'json') {
            $output = $locations;
        } else {
            $output = $this->load->view('partial/locations', array(
                'locations' => $locations
            ), true);
        }

        return $this->output
        ->set_content_type('application/json')
        ->set_status_header(200)
        ->set_output(
            json_encode($output)
        );
    }

	public function overlay()
	{
        if (!$this->input->is_ajax_request()) {
            redirect('/');
        }

        $details = $this->input->get(array(
            'location_id',
            'place_id',
        ));

        if (empty($details)) {
            redirect('/');
        }

        // $details = array(
            // 'place_id' => 'ChIJkWglTgb-04kR17elEr78XaE'
        // );
        $reviews = array();
        $averageRating = false;

        $location = $this->location_model->getLocation($details);
        if (empty($location)) {
            $apiDetails = $this->googlemapsservice->getPlaceById($details['place_id'])->getResult();
            if (empty($apiDetails)) {
                redirect('/');
            }

            // echo '<pre>'; var_dump($apiDetails); echo '</pre>'; die();
            $location = array(
                'place_id' => $apiDetails->getPlaceId(),
                'name' => $apiDetails->getName(),
                'address' => $apiDetails->getFormattedAddress(),
                'latitude' => $apiDetails->getGeometry()->getLocation()->getLatitude(),
                'longitude' => $apiDetails->getGeometry()->getLocation()->getLongitude()
            );

            $this->db->insert('location', $location);
            $location['location_id'] = $this->db->insert_id();
        } else {
            $averageRating = $this->review_model->getAverageRating(array('location_id' => $location['location_id']));

            $reviews = $this->review_model->getReviews(array(
                'join' => $this->review_model::FETCH_USER,
                'location_id' => $location['location_id'],
                'limit' => 5,
                'order' => 'rating'
            ));
        }

		$this->load->view(wrapper_view(), array(
            'content' => $this->load->view('location/overlay', array(
                'title' => $location['name'],
                'location' => $location,
                'reviews' => $reviews,
                'averageRating' => $averageRating
            ), true),
            'modalFooter' => $this->load->view('location/overlay_footer', array(
                'location' => $location,
            ), true),
        ));
	}
}