<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repo extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Sales_model', 'sales');
  }

  public function index() {

    $this->header_data('title', 'Repo Registation');
    $this->header_data('nav', 'repo-registration');
    $this->footer_data('script', '<script src="assets/js/repo_registration.js?v1.0.0"></script>');

    $this->template('repo-registration/repo-in.php', ['data' => 'Repo']);

  }

  public function get_sales() {
    if($this->input->post('engine_no')) {
      $select_clause = <<<SELECT
        s.sid, s.cr_no, s.mvf_no, e.*, c.*,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name
SELECT;
      $where_clause = 'e.engine_no = "'.$this->input->post("engine_no").'" AND status >= 4';
      $sales = $this->sales->get_sales($select_clause, $where_clause);
      echo json_encode($sales);
    }
  }


}


