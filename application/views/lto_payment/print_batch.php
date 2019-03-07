<html class="no-js">
<head>
    <title>Print Batch <?php print $payment->reference; ?></title>
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
<h3 style="display: block; float: right; margin: 0">Reference #: <?php print $payment->reference; ?></h3>
<h3>LTO Payment for <?php print $payment->region.' '.$payment->company; ?></h3>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Date Sold</th>
      <th>Branch</th>
      <th>Customer Name</th>
      <th>Customer Code</th>
      <th>Engine #</th>
      <th>SI #</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $ctr = 0;
    foreach ($payment->sales as $sales)
    {
      $ctr++;
      print '<tr>';
      print '<td>'.$ctr.'</td>';
      print '<td>'.substr($sales->date_sold, 0, 10).'</td>';
      print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
      print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
      print '<td>'.$sales->cust_code.'</td>';
      print '<td>'.$sales->engine_no.'</td>';
      print '<td>'.$sales->si_no.'</td>';
      print '</tr>';
    }
    ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="5">Amount for Payment</th>
      <th style="text-align:right; font-size: 20px;">&#x20b1 <?php print number_format($payment->amount, 2, '.', ','); ?></th>
    </tr>
  </tfoot>
</table>

</body>
</html>