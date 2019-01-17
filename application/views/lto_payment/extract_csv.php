<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// close the window, after execute
print '<script>$(document).ready(function(){ setTimeout(function(){ window.close(); }, 1000); });</script>';

// set header to generate csv
header('Pragma:public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=lto_batch_'.$param->region.$param->company.date('ymd').'.csv');

// start generate csv
ob_end_clean();
$output = fopen('php://output', 'w');

// output headers
fputcsv($output, array('ENGINE_NUMBER', 'CHASSIS_NUMBER', 'MV_TYPE','BRANCH NAME','CUSTOMER NAME'));

foreach ($result as $row) {
  fputcsv($output, $row);
}
fclose($output);
exit();
?>