<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request extends CI_Controller {
  function __construct() {
    parent::__construct();
    $this->load->model('Request_model', 'request');
  }

  function index() {

    if (isset($_POST['action']) && $_POST['action'] == 'edit_repo_misc') {
      $data['record']       = $this->request->view_repo_misc();
      $data['expense_type'] = $this->request->expense_type();
      $data['batch']        = $this->request->batch_dropdown();
      $this->load->view('modal/repo/edit_repo_misc', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'edit_repo_sales') {
      $data['record']  = $this->request->view_repo_sale();
      $this->load->view('modal/repo/edit_repo_sales_amount', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'create_repo_return_fund') {
      $id = $this->input->post('batch_id');
      $data['record']  = $this->request->get_batch($id);
      $this->load->view('modal/repo/add_repo_return_fund', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'edit_repo_return_fund') {
      $id = $this->input->post('return_fund_id');
      $record = $this->request->get_return_fund($id);
      $data['record']  = $record;
      $data['batch']   = $this->request->get_batch($record->repo_batch_id);
      $data['dropdown']= $this->request->repo_fund_change_status();
      $this->load->view('modal/repo/edit_repo_return_fund', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'resolve_repo_sale') {
      echo $this->request->update_repo_sale();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'resolve_repo_misc') {
      echo $this->request->update_repo_sale();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'add_repo_return_fund') {
      echo $this->request->insert_repo_return_fund();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'update_repo_return_fund') {
      if(isset($_POST['change_status'])){
        echo $this->request->change_status_repo_return_fund();
      }else{
        echo $this->request->update_repo_return_fund();
      }
    }
  }
}
