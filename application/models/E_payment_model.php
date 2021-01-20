<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class E_payment_model extends CI_Model{

        public $status = array(
                1 => 'Pending',
                2 => 'Processing',
                3 => 'For Deposit',
                4 => 'Deposited',
                5 => 'Liquidated',
                );

        public function __construct()
        {
                parent::__construct();
                $this->compQuery = ($_SESSION['company'] == 8) ? " AND ep.company = ".$_SESSION['company'] : " AND ep.company != 8";
        }

        public function get_list($param) {

          if (empty($param->date_from)) $param->date_from = date('Y-m-d', strtotime('-5 days'));
          if (empty($param->date_to)) $param->date_to = date('Y-m-d');

          $region = (empty($param->region)) ? "" : " AND ep.region = ".$param->region;
          $status = (empty($param->status)) ? "" : " AND ep.status = ".$param->status;
          $reference = (empty($param->reference)) ? "" : " AND ep.reference LIKE '%".$param->reference."%'";

          $sql = <<<SQL
            SELECT
              ep.epid, ep.created, ep.region,
              ep.company, ep.reference, ep.ref_date,
              ep.amount + ep.addtl_amt AS ttl_amt,
              ep.amount, ep.addtl_amt, ep.doc_no,
              ep.dm_no, ep.confirmation, ep.status,
              ep.dm_date, ep.doc_date, ep.deposit_date,
              ep.close_date, ep.screenshot, ep.receipt,
              ep.ric_id, c.company_code AS company, r.region,
              FORMAT(
                (ep.amount+ep.addtl_amt+IFNULL(ric.amount,0))
                -SUM(IFNULL((s.registration+s.penalty+s.tip),0)
              ),2) AS pending_amt,
              FORMAT(SUM(
                IF(
                  (susb.sid IS NULL OR sub.doc_no IS NULL),
                  s.registration+s.tip+IF(ric.debit_memo IS NULL, s.penalty,0),
                  0
                )
              ),2) AS for_liq,
              FORMAT(SUM(
                IF(
                  sub.doc_no IS NOT NULL,
                  s.registration+s.tip+IF(
                    (s.is_penalty_for_ric=0) OR (s.is_penalty_for_ric=1 AND ric.debit_memo IS NOT NULL),
                    s.penalty,
                    0
                  ),
                  0
                )
              ),2) AS liquidated,
              ric.reference_num,
            FORMAT(SUM(IF(s.is_penalty_for_ric=1,s.penalty,0)),2) ric_penalty_amount
            FROM tbl_sales s
            LEFT JOIN tbl_sap_upload_sales_batch susb ON susb.sid = s.sid
            LEFT JOIN tbl_sap_upload_batch sub ON sub.subid = susb.subid
            INNER JOIN tbl_electronic_payment ep ON s.electronic_payment = ep.epid
            LEFT JOIN tbl_ric ric ON ric.ric_id = ep.ric_id
            INNER JOIN tbl_company c ON c.cid = ep.company
            INNER JOIN tbl_region r ON r.rid = ep.region
            WHERE ep.ref_date BETWEEN '$param->date_from' AND '$param->date_to'
            $region $status $reference $this->compQuery
            GROUP BY ep.epid, r.rid, c.cid, ric.ric_id
            ORDER BY created DESC limit 1000
SQL;

          return $this->db->query($sql)->result_object();
        }

        public function load_payment($epid) {
                $payment = $this->db->query("select * from tbl_electronic_payment where epid = ".$epid)->row();

                // SHOW 404 THIS CODE IS FOR MANUAL DELETION OF tbl_electronic_payment REF
                if($payment->status == 0){
                        show_404();
                        exit;
                }

                $payment->status = $this->status[$payment->status];
                $payment->sales = $this->db->query("
                  SELECT *
                  FROM tbl_sales s
                  INNER JOIN tbl_engine e ON s.engine = e.eid
                  INNER JOIN tbl_customer c ON s.customer = c.cid
                  INNER JOIN tbl_status st ON st.status_id = s.status AND status_type = 'SALES'
                  WHERE s.electronic_payment = ".$epid."
                  ORDER BY s.bcode ASC, s.sid ASC
                ")->result_object();
                return $payment;
        }

        public function upload_screenshot()
        {
                $this->load->library('upload');
                $config['allowed_types'] = 'pdf';
                $config['upload_path'] = './rms_dir/temp/';
                $config['max_size'] = '1024';
                $this->upload->initialize($config);

                if ($this->upload->do_upload('screenshot')) return $this->upload->data('file_name');

                $_SESSION['warning'][] = $this->upload->display_errors();
                return false;
        }

        public function upload_batch()
        {
                $this->load->library('upload');
                $config['allowed_types'] = 'csv';
                $config['upload_path'] = './rms_dir/temp/';
                $this->upload->initialize($config);

                if ($this->upload->do_upload('batch')) return $this->upload->data('file_name');

                $_SESSION['warning'][] = $this->upload->display_errors();
                return false;
        }

        public function save_payment($payment, $batch)
        {
                $payment->status = 1;
                $this->db->insert('tbl_electronic_payment', $payment);
                $payment->epid = $this->db->insert_id();

                $path = './rms_dir/temp/'.$batch;
                $file = fopen($path, 'r');
                $header = fgetcsv($file, 4096, ';', '"');

                while (($row = fgetcsv($file, 4096, ';', '"')) !== false) {
                        if (!empty($row)) {
                                $col = explode(',', $row[0]);
                                if (!empty($col[0])) {
                                        $this->db->query("update tbl_sales
                                                inner join tbl_engine on engine = eid
                                                set status = 3, electronic_payment = ".$payment->epid."
                                                where status = 2
                                                and region = ".$payment->region."
                                                and left(bcode, 1) = '".$payment->company."'
                                                and engine_no = '".$col[0]."'");
                                }
                        }
                }
                fclose($file);
                unlink($path);

                $_SESSION['messages'][] = 'E-Payment # '.$payment->reference.' saved successfully.';
                redirect('/electronic_payment/view/'.$payment->epid);
        }

        public function update_payment($payment, $remove, $engine_no)
        {
                $this->db->update('tbl_electronic_payment', $payment, array('epid' => $payment->epid));

                if (!empty($remove)) {
                        foreach ($remove as $sid) {
                                $this->db->query("update tbl_sales
                                        set status = 2, electronic_payment = 0
                                        where sid = ".$sid);
                        }
                }

                if (!empty($engine_no)) {
                        foreach ($engine_no as $row) {
                            $this->db->query("
                              UPDATE
                                tbl_sales
                              INNER JOIN
                                tbl_engine on engine = eid
                              SET
                                electronic_payment = ".$payment->epid.",
                                status      = 3
                              WHERE engine_no = '".$row."'
                            ");
                        }
                }

                $_SESSION['messages'][] = 'E-Payment # '.$payment->reference.' updated successfully.';
                redirect('/electronic_payment/view/'.$payment->epid);
        }

        public function get_list_by_status($status) {
          $company = ($_SESSION['company'] != 8) ? ' AND company != 8' : ' AND company = 8';
          return $this->db->query("
            SELECT
              epid, created, region, company, reference, ref_date,
              amount, addtl_amt, amount+addtl_amt AS ttl_amt,
              doc_no, dm_no, confirmation, status, dm_date, doc_date,
              deposit_date, close_date, screenshot, receipt, ric_id
            FROM tbl_electronic_payment
            WHERE status = ".$status.$company."
            ORDER BY created DESC
          ")->result_object();
        }

        public function update_payment_status($payment, $status)
        {
                $payment->status = $status;
                $this->db->update('tbl_electronic_payment', $payment, array('epid' => $payment->epid));
        }

        public function upload_receipt($epid)
        {
                $this->load->library('upload');
                $config['allowed_types'] = 'pdf';
                $config['upload_path'] = './rms_dir/temp/';
                $config['max_size'] = '1024';
                $this->upload->initialize($config);

                if ($this->upload->do_upload('receipt_'.$epid)) return $this->upload->data('file_name');

                $_SESSION['warning'][] = $this->upload->display_errors();
                return false;
        }

        public function deposit_payment($payment) {
                $payment->status = 4;
                $this->db->update('tbl_electronic_payment', $payment, array('epid' => $payment->epid));
        }

        public function get_liquidation_list() {
          $sql = <<<SQL
            SELECT
              ep.epid, ep.created, ep.reference, ep.ref_date,
              ep.amount, ep.doc_no, ep.dm_no, ep.confirmation,
              ep.status, ep.dm_date, ep.doc_date, ep.deposit_date,
              ep.close_date, ep.screenshot, ep.receipt,
              SUM(s.registration) AS sales, r.region,
              c.company_code AS company
            FROM tbl_electronic_payment ep
            RIGHT JOIN tbl_sales s ON s.electronic_payment = ep.epid
            LEFT JOIN tbl_region r ON r.rid = ep.region
            LEFT JOIN tbl_company c ON c.cid = ep.company
            WHERE ep.status = 4 $this->compQuery
            GROUP BY ep.epid, r.region, c.company_code
            ORDER BY ep.created DESC
SQL;
          return $this->db->query($sql)->result_object();
        }

        public function load_batch_sales($epid)
        {
                $payment = $this->db->query("select * from tbl_electronic_payment where epid = ".$epid)->row();
                $payment->sales = $this->db->query("select * from tbl_sales
                        inner join tbl_engine on engine = eid
                        inner join tbl_customer on customer = cid
                        where electronic_payment = ".$epid."
                        order by bcode desc, date_sold desc")->result_object();
                return $payment;
        }

        public function extract_to_csv($param) {
          $extract_csv = $this->db->query("
            SELECT
              e.engine_no, e.chassis_no,
              'Motorcycle without Side Car' as type,
              CONCAT(s.bcode,' ',s.bname) as branchnames,
              CONCAT(c.last_name,',',c.first_name) as customername,
              s.payment_method
            FROM
              tbl_sales s
            INNER JOIN
              tbl_engine e ON e.eid = s.engine
            INNER JOIN
              tbl_customer c ON c.cid = s.customer
            WHERE
              s.status = 2 AND s.region = ".$param->region."
              AND LEFT(s.bcode, 1) = '".$param->company."'
              AND s.pending_date BETWEEN '".$param->date_from." 00:00:00' AND '".$param->date_to." 23:59:59'
              ORDER BY s.bcode
          ")->result_array();

          return $extract_csv;
        }

}
