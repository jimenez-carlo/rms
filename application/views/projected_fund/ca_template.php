<?php
$list = array(
  [
    "Debit/Credit line item", "Document Type", "Posting Date", "Document Type",
    "Company Code", "Currency", " ", "Reference#", " ", "Posting Key", "Vendor",
    "Special GL", "CA Amount",  " "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," ",
    "Profit Center", " ", " ", "Reference", "Description"
  ]
);

foreach ($data AS $key => $ca) {
  $line_item = $key+1;
  $list[] =  array(
    $line_item, $ca['document_type'], $ca['posting_date'], $ca['kr'], $ca['company_code'], $ca['php'], " ", $ca['reference'],
    " ", 29, $ca['vendor'], 1, $ca['amount'], " "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," ", $ca['profit_center'],
    " ", " ", $ca['reference'], $ca['description']
  );
  $list[] =  array(
    $line_item, $ca['document_type'], $ca['posting_date'], $ca['kr'], $ca['company_code'], $ca['php'], " ", $ca['reference'],
    " ", 31, $ca['vendor'], " ", $ca['amount'], " "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," ", $ca['profit_center'],
    " ", " ", $ca['reference'], $ca['description']
  );
}


header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="CA-'.$date.'.csv";');

$file = fopen('php://output', 'w');

foreach ($list as $line) {
  fputcsv($file, $line);
}

?>
