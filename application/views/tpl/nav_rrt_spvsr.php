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
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>profile">Profile</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>logout">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav">
                            <li class="dropdown <?php if(isset($nav) && ($nav=="fund" || $nav=="ca_list")) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Fund <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>fund">Withdraw/Deposit</a>
                                        <!-- <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>checks">Checks</a> -->
					<?php if($this->session->region !== 1): ?>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>projected_fund/ca_list">View CA Status</a>
					<?php endif; ?>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>fund/audit">Audit</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- <li <?php if(isset($nav) && $nav=="fund") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>fund">Fund</a>
                            </li> -->
                            <li <?php if(isset($nav) && $nav=="sales") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>sales">Customer Status</a>
                            </li>


                            <li class="dropdown <?php if(isset($nav) && $nav=="lto_payment") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">LTO Payment <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>lto_payment">List</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>lto_payment/extract">Extract</a>
                                    </li>
                                </ul>
                            </li>


                            <li class="dropdown <?php if(isset($nav) && $nav=="expense") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Miscellaneous <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>expense">List</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>expense/ca_ref">CA Reference Update (Temporary)</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- <li <?php if(isset($nav) && $nav=="expense") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>expense">Miscellaneous</a>
                            </li> -->
                            <li <?php if(isset($nav) && $nav=="rerfo") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>rerfo">Rerfo</a>
                            </li>
                            <!-- <li <?php if(isset($nav) && $nav=="topsheet") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>topsheet">Topsheet</a>
                            </li> -->
                            <li class="dropdown <?php if(isset($nav) && ($nav=="topsheet" || $nav=="disapprove")) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Topsheet <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>topsheet">List</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>topsheet/create">Create</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>disapprove">Disapprove</a>
                                    </li>
                                </ul>
                            </li>
                            <li <?php if(isset($nav) && $nav=="transmittal") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>transmittal">Transmittal</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && ($nav=="liquidation" || $nav=="return_fund")) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Liquidation <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>liquidation">Liquidation</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>return_fund">Return Fund</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- <li class="dropdown <?php if(isset($nav) && $nav=="report") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Report <?php if($not_received > 0) { ?><span class="badge badge-warning"><?php echo $not_received; ?></span><?php } ?> <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>dashboard">Dashboard</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>dashboard/unprocessed">Unprocessed</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>expense/rejected">Expense (Rejected)</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>fund/passbook">Fund Audit</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>topsheet/status">Topsheet Status</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>transmittal/status">Transmittal Status <?php if($not_received > 0) { ?><span class="badge badge-warning"><?php echo $not_received; ?></span><?php } ?></a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>self_registration">Self Registration</a>
                                    </li>
                                </ul>
                            </li> -->
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
