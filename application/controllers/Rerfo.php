<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rerfo extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Rerfo_model', 'rerfo');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Rerfo');
		$this->header_data('nav', 'rerfo');
		$this->header_data('dir', './');

		$param = new Stdclass();
		$param->region = $_SESSION['region'];
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->branch = $this->input->post('branch');
		$param->status = $this->input->post('status');
		$param->print = $this->input->post('print');

		$data['branches'] = $this->rerfo->dd_branches($_SESSION['region']);
		$data['table'] = $this->rerfo->list_rerfo($param);
		$this->template('rerfo/list', $data);
	}

	public function view($rid = null)
	{
		$dir = base_url();
		$this->access(1);
		$this->header_data('title', 'Rerfo');
		$this->header_data('nav', 'rerfo');
		$this->header_data('dir', $dir);

		$view = $this->input->post('view');
		$rid = (!empty($view)) ? current(array_keys($view)) : $rid;
		$rid = (!empty($rid)) ? $rid : $this->input->post('rid');
		if (empty($rid)) redirect('rerfo');

		$save = $this->input->post('save');
		if (!empty($save)) {
			$check = $this->input->post('check');
			$this->rerfo->save_validated($rid, $check);
			$_SESSION['messages'][] = 'Changes updated successfully.';
		}

		$data['rerfo'] = $this->rerfo->load($rid);
		$this->template('rerfo/view', $data);
	}

	public function sprint()
	{
		$print = $this->input->post('print');
		if (empty($print)) redirect('rerfo');
		$rid = current(array_keys($print));

		$data['rerfo'] = $this->rerfo->print_rerfo($rid);
		$this->load->view('rerfo/print', $data);
	}

	public function request($rid)
	{
		$request = $this->input->post('request');
		if (empty($request)) redirect('rerfo');
		$rid = current(array_keys($request));

    if ($this->rerfo->request_reprint($rid)) {
    	$_SESSION['messages'][] = 'Request for reprint sent.';
    }
    else {
    	$_SESSION['warning'][] = 'Request for reprint already sent.';
    }
    redirect('rerfo');
	}
}
