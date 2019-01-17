<html class="no-js">
<head>
    <title>Print Topsheet</title>
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
      table th,
      table td {
        text-align: left;
      }
      table thead tr:last-child th,
      table tfoot tr:first-child th {
        white-space: nowrap;
        border-bottom: double black;
      }

      table tbody tr td,
      table tfoot tr td {
        padding: 3px;
        border-top: 1px solid #ababab;
      }
      table tbody tr th {
        border-top: 1px solid black;
        border-bottom: double black;
      }

      table tfoot tr:last-child th {
        border-top: double black;
        border-bottom: 1px solid black;
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
      table th,
      table td {
        text-align: left;
      }
      table thead tr:last-child th,
      table tfoot tr:first-child th {
        white-space: nowrap;
        border-bottom: double black;
      }

      table tbody tr td,
      table tfoot tr td {
        padding: 3px;
        border-top: 1px solid #ababab;
      }
      table tbody tr th {
        border-top: 1px solid black;
        border-bottom: double black;
      }

      table tfoot tr:last-child th {
        border-top: double black;
        border-bottom: 1px solid black;
      }
    </style>
</head>
<body onload="window.print();">

<table>
    <thead>
      <tr>
        <th><p>TRANSACTION #</p></th>
        <th><p><?php print $topsheet->trans_no; ?></p></th>
        <th><p></p></th>
        <th><p></p></th>
        <th><p></p></th>
        <th><p></p></th>
      </tr>
      <tr>
        <th><p>DATE:</p></th>
        <th><p><?php print $topsheet->date; ?></p></th>
        <th><p></p></th>
        <th><p></p></th>
        <th><p></p></th>
        <th><p></p></th>
      </tr>
      <tr>
        <th><p>Region</p></th>
        <th><p>Branch Code</p></th>
        <th><p>Branch Name</p></th>
        <th><p>RERFO Date</p></th>
        <th><p>Reference</p></th>
        <th><p style="text-align: right">Amount</p></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $bcode = '0';
      $tot_amt = $tot_exp = $sub_total = 0;
      $table = $topsheet->sales;
      foreach($table as $row)
      {
        // first row
        if ($bcode == '0') $bcode = substr($row->bcode, 0, 1);
        if ($bcode != substr($row->bcode, 0, 1))
        {
          print '<tr>
            <th>Total SUM</th>
            <th>SUB TOTAL</th>
            <th></th>
            <th></th>
            <th></th>
            <th style="text-align: right">'.number_format($sub_total, 2, '.', '').'</th>
            </tr>';
          $sub_total = 0;
          $bcode = substr($row->bcode, 0, 1);
        }

        print '<tr>';
        print '<td>'.$regions[$row->region].'</td>';
        print '<td>'.$row->bcode.'</td>';
        print '<td>'.$row->bname.'</td>';
        print '<td>'.$row->date.'</td>';
        print '<td>'.$row->trans_no.'</td>';
        print '<td style="text-align: right">'.number_format($row->registration, 2, '.', '').'</td>';
        print '</tr>';

        $tot_amt += $row->amount;
        $tot_exp += $row->registration;
        $sub_total += $row->registration;
      }

      // last row
      if ($bcode != '0')
      {
        print '<tr>
          <th>Total SUM</th>
          <th>SUB TOTAL</th>
          <th></th>
          <th></th>
          <th></th>
          <th style="text-align: right">'.number_format($sub_total, 2, '.', '').'</th>
          </tr>';
        $sub_total = 0;
        $bcode = '0';
      }

      if (empty($table))
      {
        print '<tr>
          <td style="color: red; font-weight: bold;">No RERFO for Topsheet.</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td> 
          <td></td>
          </tr>';
      }
      ?>
    </tbody>
    <tfoot>
      <tr style="color: red">
        <th>TOTAL</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="text-align: right"><?php print number_format($tot_exp, 2, '.', ''); ?></th>
      </tr>

      <tr>
        <td>Transaction Expense:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><span style="float:right"><?php print number_format($topsheet->tot_transpo, 2, ".", ","); ?></span></td>
      </tr>
      <tr>
        <td>Meal Subsidy:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><span style="float:right"><?php print number_format($topsheet->tot_meal, 2, ".", ","); ?></span></td>
      </tr>
      <tr>
        <td>Photocopy:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><span style="float:right"><?php print number_format($topsheet->tot_photo, 2, ".", ","); ?></span></td>
      </tr>
      <tr>
        <td>Other: <?php if (!empty($topsheet->others_specify)) print '('.$topsheet->others_specify.')'; ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><span style="float:right"><?php print number_format($topsheet->tot_other, 2, ".", ","); ?></span></td>
      </tr>

      <tr style="color: red">
        <th>Total Expenses</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="text-align: right"><?php print number_format($tot_exp + $topsheet->tot_meal + $topsheet->tot_photo + $topsheet->tot_transpo + $topsheet->tot_other, 2, '.', ''); ?></th>
      </tr>
    </tfoot>
</table>

<p>Checked By:</p>
<div style="border: 1px solid; height: 50px; width: 200px;">

</body>
</html>
