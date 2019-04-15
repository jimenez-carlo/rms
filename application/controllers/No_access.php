<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class No_access extends CI_Controller {
	
  public function __construct() { 
     parent::__construct(); 
     $this->load->helper('url');
  }

	public function index()
	{
		$this->load->view('no_access_view');
	}

}
