<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$dir = base_url();
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
                            <li class="dropdown <?php if(isset($nav) && $nav=="projected_fund") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Projected Funds <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>projected_fund">Create CA</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>projected_fund/ca_list">CA List</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="dropdown <?php if(isset($nav) && $nav=="lto_payment") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">LTO Payment <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>lto_payment">Overview</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>lto_payment/pending">Pending List</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>lto_payment/liquidation">Liquidation</a>
                                    </li>
                                </ul>
                            </li>

                            <?php
                            $alert_badge = ($alert > 0) ? ' <span class="badge badge-warning">'.$alert.'</span>' : '';
                            $dir = (isset($dir)) ? $dir : '';

                            $active = (isset($nav) && $nav=="orcr_checking") ? 'class="active"' : '';
                            print '<li '.$active.'><a href="'.$dir.'orcr_checking">For Checking'.$alert_badge.'</a></li>';

                            $active = (isset($nav) && $nav=="sap_upload") ? 'class="active"' : '';
                            print '<li '.$active.'><a href="'.$dir.'sap_upload">For SAP Uploading</a></li>';
                            ?>

                            <li class="dropdown <?php if(isset($nav) && ($nav=='liquidation' || $nav=='return_fund')) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Liquidation <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>liquidation">Monitoring</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>return_fund">Return Fund</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Disapprove<i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                  <li><a href="<?php echo base_url(); ?>disapprove/resolve">View Resolve</a></li>
                                  <li><a href="<?php echo base_url(); ?>disapprove">View Disapprove</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
