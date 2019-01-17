<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checks extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'url'));
    $this->load->library('form_validation');
    $this->load->model('Check_model', 'check');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Checks');
		$this->header_data('nav', 'fund');
		$this->header_data('dir', './');
		$this->footer_data('script', '
      <script src="./assets/modal/checks.js"></script>');

		$data = array();
		$search = $this->input->post('search');
		$check_no = $this->input->post('check_no');
		$company = $this->input->post('company');

		$lto = $this->input->post('lto');
		$hold = $this->input->post('hold');

		if(!empty($lto))
		{
			foreach ($lto as $key => $value) {
				$this->check->update_hold(0,$key);
				$_SESSION["messages"][] = "Check number successfully marked as LTO Pending.";
			}
		}

		if(!empty($search))
		{
			$data['checks'] = $this->check->get_checks($check_no,$company);
		}

		$this->template('checks/list', $data);
	}

	public function hold_check($cid)
	{
		$this->form_validation->set_rules('reason', 'Reason', 'required');

    if ($this->form_validation->run() == TRUE)
    {
			$this->check->update_hold(1,$cid);
			$hold = new Stdclass();
			$hold->check = $cid;
			$hold->hold_date = date('Y-m-d H:i:s');
			$hold->reason = $this->input->post('reason');
			$this->check->save_hold_reason($hold);

			$_SESSION['messages'][] = 'Check number successfully marked as hold.';
			echo json_encode(array("status" => TRUE));
    }
    else
    {
    	echo json_encode(array("status" => FALSE, "message" => validation_errors()));
    }
	}
}
