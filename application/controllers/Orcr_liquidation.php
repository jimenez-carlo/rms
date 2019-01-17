<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_liquidation extends MY_Controller {
	
	public function __construct() { 
		parent::__construct();
		$this->load->helper('url');
    $this->load->model('Orcr_liquidation_model', 'orcr_liquidation');
	}
 
	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'ORCR Liquidation');
		$this->header_data('nav', 'orcr_liquidation');
		$this->header_data('dir', './');

		$data['table'] = $this->orcr_liquidation->ca_for_liquidation_list();
		$data['region'] = $this->region;
		$data['company'] = $this->company;
		$this->template('orcr_liquidation/list', $data);
	}
}
