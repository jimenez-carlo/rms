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
      font-size: 75%;
      font-family: Tahoma;
      padding-top: 10px !important;
    }
    h4 {
      padding-bottom: 0px;
    }
    table {
      margin-top: 10px !important;
      width:90%;
      border-collapse: collapse;
    }
    th, tr, td {
      padding: 0px !important;
      border: 1px solid;
      border-color: #000 !important;
    }
    .span4 {
        width: 40%;
        display: inline-block;
    }
    .table tfoot {
        white-space: nowrap;
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
        width: 40%;
        display: inline-block;
    }
    </style>
</head>
<body onload="window.print();">
<!--  -->
<!-- <center><h4>RERFO</h4></center> -->
<center><h4>List of Sales and its Total Registration Expense<br>(Rerfo)</h4></center>

<div class="span12">

    <form class="form-horizontal" method="post" action="accounting/generate">
        <fieldset>

            <div class="span4">
                <b>Transaction # :</b> <?php echo $rerfo->trans_no; ?><br>
                <i>R-[branch]-[year][month][day]</i>
            </div>
            <div class="span4">
                <b>Branch :</b> <?php echo $rerfo->bcode.' '.$rerfo->bname; ?>
                <br>
                <b>Expense Date :</b> <?php echo $rerfo->date; ?>
            </div>

            <table class="table" style="margin:20px 0px;clear:both;">
                <thead>
                    <tr>
                        <th><p>Date Sold</p></th>
                        <th><p>Type of Sales</p></th>
                        <th><p>Customer Name</p></th>
                        <th><p>Engine #</p></th>
                        <th><p>AR #</p></th>

                        <th><p>Registration Type</p></th>
                        <th><p>Amount Given</p></th>
                        <th><p>LTO Registration</p></th>
                        <!-- <th><p>LTO Tip</p></th> -->
                        <th><p>CR #</p></th>
                        <th><p>MV File #</p></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tot_amt = 0;
                    $tot_reg = 0;
                    $tot_tip = 0;
                    foreach ($rerfo->sales as $sales) {
                    ?>

                    <tr>
                        <td><?php print $sales->date_sold; ?></td>
                        <td><?php print $sales->sales_type; ?></td>
                        <td><?php print $sales->first_name." ".$sales->last_name;; ?></td>
                        <td><?php print $sales->engine_no; ?></td>
                        <td><?php print $sales->ar_no; ?></td>

                        <td><p><?php print $sales->registration_type; ?></p></td>
                        <td><p style="text-align:right!important;">
                            <?php print number_format($sales->amount,2,'.',','); ?>
                        </p></td>
                        <td><p style="text-align:right!important;">
                            <?php print number_format($sales->registration,2,'.',','); ?>
                        </p></td>
                        <!-- <td><p style="text-align:right!important;">
                            <?php print number_format($sales->tip,2,'.',','); ?>
                        </p></td> -->
                        <td><p>
                            <?php print $sales->cr_no; ?>
                        </p></td>
                        <td><p>
                            <?php print $sales->mvf_no; ?>
                        </p></td>
                    </tr>

                    <?php
                        $tot_amt += $sales->amount;
                        $tot_reg += $sales->registration;
                        $tot_tip += $sales->tip;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>

                        <td><b></b></td>
                        <td><b>Total</b></td>
                        <td><b><p style="text-align:right!important;">&#x20b1 <?php print number_format($tot_amt, 2, ".", ","); ?></p></b></td>
                        <td><b><p class="exp" style="text-align:right!important;">&#x20b1 <?php print number_format($tot_reg, 2, ".", ","); ?></p></b></td>
                        <!-- <td><b><p class="exp" style="text-align:right!important;">&#x20b1 <?php print number_format($tot_tip, 2, ".", ","); ?></p></b></td> -->
                        <td><b></b></td>
                        <td><b></b></td>
                    </tr>
                </tfoot>
            </table>

            <div class="span6">
                <b>PREPARED BY :</b>
                <?php
                foreach ($rerfo->users as $user) {
                    print "<br><br><br>";
                    print $user->firstname.' '.$user->lastname;
                }
                ?>
            </div>
            <div class="span4">
                <b>REVIEWED BY :</b><br><br><br>
                <?php // echo $rerfo->user->firstname.' '.$rerfo->user->lastname; ?>
            </div>

        </fieldset>
    </form>

</div>
