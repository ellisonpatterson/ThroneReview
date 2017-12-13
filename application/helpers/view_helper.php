<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('wrapper_view')) {
	function wrapper_view()
	{
        if (get_instance()->input->is_ajax_request()) {
            return 'partial/modal';
        }

        return 'wrapper';
	}
}

if (!function_exists('is_modal')) {
	function is_modal()
	{
        if (get_instance()->input->is_ajax_request()) {
            return true;
        }

        return false;
	}
}

if (!function_exists('rating_stars')) {
	function rating_stars($value)
	{
        $rounded = floor($value * 2) / 2;
        $response = '<div class="rating" data-toggle="tooltip" data-offset="0 5" data-placement="top" title="' . $rounded . '/5 Stars">';

        $modulo = function($var1, $var2) {
            $tmp = $var1 / $var2;
            return (float) ($var1 - (((int) ($tmp)) * $var2));            
        };

        for ($i = 0; $i <= 5; $i += 0.5) {
            if ($i < $rounded && $modulo($i, 1) == 0.5) {
                $response .= '<i class="fa fa-star" aria-hidden="true"></i>';
            } elseif ($i == $rounded &&  $modulo($rounded, 1) == 0.5) {
                $response .= '<i class="fa fa-star-half-o" aria-hidden="true"></i>';
            } elseif ($modulo($i, 1) == 0.5) {
                $response .= '<i class="fa fa-star-o" aria-hidden="true"></i>';
            }
        }

        return $response .= '</div>';
	}
}