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
    $data['dep_slip_option'] = array('select' => '--Select--', 'Original' => 'Original', 'Not Original' => 'Not Original');

    if ($this->input->post()) {
      $actual_docs_updated = $this->actual_docs->update($this->input->post());
      echo json_encode($actual_docs_updated);
      exit;
    }

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
      $return_status = $this->actual_docs->save($this->input->post());
      if ($return_status) {
        $message = 'Transaction saved successfully.';
        $alert = 'alert-success';
      } else {
        $message = 'Something wen\'t wrong.';
        $alert = 'alert-error';
      }
    } else {
      $message = validation_errors();
      $alert = 'alert-error';
    }

    echo json_encode([
      "message" =>
        "<div class='alert ".$alert."'>
          <button type='button' class='close' data-dismiss='alert'>&times;</button>
          <p>".$message."</p>
        </div>"
    ]);
  }
}
