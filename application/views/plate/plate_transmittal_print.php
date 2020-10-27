<html class="no-js">
<head>
  <title>Transmittal</title>
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
  body {
    font-family: Tahoma;
    font-size: 10px;
    padding-top: 30px !important;
  }
  .container {
      position: relative;
      border: solid 1px;
      margin: 2em;
      padding: 1em;
  }
  .dotted {
      border-top: dotted 1px;
      margin: 1em 0;
  }
  .divider {
      position: absolute;
      border-left: solid 1px;
      left: 60%;
      top: 2em;
      bottom: 0;
  }
  span {
      display: inline-block;
      vertical-align: top;
  }

  /* description block */
  .desc {
      display: inline-block;
      vertical-align: top;
      width: 60%;
  }
  .desc .title {
      border-bottom: solid 1px;
      padding-bottom: 0.5em;
  }
  .desc span:first-child {
      width: 60%;
  }

  /* tracking block */
  .track {
      display: inline-block;
      vertical-align: top;
      width: 35%;
      padding: 1em;
  }
  .track .bar {
      padding-top: 2em;
      text-align: center;
  }
  .track span:first-child {
      width: 35%;
  }
  </style>

  <style type="text/css">
  body {
    font-family: Tahoma;
    font-size: 10px;
    padding: 0 30px;
    padding-top: 30px !important;
  }
  .container {
      position: relative;
      /*border: solid 1px;
      margin: 2em;
      padding: 1em;*/
  }
  .dotted {
      border-top: dotted 1px;
      /*margin: 1em 0;*/
  }
  .divider {
      position: absolute;
      border-left: solid 1px;
      left: 60%;
      top: 0;
      bottom: 0;
  }
  span {
      display: inline-block;
      vertical-align: top;
  }

  /* description block */
  .desc {
      display: inline-block;
      vertical-align: top;
      width: 60%;
  }
  .desc .title {
      border-bottom: solid 1px;
      padding: 0.5em 0;
  }
  .desc span:first-child {
      width: 60%;
  }

  /* tracking block */
  .track {
      display: inline-block;
      vertical-align: top;
      width: 35%;
      padding: 1em;
  }
  .track .bar {
      padding-top: 2em;
      text-align: center;
  }
  .track span:first-child {
      width: 35%;
  }
  @media print {
    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table{
  display: block;
}
th, td {
  font-size: 10px;
  padding: 5px;
}
  </style>
</head>
<body onload="window.print();">

<?php if (count($topsheet) > 0) : ?>
  <?php foreach ($topsheet as $row) : ?>

  <div class="container">
       <div >
          <div >
              <?php
              print $row->plate_trans_no;
              ?>
          </div>
          <br>
           <div>
              <span>Plate Number:</span>
              <span><?php print $row->plate_number; ?></span>
          </div>
          <div>
              <span>Customer Name:</span>
              <span><?php print $row->name; ?></span>
          </div>
          <div>
              <span>Branch:</span>
              <span><?php print $row->bcode.' '.$row->branchname; ?></span>
          </div>
          <div>
              <span>Status:</span>
              <span><?php print $row->status; ?></span>
          </div>
          <div>
              <span>Engine Number:</span>
              <span><?php print $row->engine_no; ?></span>
          </div>
                  <div>
              <span>MV File:</span>
              <span><?php print $row->mvf_no; ?></span>
          </div>
          <div>
              <span>Prepared By:</span>
              <span><?php print $_SESSION['firstname'].' '.$_SESSION['lastname']; ?></span>
          </div>
          <div>
              <span>Date Received by Branch:</span>
              <span><?php print $row->received_dt; ?></span>
          </div>
                  <div>
              <span>Date Received by Customer:</span>
              <span><?php print $row->received_cust; ?></span>
          </div>
      </div>
  </div>

  <?php endforeach; ?>
<?php endif; ?>

<div class="pagebreak">
<?php
                print (isset($row)) ? '<div style="float:left; font-size:13px">Plate Transaction #:'.$row->plate_trans_no.'  </div>' : '';
                print (isset($row)) ? '<div style="float:right; font-size:13px">Branch:'.$row->bcode.' '.$row->branchname.'  </div>' : '';
  print '<br/><br/><br/>';
if(!empty($topsheet)){ ?>
          <table class="table table-bordered">
            <thead>
              <th style="width: 70%"><p>Customer Name</p></th>
              <th style="width: 15%"><p>Engine #</p></th>
              <th style="width: 15%"><p>Plate #</p></th>
              <th style="width: 30%"><p>MV File</p></th>
            </thead>
            <tbody>
              <?php

              foreach ($topsheet as $plate)
              {
                print '<tr>';
                print '<td>'.$plate->name.'</td>';
                print '<td style=" white-space: nowrap;
  overflow: hidden;">'.$plate->engine_no.'</td>';
                print '<td style=" white-space: nowrap;
  overflow: hidden;">'.$plate->plate_number.'</td>';
                print '<td style=" white-space: nowrap;
  overflow: hidden;">'.$plate->mvf_no.'</td>';

                print '</tr>';
              }
              ?>
            </tbody>
          </table>
  <?php } ?>
</div>
