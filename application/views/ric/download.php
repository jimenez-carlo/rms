<?php
$list = array(
  ["LTO EPP ADDITIONAL PAYMENT"], [],
  ["COMPANY", "CUSTOMER NAME", "CUSTOMER CODE", "CASH PENALTY", "REGISTRATION AMOUNT", "EPP BATCH"]
);

foreach ($data AS $key => $ric) {
  $list[] =  array(
    $ric['company'], $ric['customer_name'], $ric['cust_code'], $ric['penalty'], $ric['registration'], $ric['reference']
  );
}

$end_row = 3+count($data);

$list[] = array();
$list[] = array();
$list[] = array(
  "TOTAL", "", "", "=SUM(D4:D{$end_row})", "=SUM(E4:E{$end_row})"
);

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="'.$reference.'.csv";');

$file = fopen('php://output', 'w');

foreach ($list as $line) {
  fputcsv($file, $line);
}

?>
