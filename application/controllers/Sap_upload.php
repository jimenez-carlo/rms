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
                        $subid = current(array_keys($save));
                        $this->liquidate($subid);
                }

                $data['table'] = $this->batch->list_for_upload();
                $this->template('sap_upload/list', $data);
        }

        public function sap($subid)
        {
                $data = $this->batch->sap_upload($subid);
                $this->load->view('sap_upload/sap', $data);
        }

        public function liquidate($subid)
        {
                $this->form_validation->set_rules('doc_no', 'Document #', 'required');

                if ($this->form_validation->run() == TRUE) {
                        $this->save_liquidated($subid);
                }
        }

        private function save_liquidated($subid)
        {
                $misc_exp = $this->input->post('misc_exp');

                $batch = new Stdclass();
                $batch->subid = $subid;
                $batch->doc_no = $this->input->post('doc_no');
                $batch->download_date = date('Y-m-d H:i:s');
                $batch->is_uploaded = 1;
                $this->db->trans_start();
                $batch = $this->batch->liquidate_batch($batch);

                if ($misc_exp) {
                  $this->batch->liquidate_misc_exp($misc_exp);
                }
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                  $_SESSION["messages"][] = 'Document Number '.$batch->doc_no.' for Transaction # '.$batch->trans_no.' was saved successfully.';
                } else {
                  $_SESSION["warning"][] = 'Something went wrong.';
                }
        }
}
