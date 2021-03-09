<html class="no-js">
<head>
    <title>Fund Transfer Audit - Print</title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>images/favicon.ico"/>
    <!-- Bootstrap -->
    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print">
    <link href="<?php echo base_url(); ?>assets/styles.css" rel="stylesheet" media="print">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="<?php echo base_url(); ?>vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>

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
  <p style="display: block; float: right; margin: 20px 10px;"><?php print date('F j, Y H:i:s'); ?></p>
  <h3>Registration Monitoring System</h3>
  <hr>
  <h3>Projected Funds for <?php echo $company_region; ?></h3>
  <table>
    <thead>
      <tr>
        <th>Reference</th>
        <th>Document #</th>
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
      foreach ($prints as $print)
      {
        $amount = 0;
        print '<tr>';
        print '<td>'.$print['reference'].'</td>';
        print '<td>'.$print['doc_no'].'</td>';
        print '<td>'.$print['bcode'].'</td>';
        print '<td>'.$print['bname'].'</td>';
        print '<td>'.$print['no_of_unit'].'</td>';
        print '<td>'.number_format($print['amount'], 2, '.', ',').'</td>';
        print '</tr>';
        $total += $print['amount'];
        $total_unit += $print['no_of_unit'];
      }
      ?>
    </tbody>
    <tfoot>
      <tr>
        <th>Total</th>
        <th></th>
        <th></th>
        <th></th>
        <th><?php echo $total_unit; ?></th>
        <th style="text-align:left;">&#x20b1 <?php print number_format($total, 2, '.', ','); ?></th>
      </tr>
    </tfoot>
  </table>
</body>
</html>
