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
<!--  -->
<!-- <center><h4>TOPSHEET</h4></center> -->
<center><h4>List of Branch Total Registration Expense, Miscellaneous Expense,
<br>and Expense Summary in a Region and Company<br>(Topsheet)</h4></center>

<div class="span12 ">

    <?php if(!empty($topsheet)) { ?>

    <p style="margin-bottom:10px;"><a href="../../topsheet">Return to Topsheet</a></p>

    <br>

    <form class="form-horizontal" method="post" action="accounting/generate">
        <fieldset>

            <div class="span6">
                <b>Transaction # :</b> <?php echo $topsheet->trans_no; ?><br>
                <i>T-[region]-[company][year][month][day]</i>
            </div>
            <div class="span4">
                <b>Region :</b> <?php echo $topsheet->region; ?>
                <br>
                <b>Company :</b> <?php echo $topsheet->company; ?>
                <br>
                <b>Rerfo Date :</b> <?php echo $topsheet->date; ?>
            </div>
            <br>
            <br>
            <br>

            <table class="table" style="margin:20px 0px;clear:both;">
                <thead>
                    <tr>
                        <th><p>Branch</p></th>
                        <th><p>Expense Date</p></th>
                        <th><p>Given Amount</p></th>
                        <th><p>Expense</p></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topsheet->sales as $sales) { ?>
                    <tr>
                        <td><?php print $sales->branch->b_code.''.$sales->branch->name; ?></td>
                        <td><?php print $sales->date ; ?></td>
                        <td><p style="text-align:right"><?php print number_format($sales->amount, 2, ".", ","); ?></p></td>
                        <td><p style="text-align:right"><?php print number_format($sales->expense, 2, ".", ","); ?></p></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th><p style="text-align:left">Total</p></th>
                        <th><p style="text-align:right">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ","); ?></p></th>
                        <th><p class="exp" style="text-align:right">&#x20b1 <?php print number_format($topsheet->total_expense, 2, ".", ","); ?></p></th>
                    </tr>
                </tfoot>
            </table>

            <table class="table" style="margin:20px 0px;clear:both;">
                <tbody>
                    <tr>
                        <td>
                            <span style="float:left">Meal:</span>
                            <span style="float:right"><?php print number_format($topsheet->meal, 2, ".", ","); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="float:left">Photocopy:</span>
                            <span style="float:right"><?php print number_format($topsheet->photocopy, 2, ".", ","); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="float:left">Transportation:</span>
                            <span style="float:right"><?php print number_format($topsheet->transportation, 2, ".", ","); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="float:left">Others: <?php if (!empty($topsheet->others_specify)) print '('.$topsheet->others_specify.')'; ?> </span>
                            <span style="float:right"><?php print number_format($topsheet->others, 2, ".", ","); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <span style="float:left">Total Miscellaneous:</span>
                            <span style="float:right">&#x20b1 <?php print number_format($topsheet->total_misc, 2, ".", ","); ?></span>
                        </th>
                    </tr>
                </tbody>
            </table>

            <table class="table" style="margin:20px 0px;clear:both;">
                <tbody>
                    <tr>
                        <th>
                            <span style="float:left">TOTAL GIVEN AMOUNT</span>
                            <span style="float:right">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ","); ?></span>
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <span style="float:left">LESS TOTAL EXPENSE</span>
                            <span style="float:right">&#x20b1 <?php print number_format($topsheet->total_expense + $topsheet->total_misc, 2, ".", ","); ?></span>
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <span style="float:left">BALANCE</span>
                            <span style="float:right">&#x20b1 <?php print number_format($topsheet->total_balance, 2, ".", ","); ?></span>
                        </th>
                    </tr>
                </tbody>
            </table>

            <div class="span6">
                <b>CHECKED BY :</b><br><br><br>
                <?php echo $topsheet->user->firstname.' '.$topsheet->user->lastname; ?>
            </div>

        </fieldset>
    </form>

    <?php } ?>

</div>
