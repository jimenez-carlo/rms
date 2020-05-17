<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fund extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Fund_model', 'fund');
    $this->load->model('Check_model', 'check');
  }

  public function index()
  {
    $this->access(1);
    $this->header_data('title', 'RRT Fund');
    $this->header_data('nav', 'fund');
    $this->header_data('dir', './');
    $this->footer_data('script', '<script src="./assets/modal/rrt_fund.js"></script>');

    $data['table'] = $this->fund->load_rrt_fund($_SESSION['region_id']);
    $this->template('fund/view', $data);
  }

  public function transaction($fid, $company, $type)
  {
    $name = $this->fund->get_name($fid);
    $err_msg = array();

    $this->form_validation->set_rules('check_no', 'Check # for '.$name, 'required');
    $this->form_validation->set_rules('check_date', 'Check date for '.$name, 'required');
    $this->form_validation->set_rules('amount', 'Amount for '.$name, 'required|is_numeric|non_zero');

    if ($this->form_validation->run() == FALSE) $err_msg[] = validation_errors();

    $amount = $this->input->post('amount');
    if (!empty($amount))
    {
      switch ($type)
      {
      case 1:
      case 2:
        $fund = $this->fund->get_cash_in_bank($fid);
        $m_balance = $this->fund->get_m_balance($fid);
        if ($fund - $amount < $m_balance) $err_msg[] = 'Invalid amount. You must have a maintaining balance of at least Php '.$m_balance;
        break;
      case 3:
        $fund = $this->fund->get_cash_on_hand($fid);
        if ($fund < $amount) $err_msg[] = 'Amount is greater than Cash on Hand.';
        break;
      }
    }

    $check_no = $this->input->post('check_no');
    $check_date = $this->input->post('check_date');
    // if (!empty($check_no) && $check_no != 'N/A')
    // {
    // 	$check_no_exists = $this->check->is_check_no_exists($check_no, $fid);
    // 	if ($check_no_exists > 0)
    // 	{
    // 		$err_msg[] = 'Check # already exists. There must be no duplicate Check # per Company and Region.';
    // 	}
    // }

    if (!empty($err_msg)) echo json_encode(array("status" => FALSE, "message" => $err_msg));
    else $this->save_transaction($fid, $company, $type, $amount, $check_no, $check_date);
  }

  public function save_transaction($fid, $company, $type, $amount, $check_no, $check_date)
  {
    $transaction = new Stdclass();
    $transaction->fund = $fid;
    $transaction->type = $type;
    $transaction->amount = $amount;
    $transaction->check_no = $check_no;

    $check = new Stdclass();
    $check->fund = $fid;
    $check->check_no = $check_no;
    $check->check_date = $check_date;
    $check->amount = $amount;

    $this->fund->save_rrt_transaction($transaction, $check);

    $name = $this->fund->get_name($fid);
    switch ($type) {
    case 1: $_SESSION['messages'][] = 'Cash withdrawal for '.$name.' saved successfully.'; break;
    case 2: $_SESSION['messages'][] = 'Check withdrawal for '.$name.' saved successfully.'; break;
    case 3: $_SESSION['messages'][] = 'Deposit for '.$name.' saved successfully.'; break;
    }
    echo json_encode(array("status" => TRUE));
  }

  public function audit()
  {
    $this->access(1);
    $this->header_data('title', 'Fund Log');
    $this->header_data('nav', 'fund');
    $this->header_data('dir', './../');

    $region = $_SESSION['region_id'];
    $data['table'] = $this->db->query("select fh.*, company
      from tbl_fund_history fh
      inner join tbl_fund on fh.fund = fid
      where region = ".$region."
      and type < 5
      order by date desc
      limit 1000")->result_object();

    $this->template('fund/passbook', $data);
  }

  public function bank()
  {
    $this->access(1);
    $this->header_data('title', 'Fund Bank');
    $this->header_data('nav', 'bank');
    $this->header_data('dir', './../');

    $funds = $this->fund->load_all();

    $submit = $this->input->post('submit');
    if (!empty($submit))
    {
      foreach ($funds as $fund)
      {
        $fid = $fund->fid;
        $name = $fund->region_name.' '.$fund->company_name;

        $this->form_validation->set_rules('bank', 'Bank for '.$name, 'required');
        $this->form_validation->set_rules('m_balance', 'Maintaining Balance for '.$name, 'required');
        $this->form_validation->set_rules('acct_number', 'Account # for '.$name, 'required');
        $this->form_validation->set_rules('sign_1', 'Signatory #1 for '.$name, 'required');
        $this->form_validation->set_rules('sign_2', 'Signatory #2 for '.$name, 'required');
        $this->form_validation->set_rules('sign_3', 'Signatory #3 for '.$name, 'required');

        $new_funds[$fid] = new Stdclass();
        $new_funds[$fid]->bank = $bank[$fid];
        $new_funds[$fid]->m_balance = $m_balance[$fid];
        $new_funds[$fid]->acct_number = $acct_number[$fid];
        $new_funds[$fid]->sign_1 = $sign_1[$fid];
        $new_funds[$fid]->sign_2 = $sign_2[$fid];
        $new_funds[$fid]->sign_3 = $sign_3[$fid];
      }

      if ($this->form_validation->run() == TRUE)
      {
        $this->fund->update_fund_dtls($funds, $new_funds);
        $_SESSION['messages'][] = 'RRT fund details updated successfully.';
      }
    }

    $data['table'] = $funds;
    $this->template('fund/bank', $data);
  }
}
