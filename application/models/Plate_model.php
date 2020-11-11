
<?php
defined ('BASEPATH') OR exit('No direct script access allowed');
/*
 * TODO
 * - Plate id should be join using tbl_engine table.
 *
 *
 */

class Plate_model extends CI_Model{

   	public $status = array(
   	        0 => 'Ongoing Transmittal',
   	        1 => 'LTO Rejected',
   	        2 => 'LTO Pending',
   	        3 => 'NRU Paid',
   	        4 => 'Registered',
   	        5 => 'Liquidated',
   	);

  	public $lto_reason = array(
  	        0 => 'N/A',
  	        1 => 'Affidavit of Change Body Type',
  	        2 => 'Closed Item',
  	        3 => 'COC Does Not Exist',
  	        4 => 'DIY Reject',
  	        5 => 'Expired Accre',
  	        6 => 'Expired Insurance',
  	        7 => 'Lost Docs',
  	        8 => 'Need Affidavit of Lost Docs',
  	        9 => 'No Date on SI',
  	        10 => 'No Sales Report',
  	        11 => 'No TIN #',
  	        12 => 'Self Registration',
  	        13 => 'Unreadable SI',
  	        14 => 'Wrong CSR Attached',
  	);

	public function __construct()
	{
		parent::__construct();
		$this->company = ($_SESSION['company'] != 8) ? ' company != 8 ' : ' company = 8 ';
	}

