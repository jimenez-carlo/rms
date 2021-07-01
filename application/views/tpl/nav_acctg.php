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
            <a class="brand" href="<?php echo base_url(); ?>">RMS</a>
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
                    <li class="dropdown <?php if(isset($nav) && $nav=="projected_fund") echo 'active'; ?>">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Projected Funds <i class="caret"></i></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a tabindex="-1" href="<?php echo base_url('projected_fund'); ?>">Bnew Create CA</a>
                                <a tabindex="-1" href="<?php echo base_url('projected_fund/ca_list'); ?>">Bnew CA List</a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown <?php if(isset($nav) && $nav=="e_payment") echo 'active'; ?>">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">E-Payment <i class="caret"></i></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a tabindex="-1" href="<?php echo base_url('electronic_payment'); ?>">Overview</a>
                                <a tabindex="-1" href="<?php echo base_url('electronic_payment/pending'); ?>">Pending List</a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown <?php if(isset($nav) && (in_array($nav, ['liquidation', 'return_fund','expense']))) echo 'active'; ?>">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">CA Monitoring <i class="caret"></i></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a tabindex="-1" href="<?php echo base_url('liquidation'); ?>">Liquidation</a>
                                <a tabindex="-1" href="<?php echo base_url('return_fund'); ?>">Return Fund</a>
                                <a tabindex="-1" href="<?php echo base_url('expense'); ?>">Misc Expense</a>
                            </li>
                        </ul>
                    </li>

                    <?php
                    $alert_badge = ($alert > 0) ? ' <span class="badge badge-warning">'.$alert.'</span>' : '';
                    $active = (isset($nav) && $nav=="orcr_checking") ? 'class="active"' : '';
                    print '<li '.$active.'><a href="'.base_url('orcr_checking').'">For Checking'.$alert_badge.'</a></li>';

                    $active = (isset($nav) && $nav=="sap_upload") ? 'class="active"' : '';
                    print '<li '.$active.'><a href="'.base_url('sap_upload').'">For SAP Uploading</a></li>';
                    ?>
                    <li class="<?php echo (isset($nav) && $nav === 'ric') ? 'active' : ''; ?>"><a href="<?php echo base_url('ric/monitoring'); ?>">RIC</a></li>
                    <li class="dropdown <?php echo (isset($nav) && $nav === 'disapprove') ? 'active' : ''; ?>">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Disapprove <i class="caret"></i></a>
                        <ul class="dropdown-menu">
                          <li><a href="<?php echo base_url('disapprove/resolve'); ?>">View Resolve</a></li>
                          <li><a href="<?php echo base_url('disapprove'); ?>">View Disapprove</a></li>
                        </ul>
                    </li>
                    <li class="<?php echo (isset($nav) && $nav === 'actual_docs') ? 'active' : ''; ?>"><a href="<?php echo base_url('actual_docs'); ?>">Actual Docs</a></li>
                    <li class="<?php echo (isset($nav) && $nav === 'acctg_report') ? 'active' : ''; ?>"><a href="<?php echo base_url('report'); ?>">Reports</a></li>
                    <li class="dropdown <?php echo (isset($nav) && $nav === 'repo-registration') ? 'active' : ''; ?>">
                      <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown">Repo <i class="caret"></i></a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo base_url('repo/ca'); ?>">Create CA</a></li>
                        <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">CA Monitoring</a>
                                    <ul class="dropdown-menu">
                                      <li><a href="<?php echo base_url('projected_fund/repo_ca_monitoring'); ?>">Liquidation</a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url('repo/disapproved'); ?>">Misc Expense</a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url('repo/disapproved'); ?>">Return Fund</a></li>
                                    </ul>
                        </li>
                        <li><a href="<?php echo base_url('repo/for_checking'); ?>">For Checking</a></li>
                        <li><a href="<?php echo base_url('repo/sap_uploading'); ?>">SAP Uploading</a></li>
                        <li><a href="<?php echo base_url(); ?>">Resolved</a></li>
                        <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Disapproved</a>
                                    <ul class="dropdown-menu">
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>">View Disapproved</a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>">View Resolved</a></li>
                                    </ul>
                        </li>
                        <li><a href="<?php echo base_url(); ?>">Return Fund</a></li>
                      </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
