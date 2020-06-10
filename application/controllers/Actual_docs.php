<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Actual_docs extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Actual_docs_model', 'actual_docs');
    $this->load->model('Cmc_model', 'cmc');
  }

  public function index() {
    $this->header_data('title', 'Actual Docs');
    $this->header_data('nav', 'actual_docs');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/actual_docs.js?v1.0.0"></script>');
    $status = array('Pending' => 'Pending', 'Incomplete' => 'Incomplete', 'Resend' => 'Resend', 'Complete' => 'Complete');
    if ($_SESSION['position_name'] === 'RRT General Clerk') {
      $status = array_merge(array('New' => 'New'), $status);
    }
    $data['status'] = $status;
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
    $search = $this->input->post();
    $data['references'] = $this->actual_docs->get_batch($search);
    $data['dep_slip_option'] = array(0 => '--Select--', 'Original' => 'Original', 'Not Original' => 'Not Original');

    $this->template('actual_docs/index.php', $data);
  }

  public function save_transmittal_number() {
    $response = array('actual_docs' => false);
    $config = array(
      array(
        'field' => 'transmittal_number',
        'label' => 'Transmittal Number',
        'rules' => 'required'
      )
    );
    $this->form_validation->set_rules($config);

    if ($this->form_validation->run()) {
      $return = $this->actual_docs->save($this->input->post());
      if ($return['status']) {
        $message = 'Transaction saved successfully.';
        $alert = 'alert-success';
        $response['actual_docs'] = $return['data'];
      } else {
        $message = 'Something wen\'t wrong.';
        $alert = 'alert-error';
      }
    } else {
      $message = validation_errors();
      $alert = 'alert-error';
    }

    $response['message'] =
    "<div class='alert ".$alert."'>
      <button type='button' class='close' data-dismiss='alert'>&times;</button>
      <p>".$message."</p>
    </div>";

    echo json_encode($response);
  }

  public function update_status() {
    $actual_docs_updated = [
      'actual_doc' => $this->actual_docs->update($this->input->post()),
      'message' =>
      "<div class='alert alert-success'>
      <button type='button' class='close' data-dismiss='alert'>&times;</button>
      <p>Transaction saved successfully.</p>
      </div>"
    ];

    echo json_encode($actual_docs_updated);
  }

}
