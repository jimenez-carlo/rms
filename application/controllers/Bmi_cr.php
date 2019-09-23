<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bmi_cr extends MY_Controller {

  public function __construct() {
     parent::__construct();
     $this->load->helper('url');
     $this->andCompany = ($this->session->company === '8') ? ' AND company = 8' : ' AND company != 8';
  }

  public function index()
  {
    $this->access(1);
    $this->header_data('title', 'BMI CR Transmittal');
    $this->header_data('nav', 'bmi_cr');
    $this->header_data('dir', './');

    $data['count'] = $this->db->query("
      SELECT
        count(sid) AS count FROM tbl_sales
      LEFT JOIN
        tbl_bmi_cr ON sales = sid
      WHERE
        status > 3 AND bcid IS NULL {$this->andCompany}
    ")->row()->count;

    $data['table'] = $this->db->query("
      SELECT
        batch_no,
        max(extract_date) AS extract_date,
        count(sales) as count
      FROM
        tbl_bmi_cr bc
      GROUP BY batch_no
      ORDER BY max(extract_date) DESC LIMIT 100
    ")->result_object();

    $this->template('bmi_cr/extract', $data);
  }

  public function csv($batch_no = 0)
  {
    $extract = $this->input->post('extract');
    if (empty($extract)) redirect('bmi_cr');

    $batch_no = $this->input->post('batch_no');
    if (empty($batch_no)) {
      // get next batch
      $batch_no = $this->db->query("select ifnull(max(batch_no), 0)+1 as batch_no from tbl_bmi_cr")->row()->batch_no;

      // extract to tbl
      $this->db->query("insert into tbl_bmi_cr
        select 0, ".$batch_no.", sid, CURRENT_TIMESTAMP
        from tbl_sales
        left join tbl_bmi_cr on sales = sid
        where status > 3 and bcid is null
        order by sid limit 5000");
    }

    $data['result'] = $this->db->query("select trim(concat(ifnull(first_name, ''), ' ', ifnull(last_name, ''))) as name, cust_code, left(date_sold, 10) as date_sold, engine_no, chassis_no, cr_no, left(cr_date, 10) as cr_date, '' as bmi_date from tbl_sales
      inner join tbl_customer on cid = customer
      inner join tbl_engine on eid = engine
      inner join tbl_bmi_cr on sales = sid
      where batch_no = ".$batch_no)->result_array();

    $this->load->view('bmi_cr/csv', $data);
  }
}


