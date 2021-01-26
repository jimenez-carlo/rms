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

  public function reprint($ltid, $bcode) {
    $this->header_data('title', 'SI Reprint');
    $data["data"] = $this->si->reprint($ltid, $bcode);
    $this->header_data('title', 'SI Reprint');
    $this->header_data('nav', 'si');
    $this->template('si/reprint',$data);
  }

  public function transmittal() {
    $filter['bcode'] = $this->input->post('bcode');
    $filter['date_from'] = $this->input->post('date_from');
    $filter['date_to'] = $this->input->post('date_to');
    $transmittal = $this->si->get_transmittal($filter);
    $template = array(
      'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $data['table'] = $this->table->generate($transmittal);
    $this->header_data('title', 'Transmittal');
    $this->header_data('nav', 'si');
    $this->template('si/transmittal', $data);
  }

  public function self_regn() {
    if ($this->input->post('engine_ids')) {
      $engine_ids = $this->input->post('engine_ids');
      $this->db->trans_start();
      foreach($engine_ids AS $eid) {
        $this->si->update_si([
          'eid' => $eid,
          'pnp_status' => 1,
          'date_pnp_tag' => 'NOW()'
        ]);
      }
      $this->db->trans_complete();
      if ($this->db->trans_status()) {
        $_SESSION['messages'][] = 'Engine/s Tagged PNP Successfully!';
      } else {
        $_SESSION['warning'][] = 'Transaction not saved. Something wen\'t wrong!';
      }

      redirect($_SERVER['HTTP_REFERER']);
    }

    $self_regn = $this->si->self_regn();
    $template = array(
      'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $data['table'] = $this->table->generate($self_regn);
    $this->header_data('title', 'Self Registration');
    $this->header_data('nav', 'si');
    $this->template('si/self_regn', $data);
  }

  public function print_now() {
    $status = false;
    if ($this->input->post('bobj_sales_ids')) {
      $data['si_prints'] = $this->si->si_print_data($this->input->post('bobj_sales_ids'));
      $this->db->trans_start();
      foreach($data['si_prints'] as $si) {
        $this->si->update_si([
          'eid' => $si['eid'],
          'is_printed' => 1,
          'date_printed' => 'NOW()'
        ]);
      }
      $status = $this->db->trans_complete();
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

}
