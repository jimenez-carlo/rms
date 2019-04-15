<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// close the window, after execute
print '<script>$(document).ready(function(){ setTimeout(function(){ window.close(); }, 1000); });</script>';

// set header to generate csv
header('Pragma:public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=liquidation_'.$vid.'_'.date('ymd').'.csv');

// start generate csv
ob_end_clean();
$output = fopen('php://output', 'w');

// output headers
fputcsv($output, array('Date Sold', 'Branch', 'Customer Name', 'Customer Code', 'Engine #', 'Type of Sales', 'SI #', 'Registration Type', 'AR #', 'Amount Given', 'LTO Registration', 'Status'));

$sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
foreach ($table as $row)
{
	$line = array();
  $line[] = substr($row->date_sold, 0, 10);
  $line[] = $row->bcode.' '.$row->bname;
  $line[] = $row->first_name.' '.$row->last_name;
  $line[] = $row->cust_code;
  $line[] = $row->engine_no;
  $line[] = $sales_type[$row->sales_type];
  $line[] = $row->si_no;
  $line[] = $row->registration_type;
  $line[] = $row->ar_no;
  $line[] = $row->amount;
  $line[] = $row->registration;
  $line[] = $row->status;
  fputcsv($output, $line);
}

fclose($output);
exit();
?>