<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
	
  public function __construct() { 
     parent::__construct();
     $this->load->helper('url');
  }

	public function index() 
	{
		$this->access(1);
		$this->header_data('title', 'Home');
		$this->header_data('nav', 'home');
		$this->header_data('dir', './');
		$this->template('home');
	}
}


