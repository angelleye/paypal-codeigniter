<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demo extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// Load helpers
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('paypal/samples/demo');
	}
}
