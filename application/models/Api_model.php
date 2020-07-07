<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model {
  function __construct() {
    parent::__construct();
    if (!$this->session->has_userdata('username')) {
      show_404();
    }
  }

  function get_engine_data() {
    if ($this->input->get('engine')) {
      $where_param['e.engine_no'] = $this->input->get('engine');

      if ($this->input->get('status')) {
        $where_param['st.status'] = $this->input->get('status');
      }

      if ($this->input->get('payment_method')) {
        $where_param['s.payment_method'] = $this->input->get('payment_method');
      }

      if ($this->input->get('region')) {
        $where_param['r.region'] = $this->input->get('region');
      }

      if ($this->input->get('company_id')) {
        $where_param['com.cid'] = $this->input->get('company_id');
      }

      $this->db->select('s.*, c.*, e.*, com.*, r.region AS region_name, st.status AS status_name');
      $this->db->from('tbl_sales s');
      $this->db->join('tbl_customer c', 's.customer = c.cid', 'inner');
      $this->db->join('tbl_engine e', 's.engine = e.eid', 'inner');
      $this->db->join('tbl_company com', 's.company = com.cid', 'inner');
      $this->db->join('tbl_region r', 's.region = r.rid', 'inner');
      $this->db->join('tbl_sales_status st', 's.status = st.ssid', 'inner');
      $this->db->where($where_param);

      return $this->db->get()->result_array();
    }
  }
}

