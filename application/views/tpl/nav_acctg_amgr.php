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
                            <li class="dropdown <?php if(isset($nav) && $nav=="cash_advance") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Cash Advance <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>cash_advance">Balance</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>fund_offset">Offset</a>
                                    </li>
                                </ul>
                            </li>
                            <!--li <?php if(isset($nav) && $nav=="check_approval") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>check_approval/hold">For Management Approval <?php if($remarks_count > 0) { ?><span class="badge badge-warning"><?php echo $remarks_count; ?></span><?php } ?></a>
                            </li-->
                            <li <?php if(isset($nav) && $nav=="report") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>dashboard">Dashboard</a>
                            </li>
                            <!-- SETTINGS 
                            <li class="hide dropdown <?php if(isset($nav) && $nav=="settings") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Settings <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>user_management">Users</a>
                                    </li>
                                </ul>
                            </li>
                            -->
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>