        public function plate_report_bak($param)
        {
          $branch = (!empty($param->branch) && is_numeric($param->branch))
            ? " AND bcode = '".$param->branch."'" : ''; //'region = '.$param->region;
          $status = (!empty($param->status) && is_numeric($param->status))
            ? ' AND status = '.$param->status : '';
          $name = (!empty($param->name))
            ? " AND concat(first_name, ' ', last_name) LIKE '%".$param->name."%'" : '';
          $engine_no = (!empty($param->engine_no))
            ? " AND engine_no LIKE '%".$param->engine_no."%'" : '';

          $result = $this->db->query("
                 SELECT
                                                *, tbl_sales.sid AS ssid, tbl_engine.mvf_no AS mvff_no
                  FROM
                    tbl_sales
                  INNER JOIN
                        tbl_status ON status = status_id
                  INNER JOIN
                    tbl_engine ON engine = eid
                  INNER JOIN
                    tbl_customer ON customer = cid
                                        LEFT JOIN
                                        tbl_plate AS b ON tbl_sales.sid = b.sid
                  WHERE
                    1=1 ".$branch.$status.$name.$engine_no."AND tbl_status.status_type = 'SALES' AND (tbl_sales.status = 4 OR tbl_sales.status = 5)AND ".$this->company." AND b.plate_number IS NULL
                  ORDER BY tbl_sales.sid DESC LIMIT 1000
                ")->result_object();

                foreach ($result as $key => $sales)
                {
                  $sales->date_sold = substr($sales->date_sold, 0, 10);
                  $sales->status = $this->status[$sales->status];
                  $sales->lto_reason = $this->lto_reason[$sales->lto_reason];

                  $sales->edit = ($_SESSION['position'] == 108
                    && $sales->status == 3
                    && substr($sales->registration_date, 0, 10) == date('Y-m-d'));
                  $result[$key] = $sales;
                }

                return $result;
        }

        public function plate_report($param)
        {
          $result = '';
          $engine_no = (!empty($param->engine_no))
            ? " AND engine_no LIKE '%".$param->engine_no."%'" : '';

          if(!empty($param->engine_no)){
            $result = $this->db->query("
                  SELECT
                    *, tbl_sales.sid AS ssid
                  FROM
                    tbl_sales
                  INNER JOIN
                    tbl_status ON status = status_id
                  INNER JOIN
                    tbl_engine ON engine = eid
                  INNER JOIN
                    tbl_customer ON customer = cid
                  LEFT JOIN
                    tbl_plate AS b ON tbl_sales.sid = b.sid
                  WHERE
		    1=1 AND region = {$_SESSION['rrt_region_id']} ".$engine_no."
		    AND ".$this->company." AND tbl_status.status_type = 'SALES'
		    AND (tbl_sales.status = 4 OR tbl_sales.status = 5) AND b.plate_number IS NULL
                  ORDER BY tbl_sales.sid DESC LIMIT 1000
                ")->result_object();

                foreach ($result as $key => $sales)
                {
                  $sales->date_sold = substr($sales->date_sold, 0, 10);
                  $sales->status = $this->status[$sales->status];
                  $sales->lto_reason = $this->lto_reason[$sales->lto_reason];

                  $sales->edit = ($_SESSION['position'] == 108
                    && $sales->status == 3
                    && substr($sales->registration_date, 0, 10) == date('Y-m-d'));
                  $result[$key] = $sales;
                }

          }

                return $result;
        }

        public function plate_topsheet($branch)
        {
          if($branch == ''){
            $que = '';
          }
          else{
            $que = 'AND b.bcode = '.$branch;
          }
          $result = $this->db->query("SELECT
            b.branch as bid,
            d.mvf_no as mvf_no,
            a.plate_id AS plate_id,
            b.bname AS branchname,
            b.bcode as bcode,
            CONCAT(c.last_name, ', ', c.first_name) AS name,
            d.engine_no AS engine_no,
            a.plate_number AS plate_number,
            e.status_name AS status,
            a.status_id AS status_id,
            a.received_dt AS received_dt,
            a.received_cust AS received_cust
            FROM
            tbl_plate AS a
            INNER JOIN
            tbl_status AS e ON a.status_id = e.status_id
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine
            WHERE
            e.status_type = 'PLATE' AND a.status_id = 2 $que
            ORDER BY b.bname;")->result_object();
          return $result;
        }

	public function searchall_plate()
        {
          $result = $this->db->query("SELECT
            a.plate_id AS plate_id,
            b.bname AS branchname,
            d.mvf_no as mvf_no,
            CONCAT(c.last_name, ', ', c.first_name) AS name,
            d.engine_no AS engine_no,
            a.plate_number AS plate_number,
            e.status_name AS status,
            a.received_dt AS received_dt,
            a.received_cust AS received_cust
            FROM
            tbl_plate AS a
            INNER JOIN
            tbl_status AS e ON a.status_id = e.status_id
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine
            WHERE e.status_type = 'PLATE' AND b.region like case when ".$_SESSION['pid']."='108' then ".$_SESSION['region']." else '%%' end
            ORDER BY b.bname;")->result_object();

          return $result;
        }

        public function list_rerfo($param)
        {

          $date_from = (empty($param->date_from)) ? date('Y-m-d', strtotime('-3 days')) : $param->date_from;
          $date_to = (empty($param->date_to)) ? date('Y-m-d') : date('Y-m-d', strtotime($param->date_to));
          $branch = (!empty($param->branch) && is_numeric($param->branch)) ? " and b.bcode = '".$param->branch."'" : '';

          $print = '';
          if (!empty($param->print) && is_numeric($param->print)) {
            $print = ($param->print)
              ? ' and r.print_date is not null'
              : ' and r.print_date is null';
          }

          $having_status = '';
          if (!empty($param->status)) {
            switch ($param->status) {
            case 1: $having_status = ' AND a.status_id = 1'; break;
            case 2: $having_status = ' AND a.status_id = 2'; break;
            case 3: $having_status = ' AND a.status_id = 3'; break;
            case 4: $having_status = ' AND a.status_id = 4'; break;
            }
          }

          $result = $this->db->query("SELECT
            a.*,
            b.bname AS branchname,
            d.mvf_no as mvf_no,
            CONCAT(c.last_name, ', ', c.first_name) AS name,
            d.engine_no AS engine_no,
            e.status_name AS status
            FROM
            tbl_plate AS a
            INNER JOIN
            tbl_status AS e ON a.status_id = e.status_id
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine

            WHERE e.status_type = 'PLATE' AND a.date_encoded between '".$date_from."' AND '".$date_to."'
            ".$branch." ".$having_status." AND b.region like case when ".$_SESSION['pid']."='108' then ".$_SESSION['region']." else '%%' end
            ORDER BY b.bname;")->result_object();

          return $result;
        }

	public function update_platestatus($pid, $stat)
        {
          $username= $_SESSION['username'];
          $this->db->query("UPDATE tbl_plate
            SET
            status_id = $stat
            WHERE
            plate_id = $pid");
          return;
        }

	public function update_platenumber($pid, $pno)
        {
          $username= $_SESSION['username'];
          $this->db->query("UPDATE tbl_plate
            SET
            plate_number = '$pno'
            WHERE
            plate_id = $pid");
        }

	public function add_platenumber($sid, $plate_number, $branch_code)
        {
	  $plate_id = NULL;
          $trans = 'P-'.$branch_code.'-'.date("ymd");
	  $status = $this->db->query("
	    INSERT INTO
	      tbl_plate (plate_number, status_id, date_encoded, sid, plate_trans_no)
              VALUES ('$plate_number', '1', NOW(), '$sid', '$trans')
	  ");

	  if ($status) {
	    $plate_id = $this->db->insert_id();
	    $this->db->query("
	      UPDATE
		tbl_engine e,
		tbl_sales s
	      SET
		e.plate_id = {$plate_id}
	      WHERE
		e.eid = s.engine AND s.sid = {$sid}
	    ");
	  }

	  return $plate_id;
        }

	//Load Branch
	public function get_branch_transmittal($param)
        {
          $date_from = (empty($param->date_from)) ? date('Y-m-d', strtotime('-3 days')) : $param->date_from;
          $date_to = (empty($param->date_to)) ? date('Y-m-d') : date('Y-m-d', strtotime($param->date_to));
          $branch = (!empty($param->branch) && is_numeric($param->branch)) ? " and b.bcode = '".$param->branch."'" : '';

          $print = '';
          if (!empty($param->print) && is_numeric($param->print)) {
            $print = ($param->print)
              ? ' and r.print_date is not null'
              : ' and r.print_date is null';
          }

          $having_status = '';
          if (!empty($param->status)) {
            switch ($param->status) {
            case 1: $having_status = ' AND a.status_id = 1'; break;
            case 2: $having_status = ' AND a.status_id = 2'; break;
            case 3: $having_status = ' AND a.status_id = 3'; break;
            case 4: $having_status = ' AND a.status_id = 4'; break;
            }
          }

          $result = $this->db->query("
            SELECT
              ANY_VALUE(a.plate_id) AS plate_id, ANY_VALUE(a.plate_number) AS plate_number,
              ANY_VALUE(a.status_id) AS status_id, a.date_encoded,
              ANY_VALUE(a.sid) AS sid, ANY_VALUE(a.received_dt) AS received_dt,
              ANY_VALUE(a.received_cust) AS received_cust, ANY_VALUE(a.plate_trans_no) AS plate_trans_no,
              b.branch AS bid,
              ANY_VALUE(b.bcode) AS bcode,
              ANY_VALUE(d.mvf_no) as mvf_no,
              b.bname AS branchname,
              SUM(CASE WHEN a.status_id = '1' THEN 1 ELSE 0 END) AS forApproval,
              SUM(CASE WHEN a.status_id = '2' THEN 1 ELSE 0 END) AS pending,
              SUM(CASE WHEN a.status_id = '3' THEN 1 ELSE 0 END) AS received,
              SUM(CASE WHEN a.status_id = '4' THEN 1 ELSE 0 END) AS receivedcust,
              COUNT(a.plate_id) AS total
            FROM tbl_plate AS a
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine
            WHERE a.date_encoded BETWEEN '".$date_from."' AND '".$date_to."'
            ".$branch." ".$having_status."
            GROUP BY b.bname,b.branch, a.date_encoded
            ORDER BY b.bname;
          ")->result_object();
          return $result;
        }

	//View Transmittal
	public function load_platetransmittal_bak($branch)
        {
          $result = $this->db->query("SELECT
            a.*,
            b.branch as bid,
            b.bname AS branchname,
            d.mvf_no as mvf_no,
            b.bcode as bcode,
            CONCAT(c.last_name, ', ', c.first_name) AS name,
            d.engine_no AS engine_no,
            e.status_name AS status
            FROM
            tbl_plate AS a
            INNER JOIN
            tbl_status AS e ON a.status_id = e.status_id
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine
            WHERE
            e.status_type = 'PLATE' AND b.branch = $branch
            ORDER BY b.bname;")->result_object();
          return $result;
        }


	public function load_platetransmittal($branch, $test, $status)
        {
          if($status == '' || $status == 0){
            $stats = '';
          }
          else{
            $stats = 'AND a.status_id ='.$status;
          }
          $result = $this->db->query("SELECT
            b.branch as bid,
            d.mvf_no as mvf_no,
            a.plate_id AS plate_id,
            a.plate_trans_no AS plate_trans_no,
            b.bname AS branchname,
            b.bcode as bcode,
            CONCAT(c.last_name, ', ', c.first_name) AS name,
            d.engine_no AS engine_no,
            a.plate_number AS plate_number,
            e.status_name AS status,
            a.status_id AS status_id,
            a.received_dt AS received_dt,
            a.received_cust AS received_cust,
            a.date_encoded AS date_encoded
            FROM
            tbl_plate AS a
            INNER JOIN
            tbl_status AS e ON a.status_id = e.status_id
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine
            WHERE
            e.status_type = 'PLATE'	AND b.branch = $branch AND a.date_encoded = '$test' $stats
            ORDER BY b.bname;")->result_object();
          return $result;
        }

        public function print_platetransmittal($branch, $test, $status)
        {
          if($status == '' || $status == 0){
            $stats = '';
          }
          else{
            $stats = 'AND a.status_id ='.$status;
          }
          $result = $this->db->query("SELECT
            b.branch as bid,
            d.mvf_no as mvf_no,
            a.plate_id AS plate_id,
            b.bname AS branchname,
            a.plate_trans_no AS plate_trans_no,
            b.bcode as bcode,
            CONCAT(c.last_name, ', ', c.first_name) AS name,
            d.engine_no AS engine_no,
            a.plate_number AS plate_number,
            e.status_name AS status,
            a.status_id AS status_id,
            a.received_dt AS received_dt,
            a.received_cust AS received_cust
            FROM
            tbl_plate AS a
            INNER JOIN
            tbl_status AS e ON a.status_id = e.status_id
            INNER JOIN
            tbl_sales AS b ON a.sid = b.sid
            LEFT OUTER JOIN
            tbl_customer AS c ON c.cid = b.customer
            LEFT OUTER JOIN
            tbl_engine AS d ON d.eid = b.engine
            WHERE
            e.status_type = 'PLATE'	AND b.branch = $branch AND a.status_id = 2 AND a.date_encoded = '$test' $stats
            ORDER BY b.bname;")->result_object();
          return $result;
        }

}
