<html class="no-js">
  <head>
    <title>Print RERFO</title>
    <link rel="shortcut icon" href="/images/favicon.ico"/>
    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print">
    <link href="/assets/styles.css" rel="stylesheet" media="print">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>

    <style type="text/css" media="print">
      body {
        font-family: Tahoma;
        font-size: 14px;
        padding-top: 30px;
      }
      table {
        font-size: 14px;
        width:100%;
        border-collapse: collapse;
      }
      table thead th,
      table tfoot th {
        text-align: left;
      }
      table thead tr:last-child th,
      table tfoot tr:first-child th {
        white-space: nowrap;
      }
      table thead tr:last-child th,
      table tbody tr td,
      table tfoot tr:first-child th {
        padding: 3px;
        border: 1px solid;
        border-color: #000;
      }
      table tfoot tr.tot-row th {
        color: red;
        font-size: 16px;
        padding: 3px;
        border: black 1px solid;
        padding-top: 20px;
        border-top: black double;
      }
    </style>

    <style type="text/css">
      body {
        font-family: Tahoma;
        font-size: 14px;
        padding-top: 30px;
      }
      table {
        font-size: 14px;
        width:100%;
        border-collapse: collapse;
      }
      table thead th,
      table tfoot th {
        text-align: left;
      }
      table thead tr:last-child th,
      table tfoot tr:first-child th {
        white-space: nowrap;
      }
      table thead tr:last-child th,
      table tbody tr td,
      table tfoot tr:first-child th {
        padding: 3px;
        border: 1px solid;
        border-color: #000;
      }
      table tfoot tr.tot-row th {
        color: red;
        font-size: 16px;
        padding: 3px;
        border: black 1px solid;
        padding-top: 20px;
        border-top: black double;
      }
    </style>
  </head>
  <body onload="window.print();">
    <table>
      <thead>
        <tr>
          <th colspan="7">DAILY RERFO</th>
          <th colspan="5">RERFO ID: <?php print $rerfo->trans_no; ?></th>
        </tr>
        <tr>
          <th colspan="7">Branch: <?php print $rerfo->bcode.' '.$rerfo->bname; ?></th>
          <th colspan="5">Date: <?php print date('Y-m-d'); ?></th>
        </tr>
        <tr>
          <th colspan="7">Period Covered: <?php print $rerfo->date ?></th>
          <th colspan="5"></th>
        </tr>
        <tr><th colspan="12"><br><br></th></tr>
        <tr>
          <th><p>Registration Type</p></th>
          <th><p>Reference AR #</p></th>
          <th><p>Motor Type</p></th>
          <th><p>Date Sold</p></th>
          <th><p>Cust Code</p></th>
          <th><p>Customer Name</p></th>
          <th><p>Engine #</p></th>
          <th><p>Target</p></th>
          <th><p>Amount Given</p></th>
          <th><p>Insurance</p></th>
          <!-- <th><p>LTO Tip</p></th> -->
          <th><p>LTO Registration</p></th>
          <th><p>Balance</p></th>
          <!-- <th><p>CR #</p></th>
          <th><p>MV File #</p></th> -->
        </tr>
      </thead>
      <tbody>
        <?php
        $tot_tgt = $tot_amt = $tot_ins = $tot_reg = $tot_tip = $tot_bal = 0;
        foreach ($rerfo->sales as $sales)
        {
          print '<tr>';
          print '<td>'.$sales->registration_type.'</td>';
          print '<td>'.$sales->ar_no.'</td>';
          print '<td>'.$sales->sales_type.'</td>';
          print '<td>'.$sales->date_sold.'</td>';
          print '<td>'.$sales->cust_code.'</td>';
          print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
          print '<td>'.$sales->engine_no.'</td>';
          print '<td style="text-align: right">1,500.00</td>';
          print '<td style="text-align: right">'.number_format($sales->amount, 2, '.', ',').'</td>';
          print '<td style="text-align: right">300.00</td>';
          // print '<td>'.number_format($sales->tip, 2, '.', ',').'</td>';
          print '<td style="text-align: right">'.number_format($sales->registration, 2, '.', ',').'</td>';

          $bal = 1500 - 300 - $sales->registration - $sales->tip;
          print '<td style="text-align: right">'.number_format($bal, 2, '.', ',').'</td>';
          // print '<td>'.$sales->cr_no.'</td>';
          // print '<td>'.$sales->mvf_no.'</td>';
          print '</tr>';

          $tot_tgt += 1500;
          $tot_amt += $sales->amount;
          $tot_ins += 300;
          $tot_reg += $sales->registration;
          $tot_tip += $sales->tip;
          $tot_bal += $bal;
        }
        ?>
      </tbody>
      <tfoot>
        <tr>
          <th>Total SUM</th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th style="text-align: right">&#x20b1 <?php print number_format($tot_tgt, 2, ".", ","); ?></th>
          <th style="text-align: right">&#x20b1 <?php print number_format($tot_amt, 2, ".", ","); ?></th>
          <th style="text-align: right">&#x20b1 <?php print number_format($tot_ins, 2, ".", ","); ?></th>
          <!-- <th style="text-align: right;">&#x20b1 <?php print number_format($tot_tip, 2, ".", ","); ?></th> -->
          <th style="text-align: right">&#x20b1 <?php print number_format($tot_reg, 2, ".", ","); ?></th>
          <th style="text-align: right">&#x20b1 <?php print number_format($tot_bal, 2, ".", ","); ?></th>
        </tr>
        <tr class="tot-row">
          <th colspan="9">Total Expenses</th>
          <th colspan="3" style="text-align: right">&#x20b1 <?php print number_format($tot_reg, 2, ".", ","); ?></th>
        </tr>
        <tr><th colspan="12"><br><br></th></tr>
        <tr><th colspan="12">
          <p style="max-width: 780px">We hereby certify that we have personally examined all the details and the supporting documents of this RERFO. We affirmed that the above-mentioned items were incurred to process the registration-related expenses of the aforementioned customers. By signing below, we affirmed the legitimacy of these expenses and/or transcations including the genuineness and authenticity of the attached suppporting receipts and/or documents.</p>
        </th></tr>
        <tr><th colspan="12"><br><br></th></tr>
        <tr>
          <th colspan="2">Prepared By:<br><br><br>MA/LO/BS</th>
          <th colspan="3">Checked By:<br><br><br>Revolving Fund Custodian</th>
          <th colspan="7">Approved By:<br><br><br>RS</th>

          <?php
          // foreach ($rerfo->users as $user) {
          //   print "<br><br><br>";
          //   print $user->firstname.' '.$user->lastname;
          // }

          // echo $rerfo->user->firstname.' '.$rerfo->user->lastname;
          ?>
        </tr>
      </tfoot>
    </table>
  </body>
</html>
