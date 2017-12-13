<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

if (!function_exists('carbon')) {
	function carbon($time = null, $tz = null)
	{
        return new Carbon($time, $tz);
	}
}