<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html class="no-js">

    <head>
        <title><?php if(isset($title)) echo $title; else echo 'Registration Monitoring System'; ?></title>
        <link rel="shortcut icon" href="images/favicon.ico"/>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/custom.css" rel="stylesheet" media="screen">
        <link href="vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
        <link href="assets/styles.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body id="login" >
        <div class="container-login">
          <form class="form-signin" method="post">
          <center>
            <img class="signin-logo" src="images/logo-cmc.png">
            <h4 class="form-signin-heading">Registration Monitoring System Version 2.12.1</h4>
          </center>

            <?php
            if(isset($error)) echo '
                <div class="alert alert-error">
                    <button class="close" data-dismiss="alert">&times;</button>
                    <strong>Error!</strong> '.$error.'
                </div><hr>';
            if(isset($logout)) echo '
                <div class="alert alert-success">
                    <button class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> '.$logout.'
                </div><hr>';
            ?>

            <input type="text" class="input-block-level" placeholder="Username" name="username">
            <input type="password" class="input-block-level" placeholder="Password" name="password">
            <button class="btn btn-large btn-success pull-right" type="submit" name="login">Log in</button>
            <div style="clear:both;"></div>
          </form>

        </div> <!-- /container -->
    <script src="vendors/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
