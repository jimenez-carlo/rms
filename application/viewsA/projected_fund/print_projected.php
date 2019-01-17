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
<!--  -->
<h3>Projected Funds for <?php print $fund->region.' '.$fund->company; ?></h3>
<div class="span12">

  <div style="clear:both"></div>
<br>
  <div>
    <div>
      <table>
        <thead>
          <tr>
            <th>Transmittal Date</th>
            <th>Amount</th>
            <th>No. of Cash Units</th>
            <th>No. of Installment Units</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total = $total_cash = $total_inst = 0;
          foreach ($fund->projected as $row)
          {
            $total += $row->amount;
            $total_cash += $row->unit_cash;
            $total_inst += $row->unit_inst;
            print '<tr>';
            print '<td>'.$row->date.'</td>';
            print '<td style="text-align:right;">'.number_format($row->amount,2,'.',',').'</td>';
            print '<td style="text-align:right;">'.number_format($row->unit_cash,0,'.',',').'</td>';
            print '<td style="text-align:right;">'.number_format($row->unit_inst,0,'.',',').'</td>';
            print '</tr>';
          }
          ?>
        </tbody>
        <tfoot>
          <th>Total</th>
          <th style="text-align:right;">&#x20b1 <?php print number_format($total, 2, '.', ','); ?></th>
          <th style="text-align:right;"><?php print number_format($total_cash, 0, '.', ','); ?></th>
          <th style="text-align:right;"><?php print number_format($total_inst, 0, '.', ','); ?></th>
        </tfoot>
      </table>  
    </div>
  </div>
</div>
