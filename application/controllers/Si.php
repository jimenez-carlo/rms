<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;

class Si extends MY_Controller {
  function __construct() {
    parent::__construct();
    $this->load->model('Si_model', 'si');
  }

  public function printing(){
    $data['bcode'] = $this->input->post('branch');
    if($this->input->post('search')){
      $data["data"] = $this->si->for_print_si($this->input->post('b_code'));
      $data["bcode"] = $this->input->post('b_code');
    }

    $this->header_data('title', 'SI Printing');
    $this->header_data('nav', 'si');
    $this->template('si/printing',$data);
  }

  public function print_now() {

    $status = false;
    if ($this->input->post('bobj_sales_ids')) {
      $data['si_prints'] = $this->si->si_print_data($this->input->post('bobj_sales_ids'));
      $this->db->trans_start();
      foreach($data['si_prints'] as $si) {
        $this->si->tag_si_printed($si['bobj_sales_id']);
      }
      $status = $this->db->trans_complete();
    } elseif ($this->input->post('transmittal_id')) {
      $data['si_prints'] = $this->si->get_reprint($this->input->post('transmittal_id'));
      $status = true;
    }

    if ($status !== false) {
      $si_html_raw = $this->load->view('si/print_template',$data, true);
      // instantiate and use the dompdf class
      $dompdf = new Dompdf;

      $dompdf->loadHtml($si_html_raw);

      // (Optional) Setup the paper size and orientation
      $dompdf->setPaper(array(0,0,612.00,468.00), 'portrait');

      // Render the HTML as PDF
      $dompdf->render();

      // Output the generated PDF to Browser
      $dompdf->stream('si_print.pdf', array('Attachment'=>false));

    } else {
      redirect($_SERVER['HTTP_REFERER']);
    }
  }

  public function reprint() {
    $data['title'] = "SI Re-Print";
    $data['si_print_active'] = true;
    $data['transmittal_no'] = $this->input->post('transmittal_no');
    $this->load->view('tpl/header',$data);//header
    if(isset($_POST['search'])){
      $rrt = $this->si->get_rrt_class($_SESSION['b_code']);
      $data["data"] = $this->si->get_transmittal_no($this->input->post('transmittal_no'),$rrt->rrt_class);
    }
    $this->load->view('printing/si_reprint',$data);
    $this->load->view('tpl/footer');//footer
  }

}
