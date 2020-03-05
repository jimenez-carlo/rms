<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sap_upload extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Batch_model', 'batch');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'For SAP Uploading');
		$this->header_data('nav', 'sap_upload');
		$this->header_data('dir', './');
		$this->footer_data('script', '<script src="./assets/modal/sap_upload.js"></script>');

		$save = $this->input->post('save');
		if(!empty($save))
		{
			$bid = current(array_keys($save));
			$this->liquidate($bid);
		}

		$data['table'] = $this->batch->list_for_upload();
                var_dump($this->db->last_query()); die();
		$this->template('sap_upload/list', $data);
	}

	public function sap($bid)
	{
		$data['batch'] = $this->batch->sap_upload($bid);
		$this->load->view('sap_upload/sap', $data);
	}

	public function liquidate($bid)
	{
		$this->form_validation->set_rules('doc_no', 'Document #', 'required');

		if ($this->form_validation->run() == TRUE) {
			$this->save_liquidated($bid);
		}
	}

	private function save_liquidated($bid)
	{
		$batch = new Stdclass();
		$batch->bid = $bid;
		$batch->doc_no = $this->input->post('doc_no');
		$batch->download_date = date('Y-m-d H:i:s');
		$batch = $this->batch->liquidate_batch($batch);

		$_SESSION["messages"][] = 'Document Number '.$batch->doc_no.' for Transaction # '.$batch->trans_no.' was saved successfully.';
	}
}
