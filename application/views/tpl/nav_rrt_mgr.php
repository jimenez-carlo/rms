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
                            <!--li class="dropdown <?php if(isset($nav) && $nav=="confirmation") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Confirmation <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>confirm/fund">Fund</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>confirm/cash_advance">Old RMS</a>
                                    </li>
                                </ul>
                            </li-->
                            <?php
                            $reprint = $topsheet+$rerfo;
                            $reprint = ($reprint == '0') ? '' : '<span class="badge badge-warning">'.$reprint.'</span>';
                            $topsheet = ($topsheet == '0') ? '' : '<span class="badge badge-warning">'.$topsheet.'</span>';
                            $rerfo = ($rerfo == '0') ? '' : '<span class="badge badge-warning">'.$rerfo.'</span>';
                            ?>
                            <li <?php if(isset($nav) && $nav=="sales") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>sales">Customer Status</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="projected_fund") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>projected_fund">Projected Fund</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="liquidation") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>liquidation">Liquidation</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="bmi_cr") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>bmi_cr">BMI CR Transmittal</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="reprint") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Reprinting Request <?php print $reprint; ?> <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>reprint/topsheet">Topsheet <?php print $topsheet; ?></a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>reprint/rerfo">Rerfo <?php print $rerfo; ?></a>
                                    </li>
                                </ul>
                            </li>
                            <li <?php if(isset($nav) && $nav=="bank") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>fund/bank">RRT Bank Accounts</a>
                            </li>
                            <!-- <li <?php if(isset($nav) && $nav=="report") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>sales/missing_branch">Missing Branches Report</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="report") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Report <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>dashboard">Dashboard</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>fund/audit">Fund Audit</a>
                                    </li>
                                </ul>
                            </li> -->
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
