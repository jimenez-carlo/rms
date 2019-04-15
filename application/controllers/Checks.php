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

		$param = new Stdclass();
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->status = $this->input->post('status');

		$unhold = $this->input->post('unhold');
		if(!empty($unhold))
		{
			$cid = current(array_keys($unhold));
			$check = $this->check->unhold($cid);
			$_SESSION["messages"][] = "Check # ".$check->check_no." was updated successfully.";
		}

		$data['table'] = $this->check->list_checks($param);
		$data['status'] = $this->check->status;
		$this->template('checks/list', $data);
	}

	public function hold()
	{
		$this->form_validation->set_rules('reason', 'Reason', 'required');

    if ($this->form_validation->run() == TRUE)
    {
			$check = new Stdclass();
			$check->cid = $this->input->post('cid');
			$check->reason = $this->input->post('reason');
			$check = $this->check->hold($check);

			$_SESSION['messages'][] = 'Check # '.$check->check_no.' was updated successfully.';
			echo json_encode(array("status" => TRUE));
    }
    else
    {
    	echo json_encode(array("status" => FALSE, "message" => validation_errors()));
    }
	}
}
