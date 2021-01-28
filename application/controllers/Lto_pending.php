<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lto_pending extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Lto_pending_model', 'lto_pending');
    $this->load->model('Sales_model', 'sales');
    $this->load->model('Status_model', 'status');
    $this->lto_reason = $this->status->get([
      'status_type' => 'LTO_REASON',
      'format'=>'ARRAY'
    ]);
  }

  public function index()
  {
    $this->access(11);
    $this->header_data('title', 'LTO Pending');
    $this->header_data('nav', 'pending');
    $this->header_data('dir', './');

    $ltid = $this->input->post('ltid');
    if (empty($ltid))
    {
      $data['table'] = $this->lto_pending->load_list($_SESSION['region_id']);
      $this->template('lto_pending/list', $data);
    }
  }

  public function view($ltid)
  {
    if ($this->input->post('submit')) {
      $sales = $this->input->post('sales');
      $no_error = true;
      foreach ($sales AS $key => $sale)
      {
        // require reason field if rejected is chosen
        if ($sale['action'] == 'REJECT' && $sale['lto_reason'] == 0)
        {
          $engine = $this->sales->get_engine($sale['sid']);
          $this->form_validation->set_rules(
            "sales[{$key}][lto_reason]",
            'Lto Reason',
            'is_natural_no_zero',
            [ 'is_natural_no_zero' => 'Reason for rejection for Engine # '.$engine.' is required.'  ]
          );
          $no_error = $this->form_validation->run();
        }
      }

      if($no_error) {
        $this->submit_save();
      }
    }

    if (isset($ltid) && is_numeric($ltid)) {
      $this->access(11);
      $this->header_data('title', 'View LTO Pending');
      $data['transmittal'] = $this->lto_pending->load_transmittal($ltid);
      $data['reasons'] = $this->lto_reason;
      $this->template('lto_pending/view', $data);
    } else {
      redirect('lto_pending');
    }
  }

  public function submit_save()
  {
    $ltid = $this->input->post('ltid');
    $sales = $this->input->post('sales');
    foreach ($sales AS $sale) {
      switch ($sale['action']) {
        case 'CASH':
        case 'EPP':
          $sales_status = 2;
          $payment_method = $sale['action'];
          break;
        case 'REJECT':
          $sales_status = 1;
          $payment_method = NULL;
          break;
        default:
          $sales_status = 0;
          $payment_method = NULL;
          break;
      }

      $update_sale = new Stdclass();
      $update_sale->sid = $sale['sid'];
      $update_sale->status = $sales_status;
      $update_sale->lto_reason = $sale['lto_reason'];
      $update_sale->payment_method = $payment_method;
      $this->sales->save_lto_pending($update_sale);
    }

    $transmittal = $this->db->query("SELECT * FROM tbl_lto_transmittal WHERE ltid = ".$ltid)->row();
    $_SESSION['messages'][] = "Transmittal # ".$transmittal->code." updated successfully.";
    redirect("lto_pending");
  }
}
