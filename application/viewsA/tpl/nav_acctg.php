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
                            <li class="dropdown <?php if(isset($nav) && $nav=="projected_fund") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Projected Funds <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>projected_fund">Create Voucher</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>projected_fund/voucher">Voucher List</a>
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
                            <li class="dropdown <?php if(isset($nav) && $nav=="liquidation") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Liquidation <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>liquidation">List of Transfers</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>liquidation/topsheets">Liquidated Topsheets</a>
                                    </li>
                                </ul>
                            </li>
                            <!--li <?php if(isset($nav) && $nav=="report") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>dashboard">Dashboard</a>
                            </li-->
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>