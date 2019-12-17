<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reprint extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
  }

  public function index()
  {
    redirect('reprint/topsheet');
  }

  public function topsheet()
  {
    $this->access(1);
    $this->header_data('title', 'Approve Tospheet');
    $this->header_data('nav', 'reprint');
    $this->header_data('dir', './../');

    $this->load->model('Topsheet_model', 'topsheet');

    $approve = $this->input->post('approve');
    if (!empty($approve))
    {
      foreach ($approve as $key => $val) {
        $this->topsheet->approve_printing($key,0);
      }
    }

    $data['table'] = $this->topsheet->get_topsheet_request();
    $this->template('reprint/topsheet', $data);
  }

  public function rerfo()
  {
    $this->access(1);
    $this->header_data('title', 'Approve Rerfo');
    $this->header_data('nav', 'reprint');
    $this->header_data('dir', './../');

    $this->load->model('Rerfo_model', 'rerfo');

    $approve = $this->input->post('approve');
    if (!empty($approve))
    {
      foreach ($approve as $key => $val) {
        $this->rerfo->approve_printing($key);
      }
    }

    $data['table'] = $this->rerfo->get_rerfo_request();
    $this->template('reprint/rerfo', $data);
  }
}
