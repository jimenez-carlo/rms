<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html class="no-js">

    <head>
        <title><?php echo 'RMS'; if(isset($title)) echo ' | '.$title; ?></title>
        <link rel="shortcut icon" href="<?php print base_url('images/favicon.ico') ?>">
        <!-- Bootstrap -->
        <link href="<?php print base_url('bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('bootstrap/css/bootstrap-responsive.min.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('bootstrap/font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
        <link href="<?php print base_url('bootstrap/css/custom.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('assets/styles.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('assets/DT_bootstrap.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('vendors/select2.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('vendors/datepicker.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('vendors/uniform.default.css') ?>" rel="stylesheet" media="screen">
        <link href="<?php print base_url('bootstrap/css/alerty.css') ?>" rel="stylesheet" media="screen">
        <?php if(isset($link)) echo $link; ?>

        <!-- JQUERY -->
        <script src="<?php print base_url('vendors/jquery-1.9.1.min.js') ?>"></script>
        <script src="<?php print base_url('bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php print base_url('vendors/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php print base_url('assets/DT_bootstrap.js') ?>"></script>
        <script src="<?php print base_url('vendors/bootstrap-datepicker.js') ?>"></script>

        <script src="<?php print base_url('vendors/jquery.uniform.min.js') ?>"></script>
        <script src="<?php print base_url('vendors/select2.min.js') ?>"></script>
        <script src="<?php print base_url('vendors/modernizr-2.6.2-respond-1.1.0.min.js') ?>"></script>
        <script src="<?php print base_url('assets/autocomma.js') ?>"></script>
        <script src="<?php print base_url('assets/js/alerty.js') ?>"></script>
        <script src="<?php print base_url('assets/js/custom_alert.js') ?>"></script>

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <style>
        *[disabled] {cursor: not-allowed !important;}
        .form-horizontal {margin-top: 0px;}
        .form-horizontal .control-group { margin-bottom: 10px; }
        .modal.fade {top: -100%;}
        .table tfoot {white-space: nowrap;}
        </style>
    </head>
    <body>
