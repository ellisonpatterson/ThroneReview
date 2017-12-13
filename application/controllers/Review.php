<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Review extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('location_model');
        $this->load->model('review_model');
    }

	public function create($locationId)
	{
        if ($this->input->method() != 'post' || !$this->user->isLoggedIn() || $this->review_model->getReview(array(
            'user_id' => $this->user->user_id,
            'location_id' => $locationId
        ))) {
            redirect('locations/' . $locationId);
        }

        $details = array_merge($this->input->post(array(
            'rating',
            'review',
        )), array(
            'user_id' => $this->user->user_id,
            'location_id' => $locationId,
            'added' => date('Y-m-d H:i:s')
        ));

        if (empty($details['rating']) || empty($details['review']) || !in_array($details['rating'], range(1, 5))) {
            redirect('locations/' . $locationId);
        }

        $this->db->insert('review', $details);
        $details['review_id'] = $this->db->insert_id();

        if ($this->input->is_ajax_request()) {
            return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(
                json_encode($details)
            );
        } else {
            redirect('locations/' . $locationId);
        }
    }

	public function update($locationId)
	{
        if ($this->input->method() != 'post' || !$this->user->isLoggedIn() || !$review = $this->review_model->getReview(array(
            'user_id' => $this->user->user_id,
            'location_id' => $locationId
        ))) {
            redirect('locations/' . $locationId);
        }

        $details = array_merge($this->input->post(array(
            'rating',
            'review',
        )), array(
            'review_id' => $review['review_id'],
            'updated' => date('Y-m-d H:i:s')
        ));

        if (empty($details['rating']) || empty($details['review']) || !in_array($details['rating'], range(1, 5))) {
            redirect('locations/' . $locationId);
        }

        $this->db->where('review_id', $review['review_id']);
        $this->db->update('review', $details);

        if ($this->input->is_ajax_request()) {
            return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(
                json_encode($details)
            );
        } else {
            redirect('locations/' . $review['location_id']);
        }
    }

	public function delete($locationId)
	{
        if (!$this->user->isLoggedIn() || !$review = $this->review_model->getReview(array(
            'user_id' => $this->user->user_id,
            'location_id' => $locationId
        ))) {
            redirect('locations/' . $locationId);
        }

        $this->db->delete('review', array(
            'review_id' => $review['review_id']
        ));

        redirect('locations/' . $review['location_id']);
    }
}