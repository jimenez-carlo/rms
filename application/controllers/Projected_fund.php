<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projected_fund extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Projected_fund_model', 'projected_fund');
    $this->load->model('Js_model', 'js');
  }

  /**
   * Accounting to Create Voucher
   */
  public function index() {
    $this->access(1);
    $this->auto_create_ca_batch();
    $this->header_data('title', 'Projected Funds');
    $this->header_data('nav', 'projected_fund');
    $this->footer_data('script', '<script src="'.base_url().'assets/modal/projected_fund.js?v=1.0.1"></script>');

    $data['position'] = $_SESSION['position'];
    $data['table'] = $this->projected_fund->get_projected_funds();
    $this->template('projected_fund/list_projected', $data);
  }

  public function create_voucher() {
    $region_id = $this->input->post('region_id');
    $company_id = $this->input->post('company_id');
    $data['table'] = $this->projected_fund->create_voucher($company_id, $region_id);
    $view = $this->load->view('projected_fund/create_voucher', $data, TRUE);
    print $view;
  }

  public function sprint($vid) {
    if ($vid) {
      $data['ca_batch'] = $this->projected_fund->print_projected($vid);
      $this->load->view('projected_fund/print_projected', $data);
    }
  }

  public function save_voucher() {
    $response = '';
    if ($this->input->post('vid') && $this->input->post('voucher_no')) {
      $this->form_validation->set_rules('voucher_no', 'Document #', 'required');

      if ($this->form_validation->run() == FALSE) {
        $response = json_encode(array("status" => FALSE, "message" => validation_errors()));
      } else {
        $isSuccess = $this->projected_fund->save_voucher($this->input->post());

        $message['status'] = $isSuccess;
        $message['message'] = ($isSuccess) ? "Document Number {$this->input->post('voucher_no')} Save Successfully." : "Something went wrong.";
        $response = json_encode($message);
      }
    }
    echo $response;
  }

  /**
   * Accounting to view list of Voucher
   */
  public function ca_list()
  {
    switch ($_SESSION['position']) {
      case 34: // TRSRY-ASST
        $nav = 'deposited_fund';
        $data['def_stat'] = 2;
        break;

      case 3:   // ACCTG-PAYCL
        $nav = 'projected_fund';
        $data['def_stat'] = '';
        break;

      case 107: // RRT-MGR
      case 108: // RRT-SPVSR
        $nav ='ca_list';
        $data['def_stat'] = '';
        break;
    }

    $this->access(16);
    $this->header_data('title', 'Brand New CA List');
    $this->header_data('nav', $nav);
    $this->header_data('dir', './../');
    $this->footer_data('script', '<script src="./../assets/js/voucher_list.js"></script>');

    $param = new Stdclass;
    $param->date_from = $this->input->post('date_from') ?? date('Y-m-d');
    $param->date_to   = $this->input->post('date_to')   ?? date('Y-m-d');
    $param->status    = $this->input->post('status')    ?? $data['def_stat'];
    $param->region    = $this->input->post('region_id');

    $data['status'] = $this->projected_fund->status;
    $data['table']  = $this->projected_fund->list_voucher($param);
    $this->template('projected_fund/list_voucher', $data);
  }

  public function repo_ca_list() {
    $this->access(16);
    $this->header_data('title', 'Repo CA List');
    $this->header_data('dir', './../');

    $param = new Stdclass;
    $param->date_from = $this->input->post('date_from') ?? date('Y-m-d', strtotime('-1 day'));
    $param->date_to   = $this->input->post('date_to') ?? date('Y-m-d');
    $param->status    = $this->input->post('status');
    $param->region    = $this->input->post('region_id');
    $data['region_dropdown'] = json_decode(
      $this->db
      ->select("
        CONCAT('{',
          '\"0\": \"- Any -\",',
          GROUP_CONCAT('\"',rid,'\"', ':\"',region,'\"')
        ,'}') AS regions
      ")
      ->get('tbl_region')
      ->row_array()['regions'],
      true
    );

    $data['table']  = $this->projected_fund->repo_ca_list($param);
    $this->template('projected_fund/repo_ca_list', $data);
  }

  private function auto_create_ca_batch() {
    $ca_batch = $this->db
      ->where("DATE_FORMAT(date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')")
      ->get("tbl_voucher")
      ->num_rows();
    if (empty($ca_batch)) {
      $new_ca_batch  = $this->db
        ->distinct()
        ->select('
          s.company, f.fid AS fund,
          CONCAT(
            "CA-",UPPER(r.r_code),"-",
            DATE_FORMAT(NOW(), "%y%m%d"),
            CASE
              WHEN s.company = 3 THEN "-1"
              WHEN s.company = 6 THEN "-2"
              ELSE ""
            END
          ) AS reference,
          NOW() AS date,
          0 AS amount
        ')
        ->from('tbl_sales s')
        ->join('tbl_fund f', 'f.region = s.region', 'inner')
        ->join('tbl_region r', 'r.rid = s.region','inner')
        ->where('s.date_sold >= "2018-08-01 00:00:00"')
        ->where('s.registration_type <> "Self Registration"')
        ->where([
          's.payment_method' => 'CASH',
          's.voucher' => 0
        ])
        ->get()
        ->result_array();
      foreach ($new_ca_batch as $key => $new_ca) {
        $this->db->query("SET SESSION autocommit = 0");
        $this->db->query("
          LOCK TABLES
            tbl_fund AS f READ, tbl_region AS r READ,
            tbl_sales AS s WRITE, tbl_voucher AS v WRITE,
            tbl_voucher WRITE
        ");

        $new_ca['voucher_no'] = NULL;
        $this->db->insert('tbl_voucher', $new_ca);
        $vid = $this->db->insert_id();
        $this->db->query("
          UPDATE tbl_sales s, tbl_fund f
          SET s.voucher = {$vid}
          WHERE s.region = f.region
          AND s.voucher = 0
          AND s.payment_method = 'CASH'
          AND f.fid = {$new_ca['fund']}
          AND s.company = {$new_ca['company']}
        ");

        $sales_count = $this->db->affected_rows();
        $this->db->query("
          UPDATE tbl_voucher v
          SET v.amount = {$sales_count} * IF(v.company = 8, 1200, 900)
          WHERE vid={$vid}
        ");

        $this->db->query("COMMIT;");
        $this->db->query("UNLOCK TABLES");
      }
    }
  }

  public function ca_template() {
    $this->access(1);
    if ($ca_date = $this->input->post("date")) {
      $data['date'] = $ca_date;
      $data['data'] = $this->db
        ->select("
          DATE_FORMAT(v.date, '%Y-%m-%d') AS 'document_type',
          DATE_FORMAT(v.date, '%Y-%m-%d') AS 'posting_date',
          'KR' AS 'kr', CONCAT(s.company,'000') AS 'company_code',
          'PHP' AS 'php', v.reference, f.acct_number AS vendor,
          FORMAT(v.amount, 0) AS amount, CONCAT(s.company, '000000') AS profit_center,
          CONCAT(
            DATE_FORMAT(v.date, '%d/%m/%Y'),
            ' CA - ', COUNT(*), 'UNIT - ',
            CASE
              WHEN r.rid = 1 THEN r.region
              ELSE REPLACE(r.region, 'Region ', 'RRT')
            END
          ) AS description
        ")
        ->from("tbl_voucher v")
        ->join("tbl_fund f", "f.fid = v.fund", "inner")
        ->join("tbl_sales s", "s.voucher = v.vid", "inner")
        ->join("tbl_region r", "r.rid = s.region", "inner")
        ->where("DATE_FORMAT(v.date, '%Y-%m-%d') = '{$ca_date}'")
        ->where("v.company {$this->cc} 8")
        ->group_by("v.vid, s.company, s.region")
        ->get()
        ->result_array();
      $this->load->view("projected_fund/ca_template", $data);
    }
  }
}
