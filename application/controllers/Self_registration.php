<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Self_registration extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Sales_model', 'sales');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Self Registration');
		$this->header_data('nav', 'report');
		$this->header_data('dir', './');
		$this->header_data('link', '
			<link href="assets/DT_bootstrap.css" rel="stylesheet" media="screen">');
		$this->footer_data('script', '
			<script src="vendors/datatables/js/jquery.dataTables.min.js"></script>
      <script src="assets/DT_bootstrap.js"></script>
      <script>
			$(function(){
				var datatable = $(".table").dataTable({
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

		$data = array();
		$type = $this->input->post("type");
		$date_sold = $this->input->post("date_sold");

		if($type == "with") $data['sales'] = $this->sales->get_sr_with($date_sold);
		else if ($type == "without") $data['sales'] = $this->sales->get_sr_without($date_sold);

		$this->template('self_registration/list', $data);
	}

}
