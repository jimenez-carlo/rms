<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nru extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Login_model', 'login');
    $this->load->model('Nru_model', 'nru');
		$this->load->model('Fund_model', 'fund');
  }

	public function index()
	{
		$this->access(11);
		$this->header_data('title', 'NRU');
		$this->header_data('nav', 'nru');
		$this->header_data('dir', './');
		$this->header_data('link', '
			<link href="assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="assets/DT_bootstrap.js"></script>
      <script src="assets/js/nru.js"></script>');

		// on save
		$submit = $this->input->post('submit');
		if (!empty($submit)) {
			$this->submit_save();
		}

		// get fund
		$data['table'] = $this->nru->load_sales($_SESSION['region']);
		$data['cash_on_check'] = $this->fund->get_cash_on('check');

		$save = $this->input->post('save');
		if (!empty($save))
		{
			$this->save();
		}
		else $this->template('nru/view', $data);
	}

	public function save()
	{
		$this->access(11);
		$this->header_data('title', 'NRU');
		$this->header_data('nav', 'nru');
		$this->header_data('dir', './');
		$this->footer_data('script', '
      <script src="assets/js/nru.js"></script>');

		$data = array();
		$check = $cash = array('1' => 0, '2' => 0, '3' => 0);
		$data['cash_on_check'] = $this->fund->get_cash_on('check');
		$data['cash_on_hand'] = $this->fund->get_cash_on('hand');

		$this->load->model('Sales_model', 'sales');
		$this->load->model('Cmc_model', 'cmc');
		
		$registration = $this->input->post('registration');
		$fund = $this->input->post('fund');
		foreach ($registration as $sid => $val)
		{
			if ($val > 0)
			{
				$sale = $this->sales->load_sales($sid);

				if($fund[$sid] == 1) $check[$sale->branch->company] += $val;
				else $cash[$sale->branch->company] += $val; 
			}
		}

		$data['registration'] = $registration;
		$data['fund'] = $fund;
		$data['check'] = $check;
		$data['cash'] = $cash;

		if($data['cash_on_hand'][1] < $cash[1] || $data['cash_on_hand'][2] < $cash[2] || $data['cash_on_hand'][3] < $cash[3] ||
       $data['cash_on_check'][1] < $check[1] || $data['cash_on_check'][2] < $check[2] || $data['cash_on_check'][3] < $check[3])
		{
			$_SESSION['warning'][] = "Total Expense must not be greater than fund.";
			$data['submit_btn'] = 'disabled';
		}
		else $data['submit_btn'] = '';

		$this->template('nru/save', $data);
	}

	public function submit_validate()
	{
		$cash_on_check = $this->fund->get_cash_on('check');
		$amount = $this->input->post('amount');
		$err_msg = array();

		if ($amount[1] > $cash_on_check[1]) $err_msg[] = 'Total Expense is greater than MNC Fund. Please reduce amount for expense or increase fund.';
		if ($amount[2] > $cash_on_check[2]) $err_msg[] = 'Total Expense is greater than MTI Fund. Please reduce amount for expense or increase fund.';
		if ($amount[3] > $cash_on_check[3]) $err_msg[] = 'Total Expense is greater than HPTI Fund. Please reduce amount for expense or increase fund.';

		if (!empty($err_msg)) $_SESSION['warning'] = $err_msg;
		else $this->submit_save();
	}

	public function submit_save()
	{
		$this->load->model('Sales_model', 'sales');
		$registration = $this->input->post('registration');

		foreach ($registration as $sid => $val)
		{
			if ($val > 0)
			{
				$sales = new Stdclass();
			  $sales->sid = $sid;
			  $sales->registration = $val;
			  $sales->status = 3;
				$this->sales->save_nru($sales);
			}
		}

		$cash = $this->input->post('cash');
		$check = $this->input->post('check');
		$this->fund->save_nru($_SESSION['region'], $cash, $check);

		$_SESSION['messages'][] = "NRU transaction saved successfully.";
		redirect('nru');
	}
}
