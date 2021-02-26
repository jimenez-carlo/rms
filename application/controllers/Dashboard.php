<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Cmc_model', 'cmc');
    switch ($_SESSION['position']) {
      case 81:
      case 73: $this->ccn(); break; // CCN
      //case 21: // LO
      //case 27: // PROGRAMMER/ADMIN
      case 3:
      case 53: $this->acct(); break; // ACCTG-PAYCL
      // case 34:
      // case 98: $this->trsry(); break; // Treasury
      case 108: $this->rrt_spvsr(); break; // RRT
      case 107:
      case 109:
      case 156: $this->rrt(); break; // RRT
      default: $this->template('home');
    }
  }

  public function index()
  {
    $this->access(1);
    $this->header_data('title', 'Dashboard');
    $this->header_data('nav', 'report');
    $this->header_data('dir', base_url());
  }

  public function unprocessed()
  {
    $this->access(1);
    $this->header_data('title', 'Unprocessed');
    $this->header_data('nav', 'report');
    $this->header_data('dir', './../');
    $this->header_data(
      'link',
      '<link rel="stylesheet" href="../vendors/morris/morris.css">
       <link href="../vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">'
    );

    // UNPROCESSED
    $global = $this->load->database('global', TRUE);
    $rms = $this->load->database('dev_rms', TRUE);

    $un_region = "";
    $bcode = array();

    if ($_SESSION['position'] != 108)
    {
      $branches = $global->query("select b_code from tbl_branches
        where ph_region = ".$_SESSION['region_id'])->result_object();
      foreach ($branches as $branch)
      {
        $bcode[] = $branch->b_code;
      }

      if (!empty($bcode))
      {
        $un_region = " and branch in (".implode(',', $bcode).") ";
      }
    }

    $unprocessed = $rms->query("
      SELECT
        COUNT(*) AS count,
        CASE
          WHEN pnp_status=0 OR pnp_status IS NULL THEN 'PNP'
          WHEN (ar_no IS NULL OR ar_no='N/A') AND (regn_status <> 'Self Registration' OR regn_status <> 'Free Registration') THEN 'AR'
          WHEN si_date_received IS NULL AND rrt_class <> 'NCR' THEN 'SI'
          WHEN insurance_date IS NULL THEN 'INSURANCE'
          ELSE 'PENDING'
        END AS status
      FROM customer_tbl
      LEFT JOIN regn_status ON engine_no=engine_nr
      LEFT JOIN rrt_reg_tbl ON branch=branch_code
      WHERE (transmittal_no = '' OR transmittal_no IS NULL)
      ".$un_region." GROUP BY 2
    ")->result_array();

    $data['pnp_count'] = $data['ar_count'] = $data['si_count'] = $data['insurance_count'] = $data['pending_count'] = 0;

    foreach ($unprocessed as $row) {
      switch ($row['status']) {
      case 'PNP': $data['pnp_count'] = $row['count']; break;
      case 'AR': $data['ar_count'] = $row['count']; break;
      case 'SI': $data['si_count'] = $row['count']; break;
      case 'INSURANCE': $data['insurance_count'] = $row['count']; break;
      case 'PENDING': $data['pending_count'] = $row['count']; break;
      }
    }

    $unprocessed_total = $data['pnp_count'] + $data['ar_count'] + $data['si_count'] + $data['insurance_count'] + $data['pending_count'];
    $unprocessed_data = '';
    $unprocessed_color = '';

    if ($unprocessed_total > 0)
    {
      $pnp_count = (($data['pnp_count'] / $unprocessed_total) * 100);
      $ar_count = (($data['ar_count'] / $unprocessed_total) * 100);
      $si_count = (($data['si_count'] / $unprocessed_total) * 100);
      $insurance_count = (($data['insurance_count'] / $unprocessed_total) * 100);
      $pending_count = (($data['pending_count'] / $unprocessed_total) * 100);

      if ($pnp_count > 0)
      {
        $unprocessed_data .= '{label: "No PNP", value: '.$pnp_count.' },';
        $unprocessed_color .= '"#D52323",';
      }
      if ($ar_count > 0)
      {
        $unprocessed_data .= '{label: "No AR", value: '.$ar_count.' },';
        $unprocessed_color .= '"#A11717",';
      }
      if ($si_count > 0)
      {
        $unprocessed_data .= '{label: "No SI", value: '.$si_count.' },';
        $unprocessed_color .= '"#BDBB19",';
      }
      if ($insurance_count > 0)
      {
        $unprocessed_data .= '{label: "No Insurance", value: '.$insurance_count.' },';
        $unprocessed_color .= '"#DDDA21",';
      }
      if ($pending_count > 0)
      {
        $unprocessed_data .= '{label: "Pending", value: '.$pending_count.' },';
        $unprocessed_color .= '"#3DA117",';
      }
    }
    else
    {
      $unprocessed_data = '{label: "No Data", value: 0 },';
    }

    $this->footer_data(
      'script',
      '<script src="../vendors/raphael-min.js"></script>
       <script src="../vendors/morris/morris.min.js"></script>
       <script src="../assets/scripts.js"></script>
       <script>
         $(function() {
           // Morris Donut Chart
           Morris.Donut({
               element: "unprocessed",
               data: ['.$unprocessed_data.'],
               colors: ['.$unprocessed_color.'],
               formatter: function (y) { return Math.round(y*'.$unprocessed_total.'/100) }
           });
         });
       </script>'
    );

    $this->template('dashboard/unprocessed', $data);
  }

  private function ccn($data = array())
  {
    $this->header_data(
      'link',
      '<link rel="stylesheet" href="vendors/morris/morris.css">
       <link href="vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">'
    );

    // Brand New Sales
    $data['bnew_rejected'] = $this->db->query("select count(*) as c from tbl_sales
            where bcode = ".$_SESSION['branch_code']."
            and (sales_type = 0 or sales_type = 1)
            and status = 0")->row()->c;
    $data['bnew_pending'] = $this->db->query("select count(*) as c from tbl_sales
            where bcode = ".$_SESSION['branch_code']."
            and (sales_type = 0 or sales_type = 1)
            and status in (1, 2, 3)")->row()->c;
    $data['bnew_registered'] = $this->db->query("select count(*) as c from tbl_sales
            where bcode = ".$_SESSION['branch_code']."
            and (sales_type = 0 or sales_type = 1)
            and status = 4")->row()->c;

    $bnew_data = '';
    $bnew_color = '';
    $bnew_total = $data['bnew_rejected'] + $data['bnew_pending'] + $data['bnew_registered'];

    if ($bnew_total > 0)
    {
      $bnew_rejected = round((($data['bnew_rejected'] / $bnew_total) * 100), 2);
      $bnew_pending = round((($data['bnew_pending'] / $bnew_total) * 100), 2);
      $bnew_registered = round((($data['bnew_registered'] / $bnew_total) * 100), 2);

      if ($bnew_rejected > 0)
      {
        $bnew_data .= '{label: "Pending in RRT", value: '.$bnew_rejected.' },';
        $bnew_color .= '"#A11717",';
      }
      if ($bnew_pending > 0)
      {
        $bnew_data .= '{label: "LTO Pending", value: '.$bnew_pending.' },';
        $bnew_color .= '"#BDBB19",';
      }
      if ($bnew_registered > 0)
      {
        $bnew_data .= '{label: "Registered", value: '.$bnew_registered.' },';
        $bnew_color .= '"#3DA117",';
      }
    }
    else
    {
      $bnew_pending = $bnew_rejected = $bnew_registered = 0;
      $bnew_data = '{label: "No Data", value: 0 },';
    }

    // orcr
    $result = $this->db->query("
      SELECT
        COUNT(*) as count, case when received_date is null then 'transmitted' else 'received' end as status
      FROM tbl_sales
      INNER JOIN tbl_topsheet t on tid = topsheet
      WHERE bcode = ".$_SESSION['branch_code']."
      AND t.transmittal_date is not null
      GROUP BY 2
    ")->result_object();

    $data['transmitted'] = 0;
    $data['received'] = 0;
    foreach ($result as $row)
    {
      $data[$row->status] = $row->count;
    }

    $orcr_data = '';
    $orcr_color = '';
    $orcr_total = $data['transmitted'] + $data['received'];

    if ($orcr_total > 0)
    {
      $transmitted = round((($data['transmitted'] / $orcr_total) * 100), 2);
      $received = round((($data['received'] / $orcr_total) * 100), 2);

      if ($transmitted > 0)
      {
        $orcr_data .= '{label: "Transmitted", value: '.$transmitted.' },';
        $orcr_color .= '"#A11717",';
      }
      if ($received > 0)
      {
        $orcr_data .= '{label: "Received", value: '.$received.' },';
        $orcr_color .= '"#3DA117",';
      }
    }
    else
    {
      $transmitted = $received = 0;
      $orcr_data = '{label: "No Data", value: 0 },';
    }

    $this->footer_data(
      'script',
      '<script src="vendors/raphael-min.js"></script>
       <script src="vendors/morris/morris.min.js"></script>
       <script src="assets/scripts.js"></script>
       <script>
         $(function() {
           // Morris Donut Chart
           Morris.Donut({
               element: "bnew",
               data: ['.$bnew_data.'],
               colors: ['.$bnew_color.'],
               formatter: function (y) { return Math.round(y*'.$bnew_total.'/100) + " (" + y + "%)" }
           });
           Morris.Donut({
               element: "orcr",
               data: ['.$orcr_data.'],
               colors: ['.$orcr_color.'],
               formatter: function (y) { return Math.round(y*'.$orcr_total.'/100) + " (" + y + "%)" }
           });
         });
       </script>'
    );

    $this->template('dashboard/ccn', $data);
  }

  private function rrt($data = array())
  {
    $this->header_data(
      'link',
      '<link rel="stylesheet" href="vendors/morris/morris.css">
       <link href="vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">'
    );

    $region = ($_SESSION['position'] == 107) ? '1 = 1' : 's.region = '.$_SESSION['region_id'];

    // rerfo
    $result = $this->db->query("select count(*) as count,
            case when t.transmittal_date is null then 'rrt_pending'
                    when received_date is null then 'transmitted'
                    else 'received'
            end as status
            from tbl_sales s
            inner join tbl_topsheet t on tid = topsheet
            where ".$region."
            group by 2")->result_object();

    $data['rrt_pending'] = 0;
    $data['transmitted'] = 0;
    $data['received'] = 0;
    foreach ($result as $row)
    {
            $data[$row->status] = $row->count;
    }

    $rerfo_data = '';
    $rerfo_color = '';
    $rerfo_total = $data['rrt_pending'] + $data['transmitted'] + $data['received'];

    if ($rerfo_total > 0)
    {
            $rrt_pending = round((($data['rrt_pending'] / $rerfo_total) * 100), 2);
            $transmitted = round((($data['transmitted'] / $rerfo_total) * 100), 2);
            $received = round((($data['received'] / $rerfo_total) * 100), 2);

            if ($rrt_pending > 0)
            {
                    $rerfo_data .= '{label: "For Transmittal", value: '.$rrt_pending.' },';
                    $rerfo_color .= '"#A11717",';
            }
            if ($transmitted > 0)
            {
                    $rerfo_data .= '{label: "Transmitted", value: '.$transmitted.' },';
                    $rerfo_color .= '"#BDBB19",';
            }
            if ($received > 0)
            {
                    $rerfo_data .= '{label: "Received", value: '.$received.' },';
                    $rerfo_color .= '"#3DA117",';
            }
    }
    else
    {
            $rrt_pending = $transmitted = $received = 0;
            $rerfo_data = '{label: "No Data", value: 0 },';
    }

    $this->footer_data(
      'script',
      '<script src="vendors/raphael-min.js"></script>
       <script src="vendors/morris/morris.min.js"></script>
       <script src="assets/scripts.js"></script>
       <script>
         $(function() {
         // Morris Donut Chart
           Morris.Donut({
             element: "rerfo",
             data: ['.$rerfo_data.'],
             colors: ['.$rerfo_color.'],
             formatter: function (y) { return Math.round(y*'.$rerfo_total.'/100) + " (" + y + "%)" }
           });
         });
       </script>'
    );

    $this->template('dashboard/rrt', $data);
  }

  private function rrt_spvsr($data = array())
  {
          $this->header_data('link',
                  '<link rel="stylesheet" href="vendors/morris/morris.css">
       <link href="vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">');

          // sales
          $region = ($_SESSION['position'] == 107) ? '1 = 1' : 'region = '.$_SESSION['region_id'];
          $result = $this->db->query("SELECT count(*) as count,
                  case status when 0 then 'new'
                          when 1 then 'rejected'
                          when 2 then 'pending'
                          when 3 then 'nru'
                          when 4 then 'registered'
                          when 5 then 'closed'
                  end as status
                  from tbl_sales
                  where ".$region."
                  group by 2")->result_object();

          $data['new'] = 0;
          $data['rejected'] = 0;
          $data['pending'] = 0;
          $data['nru'] = 0;
          $data['registered'] = 0;
          $data['closed'] = 0;

          foreach ($result as $row)
          {
                  $data[$row->status] = $row->count;
          }

          $sales_data = '';
          $sales_color = '';
          $sales_total = $data['new'] + $data['rejected'] + $data['pending'] + $data['nru'] + $data['registered'] + $data['closed'];

          if ($sales_total > 0)
          {
                  $new = round((($data['new'] / $sales_total) * 100), 2);
                  $rejected = round((($data['rejected'] / $sales_total) * 100), 2);
                  $pending = round((($data['pending'] / $sales_total) * 100), 2);
                  $nru = round((($data['nru'] / $sales_total) * 100), 2);
                  $registered = round((($data['registered'] / $sales_total) * 100), 2);
                  $closed = round((($data['closed'] / $sales_total) * 100), 2);

                  if ($new > 0)
                  {
                          $sales_data .= '{label: "RRT Pending", value: '.$new.' },';
                          $sales_color .= '"#A11717",';
                  }
                  if ($rejected > 0)
                  {
                          $sales_data .= '{label: "Rejected", value: '.$rejected.' },';
                          $sales_color .= '"#A11717",';
                  }
                  if ($pending > 0)
                  {
                          $sales_data .= '{label: "LTO Pending", value: '.$pending.' },';
                          $sales_color .= '"#BDBB19",';
                  }
                  if ($nru > 0)
                  {
                          $sales_data .= '{label: "NRU", value: '.$nru.' },';
                          $sales_color .= '"#BDBB10",';
                  }
                  if ($registered > 0)
                  {
                          $sales_data .= '{label: "Registered", value: '.$registered.' },';
                          $sales_color .= '"#3DA117",';
                  }
                  if ($closed > 0)
                  {
                          $sales_data .= '{label: "Closed", value: '.$closed.' },';
                          $sales_color .= '"#3DA110",';
                  }
          }
          else
          {
                  $new = $rejected = $pending = $nru = $registered = $closed = 0;
                  $sales_data = '{label: "No Data", value: 0 },';
          }

          // topsheet
          $region = ($_SESSION['position'] == 107) ? '1 = 1' : 'region = '.$_SESSION['region_id'];
          $result = $this->db->query("
              SELECT
                COUNT(*) AS count,
                CASE
                  WHEN s.status = 4 AND susb.subid IS NULL THEN 'unprocessed'
                  -- WHEN status = 1 THEN 'incomplete'
                  WHEN s.status = 4 AND sub.is_uploaded = 0 THEN 'sap_upload'
                  WHEN s.status = 5 THEN 'done'
                END AS label
              FROM
                tbl_sales s
              LEFT JOIN
                tbl_sap_upload_sales_batch susb ON s.sid = susb.sid
              LEFT JOIN
                tbl_sap_upload_batch sub ON susb.subid = sub.subid
              WHERE
                ".$region."
              GROUP BY label
          ")->result_object();

          $data['ts_unprocessed'] = 0;
          $data['ts_incomplete'] = 0;
          $data['ts_sap_upload'] = 0;
          $data['ts_done'] = 0;
          foreach ($result as $row)
          {
                  $data['ts_'.$row->label] = $row->count;
          }

          $ts_data = '';
          $ts_color = '';
          $ts_total = $data['ts_unprocessed'] + $data['ts_incomplete'] + $data['ts_sap_upload'] + $data['ts_done'];

          if ($ts_total > 0)
          {
                  $ts_unprocessed = round((($data['ts_unprocessed'] / $ts_total) * 100), 2);
                  $ts_incomplete = round((($data['ts_incomplete'] / $ts_total) * 100), 2);
                  $ts_sap_upload = round((($data['ts_sap_upload'] / $ts_total) * 100), 2);
                  $ts_done = round((($data['ts_done'] / $ts_total) * 100), 2);

                  if ($ts_unprocessed > 0)
                  {
                          $ts_data .= '{label: "Unprocessed", value: '.$ts_unprocessed.' },';
                          $ts_color .= '"#A11717",';
                  }
                  if ($ts_incomplete > 0)
                  {
                          $ts_data .= '{label: "Incomplete", value: '.$ts_incomplete.' },';
                          $ts_color .= '"#BDBB19",';
                  }
                  if ($ts_sap_upload > 0)
                  {
                          $ts_data .= '{label: "For SAP Upload", value: '.$ts_sap_upload.' },';
                          $ts_color .= '"#BDBB19",';
                  }
                  if ($ts_done > 0)
                  {
                          $ts_data .= '{label: "Done", value: '.$ts_done.' },';
                          $ts_color .= '"#3DA117",';
                  }
          }
          else
          {
                  $ts_unprocessed = $ts_incomplete = $ts_sap_upload = $ts_done = 0;
                  $ts_data = '{label: "No Data", value: 0 },';
          }

          /*
          **  report of units tagged as self registration
          **    with & without transmittal (independent, no process)
          */
          $branches = $this->cmc->get_region_branches($_SESSION['region_id']);
          $data['sr_with'] = $this->db->query("select sid from tbl_sales
                  where registration_type = 'Self Registration'and
                  transmittal_date IS NULL and
                  region = {$_SESSION['region_id']}")->num_rows();
          $data['sr_without'] = $this->db->query("select sid from tbl_sales
                  where registration_type = 'Self Registration'and
                  transmittal_date IS NOT NULL and
                  region = {$_SESSION['region_id']}")->num_rows();

          $sr_data = '';
          $sr_color = '';
          $sr_total = $data['sr_with'] + $data['sr_without'];

          if ($sr_total > 0)
          {
                  $sr_with = round((($data['sr_with'] / $sr_total) * 100), 2);
                  $sr_without = round((($data['sr_without'] / $sr_total) * 100), 2);

                  if ($sr_with > 0)
                  {
                          $sr_data .= '{label: "With Transmittal", value: '.$sr_with.' },';
                          $sr_color .= '"#A11717",';
                  }
                  if ($sr_without > 0)
                  {
                          $sr_data .= '{label: "Without Transmittal", value: '.$sr_without.' },';
                          $sr_color .= '"#BDBB19",';
                  }
          }
          else
          {
                  $sr_with = $sr_without = 0;
                  $sr_data = '{label: "No Data", value: 0 },';
          }

          $this->footer_data('script',
                  '<script src="vendors/raphael-min.js"></script>
                  <script src="vendors/morris/morris.min.js"></script>
          <script src="assets/scripts.js"></script>
          <script>
          $(function() {
                  // Morris Donut Chart
                  Morris.Donut({
                      element: "sales",
                      data: ['.$sales_data.'],
                      colors: ['.$sales_color.'],
                      formatter: function (y) { return Math.round(y*'.$sales_total.'/100) + " (" + y + "%)" }
                  });
                  Morris.Donut({
                      element: "topsheet",
                      data: ['.$ts_data.'],
                      colors: ['.$ts_color.'],
                      formatter: function (y) { return Math.round(y*'.$ts_total.'/100) + " (" + y + "%)" }
                  });
                  Morris.Donut({
                      element: "self_reg",
                      data: ['.$sr_data.'],
                      colors: ['.$sr_color.'],
                      formatter: function (y) { return Math.round(y*'.$sr_total.'/100) + " (" + y + "%)" }
                  });
          });
          </script>');

          $this->template('dashboard/rrt_spvsr', $data);
  }

  private function acct($data = array()) {
    $result = $this->db->query("
      SELECT
        'Cash Advance' AS '',
        Total, Pending, Done,
        CONCAT(ROUND((done/total) * 100, 2),'%') AS Rate
      FROM (
        SELECT
          FORMAT(COUNT(*),0) AS total,
          FORMAT(IFNULL(SUM(CASE WHEN v.voucher_no IS NULL THEN 1 ELSE 0 END), 0),0) AS pending,
          FORMAT(IFNULL(SUM(CASE WHEN v.voucher_no IS NOT NULL THEN 1 ELSE 0 END), 0), 0) AS done
        FROM tbl_sales s, tbl_voucher v
        WHERE v.vid = s.voucher AND s.payment_method = 'CASH' AND s.company {$this->cc} 8
      ) AS ca

      UNION

      SELECT
        'EPAT' AS '',
        Total, Pending, Done,
        CONCAT(ROUND((done/total) * 100, 2),'%') AS Rate
      FROM (
        SELECT
          FORMAT(COUNT(*),0) AS total,
          FORMAT(IFNULL(SUM(IF(ep.doc_no IS NULL, 1, 0)), 0),0) AS pending,
          FORMAT(IFNULL(SUM(IF(ep.doc_no IS NOT NULL, 1, 0)), 0), 0) AS done
        FROM tbl_sales s, tbl_electronic_payment ep
        WHERE s.electronic_payment = ep.epid AND s.payment_method = 'EPP' AND s.company {$this->cc} 8
      ) AS epat

      UNION

      SELECT
        'For Checking' AS '',
        Total, Pending, Done,
        CONCAT(ROUND((done/total) * 100, 2),'%') AS Rate
      FROM (
        SELECT
          FORMAT(COUNT(*), 0) AS total,
          FORMAT(IFNULL(SUM(CASE WHEN s.status = 4 AND susb.subid IS NULL THEN 1 ELSE 0 END), 0), 0) AS pending,
          FORMAT(IFNULL(SUM(CASE WHEN susb.subid IS NOT NULL OR s.status = 5 THEN 1 ELSE 0 END), 0), 0) AS done
        FROM tbl_sales s
        LEFT JOIN tbl_sap_upload_sales_batch susb ON s.sid = susb.sid
        LEFT JOIN tbl_sap_upload_batch sub ON susb.subid = sub.subid
        WHERE s.date_sold >= '2018-08-01' AND s.status >= 4 AND s.company {$this->cc} 8
      ) AS checking

      UNION

      SELECT
        'SAP Uploading' as '',
        Total, Pending, Done,
        CONCAT(ROUND((done/total) * 100, 2),'%') AS Rate
      FROM (
        SELECT
          FORMAT(IFNULL(SUM(IF(sub.is_uploaded = 0 OR s.status = 5, 1, 0)), 0), 0) AS total,
          FORMAT(IFNULL(SUM(CASE WHEN sub.is_uploaded = 0 THEN 1 END),0), 0) AS pending,
          FORMAT(IFNULL(SUM(CASE WHEN s.status = 5 THEN 1 END),0), 0) AS done
        FROM tbl_sales s
        LEFT JOIN tbl_sap_upload_sales_batch susb ON s.sid = susb.sid
        LEFT JOIN tbl_sap_upload_batch sub ON susb.subid = sub.subid
        WHERE s.status >= 4 AND s.company {$this->cc} 8
      ) AS sap

      UNION

      SELECT
        'Return Fund' AS '',
        Total, Pending, Done,
        CONCAT(ROUND((done/total) * 100, 2),'%') AS Rate
      FROM (
        SELECT
          FORMAT(IFNULL(SUM(IF(st.status_name IN('For Liquidation','Liquidated'), 1, 0)), 0), 0) AS total,
          FORMAT(IFNULL(SUM(IF(st.status_name = 'For Liquidation',1, 0)), 0), 0) AS pending,
          FORMAT(IFNULL(SUM(IF(st.status_name = 'Liquidated',1, 0)),0), 0) AS done
        FROM tbl_return_fund rf
        INNER JOIN tbl_voucher v ON v.vid = rf.fund
        INNER JOIN tbl_return_fund_history rfh1 ON rfh1.rfid = rf.rfid
        LEFT JOIN tbl_return_fund_history rfh2 ON rfh1.rfid = rfh2.rfid AND rfh1.return_fund_history_id < rfh2.return_fund_history_id
        INNER JOIN tbl_status st ON st.status_id = rfh1.status_id AND st.status_type = 'RETURN_FUND'
        WHERE rfh2.return_fund_history_id IS NULL AND rf.is_deleted = 0 AND v.company {$this->cc} 8
      ) AS return_fund

      UNION

      SELECT
        'Misc Expenses' AS '',
        Total, Pending, Done,
        CONCAT(ROUND((done/total) * 100, 2),'%') AS Rate
      FROM (
        SELECT
          FORMAT(IFNULL(SUM(IF(st.status_name IN('Approved','For Liquidation', 'Liquidated', 'Resolved'), 1, 0)), 0), 0) AS total,
          FORMAT(IFNULL(SUM(IF(st.status_name IN('Approved', 'Resolved', 'For Liquidation'), 1, 0)), 0), 0) AS pending,
          FORMAT(IFNULL(SUM(IF(st.status_name = 'Liquidated',1, 0)),0), 0) AS done
        FROM tbl_misc m
        JOIN tbl_misc_expense_history mxh1 USING(mid)
        LEFT JOIN tbl_misc_expense_history mxh2 ON mxh1.mid = mxh2.mid AND mxh1.id < mxh2.id
        INNER JOIN tbl_status st ON mxh1.status = st.status_id AND st.status_type = 'MISC_EXP'
        INNER JOIN tbl_voucher v ON m.ca_ref = v.vid
        WHERE 1=1 AND v.company {$this->cc} 8
      ) AS misc_exp
    ");
    $this->table->set_template([
      "table_open" => "<table id='tbl_chart' class='table'>"
    ]);

    $data['table'] = $this->table->generate($result);
    $this->template('dashboard/acct', $data);
  }

  private function trsry($data = array())
  {
    // batch
    $result = $this->db->query("select count(*) as count,
      case when (select count(*) from tbl_batch_status
        where batch = bid and status = 'For Check Issuance') = 0 then 'issuance'
        when (select count(*) from tbl_batch_status
        where batch = bid and status = 'For Management Approval') = 0 then 'approval'
        when (select count(*) from tbl_batch_status
        where batch = bid and status = 'For Check Deposit') = 0 then 'deposit'
        else 'done'
      end as status
      from tbl_batch
      where check_no is not null
      group by 2")->result_object();

    $data['b_issuance'] = 0;
    $data['b_approval'] = 0;
    $data['b_deposit'] = 0;
    $data['b_done'] = 0;
    foreach ($result as $row)
    {
      $data['b_'.$row->status] = $row->count;
    }

    $b_data = '';
    $b_color = '';
    $b_total = $data['b_issuance'] + $data['b_approval'] + $data['b_deposit'] + $data['b_done'];

    if ($b_total > 0)
    {
      $b_issuance = round((($data['b_issuance'] / $b_total) * 100), 2);
      $b_approval = round((($data['b_approval'] / $b_total) * 100), 2);
      $b_deposit = round((($data['b_deposit'] / $b_total) * 100), 2);
      $b_done = round((($data['b_done'] / $b_total) * 100), 2);

      if ($b_issuance > 0)
      {
        $b_data .= '{label: "For Check Issuance", value: '.$b_issuance.' },';
        $b_color .= '"#A11717",';
      }
      if ($b_approval > 0)
      {
        $b_data .= '{label: "For Management Approval", value: '.$b_approval.' },';
        $b_color .= '"#BDBB19",';
      }
      if ($b_deposit > 0)
      {
        $b_data .= '{label: "For Check Deposit", value: '.$b_deposit.' },';
        $b_color .= '"#BDBB19",';
      }
      if ($b_done > 0)
      {
        $b_data .= '{label: "Deposited", value: '.$b_done.' },';
        $b_color .= '"#3DA117",';
      }
    }
    else
    {
      $b_issuance = $b_approval = $b_deposit = $b_done = 0;
      $b_data = '{label: "No Data", value: 0 },';
    }

    $this->footer_data(
      'script',
      '<script src="vendors/raphael-min.js"></script>
       <script src="vendors/morris/morris.min.js"></script>
       <script src="assets/scripts.js"></script>
       <script>
         $(function() {
           // Morris Donut Chart
           Morris.Donut({
           element: "batch",
             data: ['.$b_data.'],
             colors: ['.$b_color.'],
             formatter: function (y) { return Math.round(y*'.$b_total.'/100) + " (" + y + "%)" }
           });
         });
       </script>'
     );

    $this->template('dashboard/trsry', $data);
  }

}
