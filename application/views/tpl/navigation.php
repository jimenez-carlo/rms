<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <body>
        <div class="navbar navbar-fixed-top navbar-cmc">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#">Registration Monitoring System</a>
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
                            <li <?php if(isset($nav) && $nav=="dashboard") echo 'class="active"'; ?>>
                                <a href="<?php if(isset($dir)) echo $dir; ?>home">Dashboard</a>
                            </li>
                            <!-- FLOOR PLANS -->
                            <li class="dropdown <?php if(isset($nav) && $nav=="plans") echo 'active'; ?>">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Floor Plans <i class="caret"></i>

                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>view">View</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="<?php if(isset($dir)) echo $dir; ?>upload">Upload</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- Branches -->
                            <li class="dropdown <?php if(isset($nav) && $nav=="branches") echo 'active'; ?>">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Branches <b class="caret"></b>

                                </a>
                                <ul class="dropdown-menu" id="menu1">
                                    <li>
                                        <a href="<?php if(isset($dir)) echo $dir; ?>branches">Manage</a>
                                    </li>
                                    <li>
                                        <a href="<?php if(isset($dir)) echo $dir; ?>add_branch">Add New</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>