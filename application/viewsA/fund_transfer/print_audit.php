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
<!-- window.print(); -->
<center><h4>FUND TRANSFER AUDIT</h4></center>

<div class="span12 ">

    <?php if(!empty($table)) { ?>

    <p style="margin-bottom:10px;"><a href="audit">Return to Fund Transfer Audit</a></p>

    <br>

        <table class="table">
          <thead>
            <tr>
              <th><p>Date Transferred</p></th>
              <!--th><p>Region</p></th>
              <th><p>Company</p></th-->
              <th><p>Debit Memo #</p></th>
              <th><p>Total Amount</p></th>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p>Transmittal Date</p></th>
              <th><p>Amount Breakdown</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.substr($row->date, 0, 10);
              if($_SESSION['username']=='TRSRY-HEAD') print ' (<a onclick="edit('.$row->ftid.')">EDIT</a>)';
              print '</td>';
              //print '<td>'.$row->fund->region_name.'</td>';
              //print '<td>'.$row->fund->company_name.'</td>';
              print '<td>'.$row->dm_no.'</td>';
              print '<td style="text-align:right;">&#x20b1 '.number_format($row->amount,2,'.',',').'</td>';

              //print '<td colspan=4>';
              //print '<a class="btn btn-success" onclick="detail('.$row->ftid.')">Fund Budget Details</a>';
              //if ($row->type == 1) else print '<a class="btn btn-success" onclick="detail('.$row->ftid.')">Cash Advance Details</a>';
              print $view;
              //print '</td>';
            }

            if (empty($table))
            {
              print '<tr>
                <td>No result.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>';
            }
            ?>
          </tbody>
        </table>

    <?php } ?>

</div>
