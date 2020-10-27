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
                                        <a tabindex="-1" href="<?php echo base_url(); ?>profile">Profile</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>logout">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <?php $orcr = ''; // ($orcr == '0') ? '' : ' <span class="badge badge-warning">'.$orcr.'</span>'; ?>
                        <ul class="nav">
                            <li <?php if(isset($nav) && $nav=="sales") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>sales">Customer Status</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="transmittal") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>transmittal/branch">Transmittal</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="disapprove") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>disapprove">Disapproved Sales</a>
                            </li>
                            <li class="dropdown <?php echo (isset($nav) && $nav=="repo-registration") ? 'active' : ''; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Repo<i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('repo/in'); ?>">Repo In</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('repo'); ?>">Inventory</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('repo/batch'); ?>">CA Batch</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url('repo/misc_exp'); ?>">Misc Expense</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
