<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rerfo extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Rerfo_model', 'rerfo');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Rerfo');
		$this->header_data('nav', 'rerfo');
		$this->header_data('dir', './');

		$data['table'] = $this->rerfo->get_list($_SESSION['region']);
		$this->template('rerfo/list', $data);
	}

	public function view($rid)
	{
		$this->access(1);
		$this->header_data('title', 'Rerfo');
		$this->header_data('nav', 'rerfo');
		$this->header_data('dir', './../../');
		// enable branch filter
		$this->load->model("Cmc_model", "cmc");
		$data["branch"] = $this->cmc->get_branches_tbl("","","","","","",$_SESSION['region']);

		$data["rerfo"] = $this->rerfo->load($rid);

		$this->template('rerfo/view', $data);
	}

	public function sprint($rid) 
	{
		$data['rerfo'] = $this->rerfo->print_rerfo($rid);
		$this->load->view('rerfo/print', $data);
	}

	public function request($rid)
	{
    if ($this->rerfo->request_reprint($rid)) {
    	$_SESSION['messages'][] = 'Request for reprint sent.';
    }
    else {
    	$_SESSION['warning'][] = 'Request for reprint already sent.';
    }
    redirect('rerfo');
	}
}
