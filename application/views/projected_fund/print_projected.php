<html class="no-js">
<head>
    <title>Fund Transfer Audit - Print</title>
    <link rel="shortcut icon" href="./../../images/favicon.ico"/>
    <!-- Bootstrap -->
    <link href="./../../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print">
    <link href="./../../assets/styles.css" rel="stylesheet" media="print">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="./../../vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>

    <style type="text/css" media="print">
    * {-webkit-print-color-adjust:exact;}
    body {
      font-family: Helvetica;
      padding-top: 30px;
    }
    table {
      width:100%;
      border-collapse: collapse;
    }
    thead th {
      background-color: aliceblue !important;
      border-bottom: 3px solid beige;
      padding-bottom: 10px;
      text-align: left;
      color: royalblue !important;
    }
    th, td {
      border: 3px solid #000;
      padding: 3px;
    }
    tfoot th {
      background-color: whitesmoke !important;
      padding-top: 10px;
      text-align: left;
      color: crimson !important;
    }
    </style>

    <style type="text/css">
    body {
      font-family: Helvetica;
      padding-top: 30px;
    }
    table {
      width:100%;
      border-collapse: collapse;
    }
    thead th {
      background-color: aliceblue !important;
      border-bottom: 3px solid beige;
      padding-bottom: 10px;
      text-align: left;
      color: royalblue !important;
    }
    th, td {
      border: 3px solid #000;
      padding: 3px;
    }
    tfoot th {
      background-color: whitesmoke !important;
      padding-top: 10px;
      text-align: left;
      color: crimson !important;
    }
    </style>
</head>
<body onload="window.print();">

<p style="display: block; float: right; margin: 0"><?php print date('F j, Y H:i:s'); ?></p>
<h3>Registration Monitoring System</h3>
<hr>
<p style="display: block; float: right; margin: 0">Reference #: <?php print $fund->reference; ?></p>
<h3>Projected Funds for <?php print $fund->region.' '.$fund->company; ?></h3>

<table>
  <thead>
    <tr>
      <th>Branch Code</th>
      <th>Branch Name</th>
      <th># of Units</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $total = 0;
    $total_unit = 0;
    $budget = ((int) ($_SESSION['company']) == 8) ? 1200 : 900;

    foreach ($fund->sales as $sales)
    {
      $amount = $budget * $sales->units;
      print '<tr>';
      print '<td>'.$sales->bcode.'</td>';
      print '<td>'.$sales->bname.'</td>';
      print '<td>'.$sales->units.'</td>';
      print '<td>'.number_format($amount, 2, '.', ',').'</td>';
      print '</tr>';
      $total += $amount;
      $total_unit+=$sales->units;
    }
    ?>
  </tbody>
  <tfoot>
    <tr>
      <th>Total</th>
      <th></th>
      <th><?php echo $total_unit; ?></th>
      <th style="text-align:right;">&#x20b1 <?php print number_format($total, 2, '.', ','); ?></th>
    </tr>
  </tfoot>
</table>
<p>Note: For cash advances, you are obligated to liquidate the above amount by submitting the original invoices with a voucher to the Head Office within 10 days from the date of deposit to your account. Otherwise, the amount shall be deducted from your salary. Please comply accordingly.</p>

</body>
</html>
