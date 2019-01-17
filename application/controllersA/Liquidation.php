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
		$this->footer_data('script', '
      <script src="assets/modal/liquidation.js"></script>');

		$data = array();
		$search = $this->input->post('search');
		$region = $this->input->post('region');
		$date_transferred = $this->input->post('date_transferred');

		if(!empty($search))
		{
			$data['table'] = $this->liquidation->load($region, $date_transferred);
			$data['company'][1] = 'MNC';
			$data['company'][2] = 'MTI';
			$data['company'][3] = 'HPTI';
		}

		$this->template('liquidation/view', $data);
	}

	public function sales($ftid)
	{
		$this->access(1);
		$this->header_data('title', 'Liquidation');
		$this->header_data('nav', 'liquidation');
		$this->header_data('dir', './../../');

		$data['table'] = $this->liquidation->load_sales($ftid);
		$this->template('liquidation/sales', $data);
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
			$data['table'] = $this->liquidation->load($_SESSION['region'], $date_transferred);
		}
		$data['company'][1] = 'MNC';
		$data['company'][2] = 'MTI';
		$data['company'][3] = 'HPTI';

		$this->template('liquidation/rrt', $data);
	}

	public function topsheets()
	{
		$this->access(1);
		$this->header_data('title', 'Liquidated Topsheets');
		$this->header_data('nav', 'liquidation');
		$this->header_data('dir', './../');
		$this->footer_data('script', '');
		$this->header_data('link', '
			<link href="./../assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
      <script src="./../assets/modal/liquidation.js"></script>
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
		</script>');

		$data['table'] = $this->orcr_checking->get_list_for_checking("liq");
		$data['page_title'] = "Liquidated Topsheets for Today";
		$this->template('orcr_checking/list', $data);
	}
}
