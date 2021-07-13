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
                            <li class="dropdown <?php if(isset($nav) && $nav=="si") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">SI <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="<?php echo base_url(); ?>si/printing">Printing</a></li>
                                    <li><a tabindex="-1" href="<?php echo base_url(); ?>si/transmittal">Transmittal</a></li>
                                    <li><a tabindex="-1" href="<?php echo base_url(); ?>si/self_regn">Self Registration</a></li>
                                </ul>
                            </li>
                            <li <?php if(isset($nav) && $nav=="pending") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>lto_pending">LTO</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="nru") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>nru">NRU</a>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && in_array($nav,["registration","expense", "ric"])) echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Expenses <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Registration</a>
                                    <ul class="dropdown-menu">
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>registration">Engine Search</a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>registration/pending_list">Pending List</a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>disapprove">Disapproved</a></li>
                                    </ul>
                                  </li>
                                  <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Miscellaneous</a>
                                    <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="<?php echo base_url(); ?>expense/add">Add</a></li>
                                    <li><a tabindex="-1" href="<?php echo base_url(); ?>expense">List</a></li>
                                    <li><a tabindex="-1" href="<?php echo base_url(); ?>expense/ca_ref">CA Reference Update (Temporary)</a></li>
                                    </ul>
                                  </li>
                                  <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">RIC</a>
                                    <ul class="dropdown-menu">
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>ric/penalty">Create RIC for Penalty </a></li>
                                      <li><a tabindex="-1" href="<?php echo base_url(); ?>ric/monitoring">RIC Monitoring </a></li>
                                    </ul>
                                  </li>
                                </ul>
                            </li>
                            <li class="dropdown <?php if(isset($nav) && $nav=="plate") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Plate <i class="caret"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>plate/encode_pnumber">Update Plate</a>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>plate/transmittal">Plate Transmittal</a>
                                        <a tabindex="-1" href="<?php echo base_url(); ?>plate/pending_list">Pending List</a>
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
                            <li <?php if(isset($nav) && $nav=="actual_docs") echo 'class="active"'; ?>>
                              <a href="<?php echo base_url(); ?>actual_docs">Actual Docs</a>
                            </li>
                            <li <?php if(isset($nav) && $nav=="matrix") echo 'class="active"'; ?>>
                                <a href="<?php echo base_url(); ?>repo/matrix">Tip Matrix</a>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
