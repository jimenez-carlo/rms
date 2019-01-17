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

    /* hide link */
    a {
        display:none;
    }
    </style>

    <style type="text/css">
    body {
      font-family: Tahoma;
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
</head>
<body onload="">
<!-- window.print(); -->

<a href="../transmittal">Return to Transmittal</a>

<?php
foreach ($result as $row)
{
?>

<div class="container">
    <div class="dotted"></div>
    <div class="desc">
        <div class="title">Shipment Description:</div>
        <ul>
            <?php
            foreach ($row->sales as $sales)
            {
                print "<li>";
                if ($row->sales_type == 0)
                {
                    print "<span>OR for ".$sales->last_name.", "
                        .$sales->first_name."</span>";
                    print "<span>CR #: ".$sales->cr_no."</span>";
                }
                else
                {
                    print "<span>OR for ".$sales->last_name.", "
                        .$sales->first_name."</span>";
                    print "<span></span>";
                }
                print "</li>";
            }
            ?>
        </ul>
    </div>

    <div class="divider"></div>

     <div class="track">
        <div class="bar"><?php print $row->transmittal->trans_no; ?></div>
        <br>
        <div>
            <span>Tracking No:</span>
            <span><?php print $row->transmittal->trans_no; ?></span>
        </div>
        <div>
            <span>Destination:</span>
            <span><?php print $row->branch->b_code.' '.$row->branch->name; ?></span>
        </div>
        <div>
            <span>Attention To:</span>
            <span>c/o: BS/BCH</span>
        </div>
        <br>
        <div>
            <span>Origin:</span>
            <span><?php print $row->branch->region_name; ?> RRT</span>
        </div>
        <div>
            <span>Prepared By:</span>
            <span><?php print $row->transmittal->user; ?></span>
        </div>
        <div>
            <span>Date Sent:</span>
            <span><?php print $row->transmittal->trans_date; ?></span>
        </div>
    </div>
</div>

<?php
}
?>

<?php
foreach ($result as $row)
{
    if($row->sales_type) {
?>

 <div class="container">
    <div class="dotted"></div>
    <div class="desc">
        <div class="title">Shipment Description:</div>
        <ul>
            <?php
            foreach ($row->sales as $sales)
            {
                print "<li>";
                if ($row->sales_type == 1)
                {
                    print "<span>".$sales->last_name.", "
                        .$sales->first_name."</span>";
                    print "<span>CR #: ".$sales->cr_no."</span>";
                }
                print "</li>";
            }
            ?>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="track">
        <div class="bar"><?php print $row->transmittal->track_no; ?></div>
        <br>
        <div>
            <span>Tracking No:</span>
            <span><?php print $row->transmittal->track_no; ?></span>
        </div>
        <div>
            <span>Destination:</span>
            <span>Bank of Makati</span>
        </div>
        <div>
            <span>Attention To:</span>
            <span>c/o: LO Officer</span>
        </div>
        <br>
        <div>
            <span>Origin:</span>
            <span><?php print $row->branch->region_name; ?> RRT</span>
        </div>
        <div>
            <span>Prepared By:</span>
            <span><?php print $row->transmittal->track_user; ?></span>
        </div>
        <div>
            <span>Date Sent:</span>
            <span><?php print $row->transmittal->track_date; ?></span>
        </div>
    </div>
</div>

<?php
    }
}
?>