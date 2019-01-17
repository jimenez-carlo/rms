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
      font-family: Tahoma;
      padding-top: 10px !important;
    }
    h4 {
      padding-bottom: 0px;
    }
    table {
      width:100%;
      border-collapse: collapse;
    }
    table, th, tr, td {
      padding: 3px !important;
      border: 1px solid;
      border-color: #000 !important;
    }
    .span3 {
        width: 18%;
        float: left;
    }
    .span4 {
        width: 30%;
        float: left;
    }
    .span6 {
        width: 40%;
        float: left;
    }
    a {
        display:none;
    }
    </style>

    <style type="text/css">
    body {
      font-family: Tahoma;
      padding-top: 30px !important;
    }
    h4 {
      padding-bottom: 0px;
    }
    table {
      width:100%;
      border-collapse: collapse;
    }
    table, th, tr, td {
      padding: 3px !important;
      border: 1px solid !important;
      border-color: #000 !important;
    }
    .span3 {
        width: 23%;
        float: left;
    }
    .span4 {
        width: 30%;
        float: left;
    }
    .span6 {
        width: 50%;
        float: left;
    }
    </style>
</head>
<body onload="window.print();">
<!--  -->
<center><h4>FUND TRANSFER</h4></center>
<div class="span12">

  <p style="margin-bottom:10px;"><a href="./../../fund_transfer">Return to Fund Transfer</a></p>
  
  <br>

  <div class="span6">
    Debit Memo #: <?php print $fund_transfer->dm_no; ?><br>
    Amount: <?php print number_format($fund_transfer->amount,2,'.',','); ?><br>
    Date Transferred: <?php print substr($fund_transfer->date,0,10); ?>
  </div>
  <div class="span4">
    Region: <?php print $fund->region; ?><br>
    Company: <?php print $fund->company; ?>
  </div>


<!--   <div class="span4">
    <label>Debit Memo #</label>
    <div><?php print $fund_transfer->dm_no; ?></div>
  </div>
  <div class="span4">
    <label>Amount: </label>
    <div>&#x20b1 <?php print number_format($fund_transfer->amount,2,'.',','); ?></div>
  </div>
  <div class="span4">
    <label>Date Transferred: </label>
    <div><?php print substr($fund_transfer->date,0,10); ?></div>
  </div> -->

  <div style="clear:both"></div>
<br>
  <div>
    <label><b>Projected Costs</b></label>
    <div>
    
      <table>
        <thead>
          <tr>
            <th>Transmittal Date</th>
            <th>Amount</th>
            <th colspan="2">Details</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total = 0;
          foreach ($table as $row)
          {
            $total += $row->amount;
            print '<tr>';
            print '<td style="text-align:center;">'.$row->date.'</td>';
            print '<td style="text-align:right;">'.number_format($row->amount,2,'.',',').'</td>';
            print '<td style="border-right:0!important;text-align:center;">
              '.number_format($row->amount_cash,2,'.',',').' 
              <i>('.$row->unit_cash.' cash units)</i>
              </td>
              <td style="border-left:0!important;">
              '.number_format($row->amount_inst,2,'.',',').'
              <i>('.$row->unit_inst.' inst units)</i>
              </td>';
            print '</tr>';
          }
          ?>
        </tbody>
        <tfoot>
          <th>Total</th>
          <td style="text-align:right;font-weight:bold;">&#x20b1 <?php print number_format($total, 2, '.', ','); ?></td>
          <th colspan="2"></th>
        </tfoot>
      </table>

    </div>
  </div>
</div>
