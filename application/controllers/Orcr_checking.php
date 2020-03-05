<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking extends MY_Controller {

        public function __construct() {
                parent::__construct();
                $this->load->helper('url');
                $this->load->model('Orcr_checking_model', 'orcr_checking');
        }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'For Checking');
		$this->header_data('nav', 'orcr_checking');
		$this->header_data('dir', './');

		$data['tid'] = $this->input->post('tid');
		$data['vid'] = $this->input->post('vid');
		$data['sid'] = $this->input->post('sid');
		$data['mid'] = $this->input->post('mid');
		$data['summary'] = $this->input->post('summary');
		$data['submit_all'] = $this->input->post('submit_all');
		$data['back'] = $this->input->post('back');

		if (!empty($data['submit_all'])) {
                  $this->save($data);
                }

		if (!empty($data['back'])) {
                  $data['sid'] = null;
                  $data['mid'] = null;
		}

                if (!empty($data['vid'])) {
                  $data['ca_ref']  = $this->orcr_checking->load_ca($data);
                  //echo '<pre>';
                  //print_r($data['ca_ref']['sales']); exit;
                  //var_dump(json_decode($data['ca_ref']['sales'])); die();
                  $view = (!empty($data['summary'])) ? 'orcr_checking/summary' : 'orcr_checking/ca_ref';
                  $data['view'] = $this->load->view($view, $data, TRUE);
                }

                //if (!empty($data['tid'])) {
                //  $topsheet = $this->orcr_checking->load_topsheet($data);
                //  $data['topsheet'] = $topsheet;
                //  // echo '<pre>';
                //  // print_r($data['topsheet']); die();

                //  $view = (!empty($data['summary'])) ? 'orcr_checking/summary' : 'orcr_checking/topsheet';
                //  $data['view'] = $this->load->view($view, $data, TRUE);
                //}

                $data['ca_refs'] = $this->orcr_checking->get_ca_for_checking();
                // var_dump($data['ca_refs']); die();
		// $data['table'] = $this->orcr_checking->get_list_for_checking("");
		$this->template('orcr_checking/list', $data);
	}

	public function attachment()
	{
		$id = $this->input->post('id');
		$type = $this->input->post('type');

		switch ($type)
		{
			case 1:
				$data['sales'] = $this->orcr_checking->sales_attachment($id);
				$response['page'] = $this->load->view('orcr_checking/sales_attachment', $data, TRUE);
                                $response['disable'] = (!in_array($data['sales']->da_reason, array(0,11))) ? true : false;
				break;
			case 2:
				$data['misc'] = $this->orcr_checking->misc_attachment($id);
				$response['page'] = $this->load->view('orcr_checking/misc_attachment', $data, TRUE);
				$response['disable'] = ($data['misc']->status == 5) ? true : false;
				break;
		}

		echo json_encode($response);
	}

	public function save($data)
	{
		foreach ($data['sid'] as $sid) {
			$this->orcr_checking->check_sales($sid);
		}

		foreach ($data['mid'] as $mid) {
			$this->orcr_checking->check_misc($mid, $data['tid']);
		}

  	        // update topsheet status
  	        $this->load->model('Topsheet_model', 'topsheet');
  	        $topsheet = $this->topsheet->check_sales($data['tid']);

  	        $_SESSION['messages'][] = 'Transaction # '.$topsheet->trans_no.' updated successfully.';
  	        redirect('orcr_checking');
	}
}
