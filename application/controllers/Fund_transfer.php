<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fund_transfer extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Fund_transfer_model', 'fund_transfer');
  }

  public function index()
  {
    $this->access(1);
    $this->header_data('title', 'Process Transfer');
    $this->header_data('nav', 'fund_transfer');
    $this->header_data('dir', './');

    $vid = $this->input->post('vid');
    if (!empty($vid)) $this->save_process($vid);

    $data['table'] = $this->fund_transfer->get_for_process();
    $this->template('fund_transfer/for_process', $data);
  }

  public function process_transfer($vid)
  {
    $data['voucher'] = $this->fund_transfer->get_voucher($vid);
    $view = $this->load->view('fund_transfer/process_transfer', $data, TRUE);
    echo json_encode($view);
  }

  public function save_process($vid)
  {
    $err_msg = array();

    foreach ($vid as $key) {
      $this->form_validation->set_rules('dm_no['.$key.']', 'Debit Memo #', 'required');
      $this->form_validation->set_rules('process_date['.$key.']', 'Date Processed', 'required');
    }

    if ($this->form_validation->run() == FALSE) {
      $err_msg[] = validation_errors();
    }

    // if (!empty($err_msg)) $_SESSION['warning'] = $err_msg;
    // echo json_encode(array("status" => FALSE, "message" => $err_msg));
    else $this->true_save_process($vid);
  }

  public function true_save_process($vid)
  {
    $offline = $this->input->post('offline');
    foreach ($vid as $key) {
      $voucher = new Stdclass();
      $voucher->dm_no = $this->input->post('dm_no['.$key.']');
      $voucher->process_date = $this->input->post('process_date['.$key.']');
      $voucher->process_timestamp = date('Y-m-d H:i:s');
      $voucher->offline = (isset($offline));
      $voucher->status = 1;
      $this->db->update('tbl_voucher', $voucher, array('vid' => $key));
    }
    $_SESSION['messages'][] = 'Changes saved successfully.';

    // $_SESSION['messages'][] = 'Debit Memo # '.$voucher->dm_no.' saved successfully.';
    // echo json_encode(array("status" => TRUE));
  }

  /**
   * Treasury to Transfer Fund
   */
  public function for_deposit() {
    $this->access(1);
    $this->header_data('title', 'Fund Transfer');
    $this->header_data('nav', 'fund_transfer');
    $this->header_data('dir', './../');

    $vid = $this->input->post('vid');
    if (!empty($vid)) $this->save_transfer($vid);

    $data['table'] = $this->fund_transfer->get_for_transfer();
    $this->template('fund_transfer/for_transfer', $data);
  }

  public function transfer_fund($vid)
  {
    $data['voucher'] = $this->fund_transfer->get_voucher($vid);
    $view = $this->load->view('fund_transfer/transfer_fund', $data, TRUE);
    echo json_encode($view);
  }

  public function save_transfer($vid)
  {
    $err_msg = array();
    foreach ($vid as $key) {
      $this->form_validation->set_rules('transfer_date['.$key.']', 'Date Deposited', 'required');
    }

    if ($this->form_validation->run() == FALSE) {
      $err_msg[] = validation_errors();
    }

    // if (!empty($err_msg)) $_SESSION['warning'] = $err_msg;
    // echo json_encode(array("status" => FALSE, "message" => $err_msg));
    if (empty($err_msg)) $this->true_save_transfer($vid);
  }

  public function true_save_transfer($vid)
  {
    $offline = $this->input->post('offline');
    foreach ($vid as $key) {
      $voucher = new Stdclass();
      $voucher->vid = $key;
      $voucher->transfer_date = $this->input->post('transfer_date['.$key.']');
      $voucher->transfer_timestamp = date('Y-m-d H:i:s');
      $voucher->offline = (isset($offline));
      $voucher->status = 2;
      $voucher = $this->fund_transfer->save_transfer($voucher);
    }
    $_SESSION['messages'][] = 'Changes saved succesfully.';

  }

  public function for_deposit_repo() {
    $this->access(1);
    $this->header_data('title', 'Repo CA Deposit');
    $this->header_data('nav', 'fund_transfer');
    $this->header_data('dir', base_url());
    if ($for_deposit = $this->input->post('deposit_funds')) {
      $keys = array_keys($for_deposit);
      foreach ($keys as $i) {
        $validation[] = [ 'field' => 'deposit_funds['.$i.'][debit_memo]', 'label' => 'Debit Memo', 'rules' => 'required' ] ;
        $validation[] = [ 'field' => 'deposit_funds['.$i.'][date_deposited]', 'label' => 'Deposit Date', 'rules' => 'required' ] ;
      }

      $this->form_validation->set_rules($validation);
      if ($this->form_validation->run()) {
        $deposit_funds = [];
        foreach ($this->input->post('deposit_funds') as $deposit) {
          $deposit_funds[] = array_merge($deposit, ['status' => 'DEPOSITED', 'date_debit_memo_encoded' => date('Y-m-d H:m:s') ]);
        }
        if($this->db->update_batch('tbl_repo_batch', $deposit_funds, 'repo_batch_id')) {
          foreach ($for_deposit as $batch) {
            $_SESSION['messages'][] = 'Success!! Debit Memo #'.$batch['debit_memo'].' Saved!!';
          }
        }
      }
    }
    $data['table'] = $this->fund_transfer->get_repo_for_deposit();
    $this->template('fund_transfer/repo_for_deposit', $data);
  }
}
