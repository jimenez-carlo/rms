<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends MY_Controller {
	
	public function __construct() { 
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('directory');
    $this->load->model('Sales_model', 'sales');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Sales');
		$this->header_data('nav', 'sales');
		$this->header_data('dir', './');

		$data = array();
		$engine_no = $this->input->post('engine_no');

		if (!empty($engine_no))
		{
			$sales = $this->sales->load_sales_by_engine($engine_no);

			if (!empty($sales)) {
				$data['sales'] = $sales;
			}
			else {
				$_SESSION['warning'][] = 'Engine Number does not exist.';
			}
		}

		$this->template('sales/list', $data); 
	}

	public function view($sid)
	{
		$this->access(1);
		$this->header_data('title', 'Sales');
		$this->header_data('nav', 'sales');
		$this->header_data('dir', './../../');

		$data['sales'] = $this->sales->load_sales($sid);
		$this->template('sales/view', $data);
	}

	public function orcr()
	{
		$this->access(1);
		$this->header_data('title', 'OR CR Print');
		$this->header_data('nav', 'orcr');
		$this->header_data('dir', './../');

		$data = array();
		$engine_no = $this->input->post('engine_no');

		if (!empty($engine_no))
		{
			$sales = $this->sales->get_orcr_by_engine($engine_no);
			
			if (!empty($sales)) {
				$data['sales'] = $sales;
			}
			else {
				$_SESSION['warning'][] = 'Engine #'.$engine_no.' does not exist.';
			}
		}

		$this->template('sales/orcr_print', $data);
	}

	public function print_orcr($sid)
	{
		$data['sales'] = $this->sales->print_orcr($sid);
		$this->load->view('sales/print_orcr', $data);
	}
}


