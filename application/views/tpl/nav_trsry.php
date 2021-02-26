<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
        <div class="navbar navbar-fixed-top navbar-cmc">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php if(isset($dir)) echo $dir; ?>">RMS</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav pull-right">
                            <li class="dropdown <?php if(isset($nav) && $nav=="profile") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user" style="background-image: url(bootstrap/img/glyphicons-halflings-white.png);"></i> <?php echo substr($_SESSION['firstname'],0,1) .'. '.$_SESSION['lastname']. ' ('.$_SESSION['username'].')'; ?> <i class="caret"></i>

                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('profile'); ?>">Profile</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('logout'); ?>">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav">
                            <li class="dropdown <?php if(isset($nav) && $nav=="fund_transfer") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Fund Transfer <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('fund_transfer'); ?>">For Process</a>
                                        <a tabindex="-1" href="<?php echo base_url('fund_transfer/for_deposit'); ?>">For Deposit</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('fund_transfer/for_deposit_repo') ?>">Repo For Deposit</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="dropdown <?php if(isset($nav) && $nav=="e_payment") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">E-Payment <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="<?php echo base_url('electronic_payment'); ?>">Overview</a></li>
                                    <li><a tabindex="-1" href="<?php echo base_url('electronic_payment/processing'); ?>">For Process</a></li>
                                    <li><a tabindex="-1" href="<?php echo base_url('electronic_payment/for_deposit'); ?>">For Deposit</a></li>
                                </ul>
                            </li>

                            <li <?php if(isset($nav) && $nav=="projected_fund") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>projected_fund">Projected Funds</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="deposited_fund") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">CA List <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('projected_fund/ca_list'); ?>">Brand New</a>
                                        <a tabindex="-1" href="<?php echo base_url('projected_fund/repo_ca_list'); ?>">Repo</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="<?php echo (isset($nav) && $nav === 'ric') ? 'active' : ''; ?>"><a href="<?php echo base_url('ric/monitoring'); ?>">RIC</a></li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
