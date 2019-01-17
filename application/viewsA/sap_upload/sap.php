<?php
$list = array();
$line = array(
"Counter V_COUNTER","Document Date BKPF-BLART","Posting Date BKPF-BUDAT","Document Type BKPF-BLART","Company code BKPF-BUKRS","Currency BKPF-WAERS","Exchange Rate BKPF-KURSF","Reference AR# -computed","Doc Header Text BKPF-BKTXT","Posting Key BSEG-BSCHL","Special GL Ind BSEG-UMSKZ","Amt in Doc Currency BSEG-WRBTR","Amt in Local Curr  BSEG-DMTR","Tax Code BSEG-MWSKZ","Calc Tax BSEG-XMWST","WHT Code BSEG-QSSKZ","Baseline Date BSEG-ZFBDT","Value Date BSEG-VALUT","Payment Term BSEG-ZTERM","Payment Method BSEG-ZLSCH","Cost Center BSEG-KOSTL","Profit Center BSEG-PRCTR","Internal Order BSEG-AUFNR","WBS Element BSEG-POSID","Assignment BSEG-ZUONR","Text BSEG-SGTXT"
);
$list[] = $line;

$ctr = 0;
$company = $batch->bcode;

// include misc
$sales_misc = (empty($batch->misc)) ? 0 : ($batch->meal + $batch->photocopy + $batch->transportation + $batch->others) / count($batch->sales);

foreach ($batch->sales as $sales)
{
	$bcode = $sales->branch->b_code;
	$sap_code = ($company == 1) ? "219".substr($bcode, 1, 3)
		: "219".$company.substr($bcode, 2, 2);

	$ctr++;
	$date_sold = date('m/d/Y', strtotime($sales->date_sold));
	$expense = number_format($sales->registration + $sales->tip + $sales_misc, 2, '.', '');

	//debit
	$line = array(
		$ctr,
		$date_sold,
		$date_sold,
		'KR',
		$company.'000',
		'PHP',
		'',
		$sales->ar_no,
		'',
		'31',
		$batch->account_key,
		'',
		$expense,
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		$bcode.'000',
		$bcode.'000',
		'',
		'',
		$sales->last_name.', '.$sales->first_name,
		$sales->cust_code,
	);
	$list[] = $line;

	//credit
	$line = array(
		$ctr,
		$date_sold,
		$date_sold,
		'KR',
		$company.'000',
		'PHP',
		'',
		$sales->ar_no,
		'',
		'40',
		$sap_code,
		'',
		$expense,
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		$bcode.'000',
		$bcode.'000',
		'',
		'',
		$sales->last_name.', '.$sales->first_name,
		$sales->cust_code,
	);
	$list[] = $line;
}

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="sap_template.csv";');

$file = fopen('php://output', 'w');

foreach ($list as $line) {
  fputcsv($file, $line);
}

?>
