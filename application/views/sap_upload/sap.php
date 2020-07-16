<?php
$list = array();
$line = array(
  "Counter V_COUNTER", "Document Date BKPF-BLART", "Posting Date BKPF-BUDAT",
  "Document Type BKPF-BLART", "Company code BKPF-BUKRS", "Currency BKPF-WAERS",
  "Exchange Rate BKPF-KURSF", "Reference AR# -computed", "Doc Header Text BKPF-BKTXT",
  "Posting Key BSEG-BSCHL", "Special GL Ind BSEG-UMSKZ", "Amt in Doc Currency BSEG-WRBTR",
  "Amt in Local Curr  BSEG-DMTR", "Tax Code BSEG-MWSKZ", "Calc Tax BSEG-XMWST",
  "WHT Code BSEG-QSSKZ", "Baseline Date BSEG-ZFBDT", "Value Date BSEG-VALUT",
  "Payment Term BSEG-ZTERM", "Payment Method BSEG-ZLSCH", "",
  "Cost Center BSEG-KOSTL", "Profit Center BSEG-PRCTR", "Internal Order BSEG-AUFNR",
  "WBS Element BSEG-POSID", "Assignment BSEG-ZUONR", "Text BSEG-SGTXT"
);
$list[] = $line;

$ctr = 0;
foreach ($batch as $sales)
{
	$ctr++;
        $regn_and_misc_expense = 0;

        if (!empty($misc_expenses)) {
          $misc_expense = $misc_expenses[$sales['reference_number']] ?? 0;
          $regn_and_misc_expense += $misc_expense;
          if ($ctr === 1) {
            $regn_and_misc_expense += $misc_expenses['remainder'];
          }
        }

        $regn_and_misc_expense += $sales['regn_expense'];

        switch ($sales['registration_type']) {
          case 'Free Registration':
	    //debit SI
	    $line = array(
              $ctr,
              $sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['si_no'],'','31',$sales['account_key'],'',
              number_format($regn_and_misc_expense, 2, '.', ''),
              '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['reference_number'],$sales['cust_code'],
	    );
	    $list[] = $line;

	    //credit SI
	    $line = array(
	    	$ctr,
	    	$sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['si_no'],'','40',$sales['sap_code'],'',
	    	number_format($regn_and_misc_expense, 2, '.', ''),
                    '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['customer_name'],$sales['cust_code'],
	    );
	    $list[] = $line;
            break;

          case 'Regular Regn. Paid':
          case 'Regn. under NIA':
	    //debit AR
	    $line = array(
              $ctr,
              $sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['ar_no'],'','31',$sales['account_key'],'',
              number_format($regn_and_misc_expense, 2, '.', ''),
              '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['reference_number'],$sales['cust_code'],
	    );
	    $list[] = $line;

	    //credit AR
	    $line = array(
	    	$ctr,
	    	$sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['ar_no'],'','40',$sales['sap_code'],'',
	    	number_format($regn_and_misc_expense, 2, '.', ''),
                    '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['customer_name'],$sales['cust_code'],
	    );
	    $list[] = $line;
            break;

          case 'With Regn. Subsidy':
            $si_exp = 0;
            $ar_exp = 0;
            if ($regn_and_misc_expense > $sales['ar_amount']) {
              $si_exp = $regn_and_misc_expense - $sales['ar_amount'];
              $ar_exp = $sales['ar_amount'];
            } elseif ($regn_and_misc_expense <= $sales['ar_amount']) {
              $ar_exp = $regn_and_misc_expense;
            }

	    //debit SI
	    $line = array(
              $ctr,
              $sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['si_no'],'','31',$sales['account_key'],'',
              number_format($si_exp, 2, '.', ''),
              '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['reference_number'],$sales['cust_code'],
	    );
	    $list[] = $line;

	    //credit SI
            $line = array(
              $ctr,
              $sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['si_no'],'','40','215450','',
              number_format($si_exp, 2, '.', ''),
              '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['customer_name'],$sales['cust_code'],
            );
	    $list[] = $line;

            $ctr++;
	    //debit AR
	    $line = array(
              $ctr,
              $sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['ar_no'],'','31',$sales['account_key'],'',
              number_format($ar_exp, 2, '.', ''),
              '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['reference_number'],$sales['cust_code'],
	    );
	    $list[] = $line;

	    //credit AR
            $line = array(
              $ctr,
              $sales['post_date'],$sales['post_date'],'KR',$sales['c_code'],'PHP','',$sales['ar_no'],'','40',$sales['sap_code'],'',
              number_format($ar_exp, 2, '.', ''),
              '','','','','','','','',$sales['branch_code'],$sales['branch_code'],'','',$sales['customer_name'],$sales['cust_code'],
            );
	    $list[] = $line;
            break;
        }

}

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="sap_template.csv";');

$file = fopen('php://output', 'w');

foreach ($list as $line) {
  fputcsv($file, $line);
}

?>
