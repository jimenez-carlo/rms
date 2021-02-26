<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Liquidation extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Liquidation_model', 'liquidation');
    $this->load->model('Orcr_checking_model', 'orcr_checking');
  }

  public function index()
  {
    $this->access(1);
    $this->header_data('title', 'Liquidation');
    $this->header_data('nav', 'liquidation');
    $this->header_data('dir', './');

    $param = new Stdclass();
    $param->date_from = $this->input->post('date_from');
    $param->date_to = $this->input->post('date_to');
    $param->region = (isset($_SESSION['region_id'])) ? $_SESSION['region_id'] : $this->input->post('region');

    $data['region'] = $this->liquidation->region;
    $data['table'] = $this->liquidation->load_list($param);
    $this->template('liquidation/list', $data);
  }

  public function sales($vid = 0)
  {
    $vid = (!empty($vid)) ? $vid : $this->input->post('vid');
    if (empty($vid)) redirect('liquidation');

    $this->access(1);
    $this->header_data('title', 'Liquidation');
    $this->header_data('nav', 'liquidation');
    $this->header_data('dir', './../../');

    $data['vid'] = $vid;
    $data['table'] = $this->liquidation->load_sales($vid);
    $this->template('liquidation/sales', $data);
  }

  public function csv($vid = 0)
  {
    $vid = (!empty($vid)) ? $vid : $this->input->post('vid');
    if (empty($vid)) redirect('liquidation');

    $data['vid'] = $vid;
    $data['table'] = $this->liquidation->load_sales($vid);
    $this->load->view('liquidation/csv', $data);
  }

  public function rrt()
  {
    $this->access(1);
    $this->header_data('title', 'Liquidation');
    $this->header_data('nav', 'liquidation');
    $this->header_data('dir', './../');

    $date_transferred = $this->input->post('date_transferred');
    $search = $this->input->post('search');

    if(!empty($search))
    {
      $data['table'] = $this->liquidation->load($_SESSION['region_id'], $date_transferred);
    }
    $data['company'][1] = 'MNC';
    $data['company'][3] = 'HPTI';
    $data['company'][6] = 'MTI';

    $this->template('liquidation/rrt', $data);
  }

  public function topsheets()
  {
    $this->access(1);
    $this->header_data('title', 'Liquidated Topsheets');
    $this->header_data('nav', 'liquidation');
    $this->header_data('dir', './../');
    $this->header_data(
      'link',
      '<link href="./../assets/DT_bootstrap.css" rel="stylesheet" media="screen">'
    );
    $this->footer_data(
      'script',
      '<script src="./../assets/modal/liquidation.js"></script>
      <script src="./../vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="./../assets/DT_bootstrap.js"></script>
      <script>
        $(function(){
          $(".table").dataTable({
            "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
             "sPaginationType": "bootstrap",
             "oLanguage": {
                     "sLengthMenu": "_MENU_ records per page"
             },
             "bSort": false,
             "bFilter": false,
             "iDisplayLength": 5,
             "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
          });
        });
      </script>'
    );

    $data['table'] = $this->orcr_checking->get_list_for_checking("liq");
    $data['page_title'] = "Liquidated Topsheets for Today";
    $this->template('orcr_checking/list', $data);
  }
}
