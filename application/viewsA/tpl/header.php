<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html class="no-js">
    
    <head>
        <title><?php if(isset($title)) echo $title; else echo 'Registration Monitoring System'; ?></title>
        <link rel="shortcut icon" href="<?php if(isset($dir)) echo $dir; ?>images/favicon.ico"/>
        <!-- Bootstrap -->
        <link href="<?php if(isset($dir)) echo $dir; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="<?php if(isset($dir)) echo $dir; ?>bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="<?php if(isset($dir)) echo $dir; ?>bootstrap/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href="<?php if(isset($dir)) echo $dir; ?>bootstrap/css/custom.css" rel="stylesheet" media="screen">
        <link href="<?php if(isset($dir)) echo $dir; ?>assets/styles.css" rel="stylesheet" media="screen">
        <link href="<?php if(isset($dir)) echo $dir; ?>vendors/select2.css" rel="stylesheet" media="screen">
        <link href="<?php if(isset($dir)) echo $dir; ?>vendors/datepicker.css" rel="stylesheet" media="screen">
        <?php if(isset($link)) echo $link; ?>
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="<?php if(isset($dir)) echo $dir; ?>vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <style>
        .modal.fade {top: -100%;}
        .table tfoot {white-space: nowrap;}
        </style>
    </head>
    <body>