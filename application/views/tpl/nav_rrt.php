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
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                                  <i class="icon-user" style="background-image: url(bootstrap/img/glyphicons-halflings-white.png);"></i>
                                    <?php echo substr($_SESSION['firstname'],0,1) .'. '.$_SESSION['lastname']. ' ('.$_SESSION['username'].')'; ?>
                                  <i class="caret"></i>
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
                        <ul class="nav">
                            <li <?php if(isset($nav) && $nav=="sales") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>sales">Customer Status</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="pending") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>lto_pending">LTO</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="nru") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>nru">NRU</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="registration") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Registration <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>registration">Engine Search</a>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>registration/pending_list">Pending List</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="plate") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Plate <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>plate/UpdatePlate_BS">Update Plate</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>plate/branch_list">Plate Transmittal</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>plate/pending_list">Pending List</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="expense") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Miscellaneous <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>expense">List</a>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>expense/ca_ref">CA Reference Update (Temporary)</a>
                                    </li>
                                </ul>
                            </li>
                            <li <?php if(isset($nav) && $nav=="rerfo") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>rerfo">Rerfo</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="topsheet") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>topsheet">Topsheet</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="transmittal") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>transmittal">Transmittal</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="disapprove") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>disapprove">Disapprove</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="actual_docs") echo 'class="active"'; ?>>
                              <a href="<?php echo base_url(); ?>actual_docs">Actual Docs</a>
                            </li>
                            <!-- <li class="dropdown <?php if(isset($nav) && $nav=="attachment") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Attachment <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>attachment">By Engine</a>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>attachment/pending_list">Pending List</a>
                                    </li>
                                </ul>
                            </li> -->
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
