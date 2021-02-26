<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ric extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Ric_model', 'ric');
    $this->load->model('Cmc_model', 'cmc');
  }

  public function monitoring() {
    switch ($_SESSION['dept_name']) {
      case 'Accounting':
      case 'Treasury':
        $data['showCheckBtn'] = true;
        break;
      case 'Regional Registration':
        $data['showCheckBtn'] = false;
    }

    $regions = array('any' => '- Any -');
    foreach ($this->region as $rid => $region) {
      $regions[$rid] = $region;
    }
    $data['region'] = $regions;

    $rms_companies = $this->cmc->rms_companies();
    $company = array('any' => '- Any -');
    foreach ($rms_companies as $rms_company) {
      $company[$rms_company['cid']] = $rms_company['company_code'];
    }
    $data['company'] = $company;

    $data['status'] = array(
      'For Doc Number' => 'For Doc Number',
      'For Deposit' => 'For Deposit',
      'Deposited' => 'Deposited'
    );

    $filter = [];
    if ($this->input->post('search')) {
      $filter = $this->input->post();
    }

    $data['batches'] = $this->ric->ric_batch($filter);
    $this->header_data('title', 'Penalty Request for Issuance of Check');
    $this->header_data('nav', 'ric');
    $this->template('ric/monitoring', $data);
  }

  public function penalty() {
    if ($this->input->post('PENALTY')) {
      $ep_ids = $this->input->post('PENALTY')['epids'];
      $this->form_validation->set_rules('PENALTY[ric_number]', 'RIC Reference Number', 'required');
      foreach ($ep_ids as $key => $epid) {
        $this->form_validation->set_rules('PENALTY[epids]['.$key.']', 'Reference checkbox', 'required');
      }
      $company_count = $this->ric->company_count($ep_ids);
      if (count($company_count) !== 1) {
        $_SESSION['warning'][] = "Please choose one company only.";
      } else {
        if ($this->form_validation->run()) {
          $status = $this->ric->create_ric($this->input->post());
          if ($status) {
            $reference = $this->input->post('PENALTY')['ric_number'];
            $_SESSION['messages'][] = "Request for Issuance Check has been made successfully!!";
            $_SESSION['messages'][] = "RIC Reference# ".$reference;
          }
        }
      }
      redirect($_SERVER["HTTP_REFERER"]);
    }

    $data['batches'] = $this->ric->get_batch_for_ric();

    $this->header_data('title', 'Penalty Request for Issuance of Check');
    $this->header_data('nav', 'ric');
    $this->template('ric/penalty', $data);
  }

  public function update() {
    if ($this->input->post()) {
      $data = $this->input->post();
      $response = [];
      switch ($_SESSION['position_name']) {
        case 'Accounts Payable Clerk':
          $data['dt_doc_num_encoded'] = date('Y-m-d H:m:s');
          break;
        case 'Treasury Assistant':
          $data['dt_dm_encoded'] = date('Y-m-d H:m:s');
          if (empty($this->input->post('date_deposited'))) {
           $data['date_deposited'] = date('Y-m-d');
          }
          $response['date_deposited'] = $data['date_deposited'];
          break;
      }
      $status = $this->db->update('tbl_ric', $data, 'ric_id='.$data['ric_id']);
      $response['success'] = $status;

      echo json_encode($response);
    } else {
      show_404();
    }
  }

  public function download($ric_id) {
    list($data['reference'], $data['data']) = $this->ric->download($ric_id);
    $this->load->view('ric/download', $data);
  }

  public function list() {
    if ($this->input->post('ric_id')) {
      echo json_encode($this->ric->data_in_ric($this->input->post('ric_id')));
    }
  }

}
