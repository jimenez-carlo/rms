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
                            <li class="dropdown <?php if(isset($nav) && ($nav=="fund" || $nav=="ca_list")) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Fund <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('fund'); ?>">Withdraw/Deposit</a>
                                        <!-- <a tabindex="-1" href="<?php echo base_url('checks'); ?>">Checks</a> -->
                                        <?php if($this->session->region !== 1): ?>
                                        <a tabindex="-1" href="<?php echo base_url('projected_fund/ca_list'); ?>">View CA Status</a>
                                        <?php endif; ?>
                                        <a tabindex="-1" href="<?php echo base_url('fund/audit'); ?>">Audit</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- <li <?php if(isset($nav) && $nav=="fund") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url('fund'); ?>">Fund</a>
                            </li> -->
                            <li <?php if(isset($nav) && $nav=="sales") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url('sales'); ?>">Customer Status</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && in_array($nav,["registration","expense","disapprove"])) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Expenses <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Registration</a>
                                    <ul class="dropdown-menu">
                                      <li><a tabindex="-1" href="<?php echo base_url('disapprove'); ?>">Disapprove</a></li>
                                    </ul>
                                  </li>
                                  <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Miscellaneous</a>
                                    <ul class="dropdown-menu">
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>expense">List</a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>expense/ca_ref">CA Reference Update (Temporary)</a></li>
                                    </ul>
                                  </li>
                                  <li><a tabindex="-1" href="<?php echo base_url('ric/penalty'); ?>">Penalty RIC </a></li>
                                </ul>
                            </li>

                            <li class="dropdown <?php if(isset($nav) && $nav=="plate") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Plate <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('plate/branch_list'); ?>">Plate Transmittal</a>
                                        <a tabindex="-1" href="<?php echo base_url('plate/pending_list'); ?>">Pending List</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="e_payment") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">E-Payment <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('electronic_payment'); ?>">List</a>
                                        <a tabindex="-1" href="<?php echo base_url('electronic_payment/extract'); ?>">Extract</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && ($nav=="liquidation" || $nav=="return_fund")) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Cash Advance <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="<?php echo base_url('liquidation'); ?>">Liquidation</a></li>
                                    <li class="dropdown-submenu">
                                      <a tabindex="-1" href="#">Miscellaneous</a>
                                      <ul class="dropdown-menu">
                                        <li><a tabindex="-1" href="<?php echo base_url(); ?>expense">List</a></li>
                                        <li><a tabindex="-1" href="<?php echo base_url(); ?>expense/ca_ref">CA Reference Update (Temporary)</a></li>
                                      </ul>
                                    </li>
                                    <li><a tabindex="-1" href="<?php echo base_url('return_fund'); ?>">Return Fund</a></li>
                                </ul>
                            </li>
                            <li <?php if(isset($nav) && $nav=="rerfo") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url('rerfo'); ?>">Rerfo</a>
                            </li>

                            <li class="dropdown <?php if(isset($nav) && $nav=="topsheet") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Topsheet <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('topsheet'); ?>">List</a>
                                        <a tabindex="-1" href="<?php echo base_url('topsheet/create'); ?>">Create</a>
                                    </li>
                                </ul>
                            </li>
                            <li <?php if(isset($nav) && $nav=="transmittal") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url('transmittal'); ?>">Transmittal</a>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
