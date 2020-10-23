<html class="no-js">
  <head>
    <title>Print RERFO</title>
    <link rel="shortcut icon" href="<?php echo base_url('/images/favicon.ico'); ?>"/>
    <!-- Bootstrap -->
    <link href="<?php echo base_url('/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" media="print">
    <link href="<?php echo base_url('/assets/styles.css'); ?>" rel="stylesheet" media="print">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('/vendors/modernizr-2.6.2-respond-1.1.0.min.js'); ?>"></script>

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
  <!-- <body onload="window.print();"> -->
  <body>
    <table>
      <thead>
        <tr>
          <th colspan="7">Branch: <?php print $rerfo['bcode'].' '.$rerfo['bname']; ?></th>
          <th colspan="5">RERFO #: <?php print $rerfo['rerfo_number']; ?></th>
        </tr>
        <tr>
          <th colspan="7">Period Covered: <?php print $rerfo['date_created']; ?></th>
          <th colspan="5">Date: <?php print date('Y-m-d'); ?></th>
        </tr>
        <tr>
          <th colspan="7"></th>
          <th colspan="5"></th>
        </tr>
        <tr><th colspan="12"><br>Registration<br></th></tr>
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
          <th><p>LTO OR</p></th>
          <th><p>PNP Clearance</p></th>
          <th><p>Insurance</p></th>
          <th><p>Macro Etching</p></th>
          <th><p>Emission</p></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $tot_tgt = $tot_ar = $tot_or = $tot_pnp = $tot_ins = $tot_macr = $tot_emi = 0;
        foreach ($rerfo_engines as $rerfo_engine)
        {
          print '<tr>';
          print '<td>'.$rerfo_engine['registration_type'].'</td>';
          print '<td>'.$rerfo_engine['ar_num'].'</td>';
          print '<td>Repo</td>';
          print '<td>'.$rerfo_engine['date_sold'].'</td>';
          print '<td>'.$rerfo_engine['cust_code'].'</td>';
          print '<td>'.$rerfo_engine['first_name'].' '.$rerfo_engine['last_name'].'</td>';
          print '<td>'.$rerfo_engine['engine_no'].'</td>';
          print '<td style="text-align: right">3,600.00</td>'; // TODO Should be query in database.
          print '<td style="text-align: right">'.number_format($rerfo_engine['ar_amt'], 2, '.', ',').'</td>';
          print '<td style="text-align: right">'.number_format($rerfo_engine['registration_amt'], 2, '.', ',').'</td>';
          print '<td style="text-align: right">'.number_format($rerfo_engine['pnp_clearance_amt'], 2, '.', ',').'</td>';
          print '<td style="text-align: right">'.number_format($rerfo_engine['macro_etching_amt'], 2, '.', ',').'</td>';
          print '<td style="text-align: right">'.number_format($rerfo_engine['emission_amt'], 2, '.', ',').'</td>';
          print '<td style="text-align: right">'.number_format($rerfo_engine['insurance_amt'], 2, '.', ',').'</td>';
          print '</tr>';

          $tot_tgt  += 3600;
          $tot_ar   += $rerfo_engine['ar_amt'];
          $tot_or   += $rerfo_engine['registration_amt'];
          $tot_pnp  += $rerfo_engine['pnp_clearance_amt'];
          $tot_macr += $rerfo_engine['macro_etching_amt'];
          $tot_emi  += $rerfo_engine['emission_amt'];
          $tot_ins  += $rerfo_engine['insurance_amt'];
        }
        ?>
        <tr>
          <td>Total SUM</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_tgt, 2, ".", ","); ?></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_ar, 2, ".", ","); ?></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_or, 2, ".", ","); ?></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_pnp, 2, ".", ","); ?></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_macr, 2, ".", ","); ?></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_emi, 2, ".", ","); ?></td>
          <td style="text-align: right">&#x20b1 <?php print number_format($tot_ins, 2, ".", ","); ?></td>
        </tr>
      </tbody>
      <thead>
        <tr><th colspan="12">Miscellaneous Expense</th></tr>
        <tr>
          <th>Type</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $misc_expenses = json_decode($rerfo['misc_expenses'], true);
          $tot_exp = 0;
          foreach ($misc_expenses as $expense) {
            if ($expense['is_deleted'] !== "1") {
              echo '<tr>
                      <td>'.$expense['expense_type'].'</td>
                      <td>'.number_format($expense['amount'], 2, '.', ',').'</td>
                    </tr>';
              $tot_exp += $expense['amount'];
            }
          }
          echo '<tr>
                  <td>Total</td>
                  <td>&#x20b1 '.number_format($tot_exp, 2, '.', ',').'</td>
                </tr>';
        ?>
      </tbody>
      <tfoot>
        <?php $tot_gran = $tot_or + $tot_pnp + $tot_ins + $tot_macr + $tot_emi + $tot_exp; ?>
        <tr class="tot-row">
          <th colspan="9">Total Expenses</th>
          <th colspan="6" style="text-align: right">&#x20b1 <?php print number_format($tot_gran, 2, ".", ","); ?></th>
        </tr>
        <tr><th colspan="12"><br><br></th></tr>
        <tr>
          <th colspan="12">
            <p style="max-width: 780px">
              We hereby certify that we have personally examined all the details and the supporting documents of this RERFO.
              We affirmed that the above-mentioned items were incurred to process the registration-related expenses of the aforementioned customers.
              By signing below, we affirmed the legitimacy of these expenses and/or transcations including the genuineness and authenticity of the attached suppporting receipts and/or documents.
            </p>
          </th>
        </tr>
        <tr><th colspan="12"><br><br></th></tr>
        <tr>
          <th colspan="2">Prepared By:<br><br><br>MA/LO/BS</th>
          <th colspan="3">Checked By:<br><br><br>Revolving Fund Custodian</th>
          <th colspan="7">Approved By:<br><br><br>RS</th>
        </tr>
      </tfoot>
    </table>
  </body>
</html>
