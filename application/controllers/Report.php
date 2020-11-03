<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends My_Controller {
  function __construct() {
    parent::__construct();
    $this->load->model('Report_model', 'report');
  }

  function index() {
    $this->header_data('title', 'Accounting Report');
    $this->header_data('nav', 'acctg_report');
    if ($this->input->post('generate') === "true") {
      switch ($this->input->post('payment_method')) {
        case 'EPP':
          $report_data = $this->report->acctg_epp([
            "from" => $this->input->post('date_from'),
            'to' => $this->input->post('date_to')
          ]);
          break;

        case 'CASH':
          $report_data = $this->report->acctg_cash([
            "from" => $this->input->post('date_from'),
            'to' => $this->input->post('date_to')
          ]);
          break;
      }
    }
    $this->template('report/acctg');
  }
}
