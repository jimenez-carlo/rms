<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request extends CI_Controller {
  function __construct() {
    parent::__construct();
    $this->load->model('Request_model', 'request');
  }

  function index() {
    if (isset($_POST['type']) && $_POST['type'] == 'edit_repo_misc') {
      $data['record']       = $this->request->view_repo_misc();
      $data['expense_type'] = $this->request->expense_type();
      $data['batch']        = $this->request->batch_dropdown();
      $this->load->view('modal/repo/edit_repo_misc', $data);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'edit_repo_sales') {
      $data['record']       = $this->request->view_repo_sale();
      $this->load->view('modal/repo/edit_repo_sales_amount', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'update_misc') {
      echo $this->request->update_misc();
      // print_r($_FILES);
    }
  }
}
