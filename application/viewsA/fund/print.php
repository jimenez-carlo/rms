<html class="no-js">
<head>
    <title>Fund Budget - Print</title>
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
      border: 1px solid;
      border-color: #000 !important;
    }
    .span4 {
        width: 30%;
        display: inline-block;
        margin: 1em;
    }
    .span6 {
        width: 50%;
        display: inline-block;
        margin: 1em;
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
    .span4 {
        width: 30%;
        display: inline-block;
        margin: 1em;
    }
    .span6 {
        width: 50%;
        display: inline-block;
        margin: 1em;
    }
    </style>
</head>
<body onload="window.print();">
<!-- window.print(); -->
<center><h4>FUND BUDGET</h4></center>

<div class="span12 ">

    <?php if(!empty($table)) { ?>

    <a href="">Return to Fund Budget Page</a>

    <br><br>

        <table class="table">
          <thead>
            <tr>
              <th><p>Company</p></th>
              <th><p>Cash in Bank</p></th>
              <th><p>Cash on Hand</p></th>
              <th><p>Pending at LTO</p></th>
              <th><p>Pending at ACCT</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              $key = '['.$row->fid.']';
              print '<tr>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.$row->fund.'</td>';
              print '<td>'.$row->cash_on_hand.'</td>';
              print '<td>'.$row->lto_pending.'</td>';
              print '<td>'.$row->acct_pending.'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td colspan=20>No result.</td></tr>';
            }
            ?>
          </tbody>
        </table>

    <?php } ?>

</div>
