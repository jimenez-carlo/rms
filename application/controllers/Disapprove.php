<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disapprove extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Disapprove_model', 'disapprove');
    $this->load->model('Orcr_checking_model', 'orcr_checking');
    $this->load->model('Topsheet_model', 'topsheet');
  }


  public function index()
  {
    $this->access(1);
    $this->header_data('title', 'Disapprove List');
    $this->header_data('nav', 'disapprove');
    $this->header_data('dir', './');

    $param = new Stdclass();
    $param->region = $this->session->region;
    $param->branch = $this->input->post('branch');

    $data['branch'] = $this->disapprove->branch_list($param);
    $data['table'] = $this->disapprove->load_list($param);
    $data['da_reason'] = $this->disapprove->da_reason();
    $this->template('disapprove/list', $data);
  }

  public function sales()
  {
    $sales = new Stdclass();
    $sales->sid = $this->input->post('sid');
    $sales->da_reason = $this->input->post('da_reason');

    $this->db->trans_start();
    $this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));
    $new_da_history = array(
      'sales_id' => $sales->sid,
      'da_status_id' => $sales->da_reason,
      'uid' => $_SESSION['uid']
    );
    $this->db->insert('tbl_da_history', $new_da_history);
    $this->db->trans_complete();

    if ($this->db->trans_status() === TRUE) {
      $da = $this->disapprove->da_reason();
      $sales->da_reason = $da[$sales->da_reason];
      echo json_encode($sales->da_reason);
    }
  }

  public function resolve()
  {
    $this->access(1);
    $this->header_data('title', 'Disapprove List');
    $this->header_data('nav', 'disapprove');
    $this->header_data('dir', './');

    $param = new Stdclass();
    $param->region = $this->session->region;
    $param->branch = $this->input->post('branch');

    $data['branch'] = $this->disapprove->branch_list($param);
    $data['table'] = $this->disapprove->get_da_resolve();
    $data['da_reason'] = $this->disapprove->da_reason();
    $this->template('disapprove/resolve', $data);
  }

  public function save_resolve()
  {
    $sales_ids = $this->input->post('sales_ids');
    $this->db->trans_start();
    foreach ($sales_ids as $sale_id) {
      $this->orcr_checking->check_sales($sale_id);

      $this->db->where('sid', $sale_id);
      $this->db->update('tbl_sales', ['da_reason' => '0']);
      $this->db->insert('tbl_da_history', $this->disapprove->da_status_history($sale_id));
    }

    $this->db->select('DISTINCT(topsheet)');
    $this->db->from('tbl_sales');
    $this->db->where_in('sid', $sales_ids);
    $topsheet_ids = $this->db->get()->result_array();
    foreach ($topsheet_ids as $topsheet_id) {
      $this->topsheet->check_sales($topsheet_id['topsheet']);
    }
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      $this->session->set_flashdata('warning', 'Something went wrong.');
    } else {
      $tid = [];
      foreach ($topsheet_ids as $topsheet_id) {
        $tid[] = $topsheet_id['topsheet'];
      }
      $this->db->select("
        GROUP_CONCAT(
          CONCAT('Transaction # ', trans_no, ' updated successfully.')
          SEPARATOR ','
        ) AS trans_no
      ");
      $this->db->from('tbl_topsheet');
      $this->db->where_in('tid', $tid);
      $trans_no = $this->db->get()->row()->trans_no;
      $trans_no = explode(',', $trans_no);
      $this->session->set_flashdata('messages', $trans_no);
    }
  }

  public function misc_expense() {
    $this->disapprove->da_misc_expense($this->input->post());
  }
}
