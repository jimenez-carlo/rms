<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_fund extends MY_Controller {

	public function __construct() {
		parent::__construct();
                $this->load->model('Return_fund_model', 'return_fund');
                if ($_SESSION['company'] == 8) {
                  $this->region  = $this->mdi_region;
                  $this->company = $this->mdi;
                }
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Return Fund');
		$this->header_data('nav', 'return_fund');
		$this->header_data('dir', './');
                $this->footer_data('script', '<script src="'.base_url().'vendors/datatables/js/dataTables.select.min.js"></script>');
                $this->footer_data('return_fund_js', '<script src="'.base_url().'assets/js/return_fund.js"></script>');

		$param = new Stdclass();
		$param->region    = $this->input->post('region');
		$param->reference = $this->input->post('reference');
		$param->date_from = $this->input->post('date_from') ?: date('Y-m-d', strtotime('-7 days'));
                $param->date_to   = $this->input->post('date_to')   ?: date('Y-m-d');

                $data['date_from'] = $param->date_from;
                $data['date_to']   = $param->date_to;
		$data['table']     = $this->return_fund->load_list($param);
		$data['region']    = $this->region;
		$this->template('return_fund/list', $data);
	}

	public function ca($vid)
	{
		$this->access(1);
		$this->header_data('title', 'Return Fund');
		$this->header_data('nav', 'return_fund');
		$this->header_data('dir', './../../');

		$save = $this->input->post('save');
		if (!empty($save)) {
			$this->form_validation->set_rules('amount', 'Amount', 'required|is_numeric|non_zero');

			if ($this->form_validation->run()
				&& ($slip = $this->return_fund->upload_slip())) {
				$return = new Stdclass();
				$return->fund = $vid;
				$return->amount = $this->input->post('amount');
				$return->slip = $slip;
				$this->return_fund->save_return($return);
			}
		}

		$data['fund'] = $this->return_fund->load_fund($vid);
		$this->template('return_fund/ca', $data);
	}

	public function view($rfid)
	{
		$this->access(1);
		$this->header_data('title', 'Return Fund');
		$this->header_data('nav', 'return_fund');
		$this->header_data('dir', './../../');

		$liquidate = $this->input->post('liquidate');
		if (!empty($liquidate)) $this->return_fund->liquidate_return($rfid);

		$data['return'] = $this->return_fund->load_return($rfid);
		$this->template('return_fund/view', $data);
	}
}
