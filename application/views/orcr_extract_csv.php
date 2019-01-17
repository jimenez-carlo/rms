<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// close the window, after execute
print '<script>$(document).ready(function(){ setTimeout(function(){ window.close(); }, 1000); });</script>';

// set header to generate csv
header('Pragma:public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=orcr_extract_batch_'.$batch_no.'_'.date('ymd').'.csv');

// start generate csv
ob_end_clean();
$output = fopen('php://output', 'w');

// output headers
fputcsv($output, array('branch_code', 'customer_name', 'engine_no', 'registration_date', 'cr_no', 'plate_no'));

foreach ($result as $row) {
  fputcsv($output, $row);
}
fclose($output);
exit();
?>