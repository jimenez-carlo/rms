<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Actual_docs extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Actual_docs_model', 'actual_docs');
  }

  public function index() {
    $this->footer_data('script', '<script src="'.base_url().'assets/js/actual_docs.js?v1.0.0"></script>');

    $data['references'] = $this->actual_docs->get_batch();
    $dep_slip_option = array(0 => '--Select--', 'Original' => 'Original', 'Not Original' => 'Not Original');
    $data['deposit_slip'] = form_dropdown('deposit_slip', $dep_slip_option);
    $this->template('actual_docs/index.php', $data);
  }

  public function save_transmittal_number() {
    $config = array(
      array(
        'field' => 'transmittal_number',
        'label' => 'Transmittal Number',
        'rules' => 'required'
      )
    );
    $this->form_validation->set_rules($config);

    if ($this->form_validation->run()) {
      $message = 'Transaction saved successfully';
      $alert = 'alert-success';
    } else {
      $message = validation_errors();
      $alert = 'alert-error';
    }

    echo json_encode([
      'message' =>
        "<div class='alert ".$alert."'>
          <button type='button' class='close' data-dismiss='alert'>&times;</button>
          <p>".$message."</p>
        </div>"
    ]);
  }
}
