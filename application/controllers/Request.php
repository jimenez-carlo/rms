<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request extends MY_Controller { //CI  Controller MY_Controller
  function __construct() {
    parent::__construct();
    $this->load->model('Request_model', 'request');
  }

  function index() {

    if (isset($_POST['action']) && $_POST['action'] == 'view_plate') {
      $data['record']    = $this->request->get_plate($_POST['plate_no']);
      $this->load->view('modal/repo/view_plate', $data);
    }
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
    if (isset($_POST['action']) && $_POST['action'] == 'view_repo_sales') {
      $data['record']  = $this->request->view_repo_sale();
      $data['status']    = $this->request->sales_disapprove_status();
      $this->load->view('modal/repo/view_repo_sales', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'create_repo_return_fund') {
      $id = $this->input->post('batch_id');
      $data['record']  = $this->request->get_batch($id);
      $this->load->view('modal/repo/add_repo_return_fund', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'create_repo_branch_tip') {
      $data['dropdown']  = $this->request->get_repo_branch_tip_not_exists();
      $this->load->view('modal/repo/add_repo_branch_tip',$data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'edit_repo_branch_tip') {
      $data['record']    = $this->request->get_repo_branch_tip($_POST['branch']);
      $data['dropdown']  = $this->request->get_repo_branch_tip_not_exists();
      $this->load->view('modal/repo/edit_repo_branch_tip',$data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'edit_repo_return_fund') {
      $id = $this->input->post('return_fund_id');
      $record = $this->request->get_return_fund($id);
      $data['record']  = $record;
      $data['batch']   = $this->request->get_batch($record->repo_batch_id);
      $data['dropdown']= $this->request->repo_fund_change_status();
      $this->load->view('modal/repo/edit_repo_return_fund', $data);
    }

    if (isset($_POST['action']) && $_POST['action'] == 'view_repo_for_checking') {
      $id = $this->input->post('repo_batch_id');
      if (!empty($id)) {
        $data['misc']  = $this->request->get_batch_misc($id);
        $data['sales'] = $this->request->get_batch_sales($id);
        $data['record'] = $this->request->get_batch($id);
        $data['repo_batch'] = $id;
        $data['checked_amount'] = $this->request->get_checked($id);
        $data['liquidated_amount'] = $this->request->get_liquidated($id);
        
        $this->load->view('repo/acctg/view_for_checking', $data);
      }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'view_repo_preview_summary') {
      $id = $this->input->post('repo_batch_id');
      if (!empty($id)) {
        $data['misc']   = $this->request->get_misc_array();
        $data['sales']  = $this->request->get_sales_array();
        $data['record'] = $this->request->get_batch($id);
        $data['repo_batch'] = $id;
        $data['sales_ids'] = $this->request->get_post_ids($_POST['sales'] ?? array());
        $data['misc_ids'] = $this->request->get_post_ids($_POST['misc'] ?? array());
        $data['checked_amount'] = $this->request->get_checked($id);
        $data['liquidated_amount'] = $this->request->get_liquidated($id);
        $this->load->view('repo/acctg/view_summary', $data);
      }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'view_repo_misc') {
      $data['record']       = $this->request->view_repo_misc();
      $data['status']       = $this->request->repo_misc_change_status();
      $data['expense_type'] = $this->request->expense_type();
      $this->load->view('modal/repo/view_repo_misc', $data);
    }

    if (isset($_POST['action']) && $_POST['action'] == 'view_repo_matrix_table') {
      $data['table'] = $this->request->view_repo_matrix_table();
      $this->load->view('repo/list/tip', $data);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'update_branch_tip') {
      echo $this->request->update_branch_tip();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'save_repo_branch_tip') {
      echo $this->request->save_repo_branch_tip();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'save_for_checking') {
      echo $this->request->save_for_checking();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'reject_repo_misc') {
      echo $this->request->reject_repo_misc();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'submit_repo_sale') {
      echo $this->request->submit_repo_sale();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'resolve_repo_sale') {
      echo $this->request->update_repo_sale();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'reject_repo_sale') {
      echo $this->request->reject_repo_sale();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'resolve_repo_misc') {
      echo $this->request->update_repo_misc();
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
  function table(){
      $data['table']   = $this->request->test_table();
      $this->template('test', $data);
      // $this->load->view('test', $data);
  }
}
