<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row-fluid">
        
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="pull-left">Search Users</div>
                    </div>
                    <div class="block-content collapse in">
                        
                        <?php if (!empty($messages)) {?>

                        <!-- MESSAGE -->
                        <div class="span12 ">
                            <?php foreach ($messages as $message) { ?>
                                <div class="alert alert-warning">
                                    <button class="close" data-dismiss="alert"></button>
                                    <b>Reminder:</b> <?php print $message; ?>
                                </div>
                            <?php } ?>
                        </div>

                        <?php } ?>

                        <form class="form-horizontal" method="post">
                          <fieldset>


                          <!-- Search Form -->
                          <div class="row-fluid">
                            <div class="span12 ">
                              <div class="control-group">
                                <label class="control-label">Username</label>
                                <div class="controls">
                                  <input type="text" name="username">
                                </div>
                              </div>
                              <div class="form-actions">
                                <input type="submit" name="search" class="btn btn-success" value="Search">
                              </div>
                            </div>
                          </div>

                          </fieldset>
                        </form>
                  </div>
              </div>
          </div>


                <!-- Search Results -->
                <?php if (isset($table)): ?>
                <div class="span12" id="content">
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="pull-left">Search Results <?php echo '('.count($table).')'; ?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                                <th width="160">Manage</th>
                                                <th>Username</th>
                                                <th>Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ( count($table) > 0 ): ?>
                                                <?php foreach($table as $row): ?>
                                                    <tr>
                                                        <td><center><a class="btn btn-mini btn-inverse" href="./users/reset/<?php echo $row->uid; ?>"><i class="icon-repeat"></i> RESET PASSWORD</a></center></td>
                                                        <td><?php echo $row->username; ?></td>
                                                        <td><?php echo $row->fullname; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td></td>
                                                    <td>No results found.</td>
                                                    <td></td>
                                                </tr>   
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                </div>
                <?php endif; ?>



        </div>