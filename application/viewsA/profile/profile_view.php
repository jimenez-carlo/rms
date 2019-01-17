<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include('assets/alert_messages.php');
?>

        <div class="container-fluid">
            <div class="row-fluid">


                <!-- Edit Profile -->
                <div class="span12" id="content">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="pull-left">Edit Profile</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                     <form class="form-horizontal" method="post" action="profile/save_profile" id="form_profile">
                                      <fieldset>
                                        <?php 
                                        if(isset($_GET['submit'])) {
                                            if($_GET['submit']==1) {
                                                echo $changes_saved;
                                                header("refresh:3;url=profile");
                                            }                                         
                                        }
                                        ?>
                                        <div class="alert alert-error alert-error-profile hide">
                                            <button class="close" data-dismiss="alert">&times;</button>
                                            You have some form errors. Please check below.
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">First Name<span class="required">*</span></label>
                                          <div class="controls">
                                            <input name="firstname" id="firstname" class="input-xlarge focused" type="text" value="<?php if(isset($firstname)) echo $firstname; ?>">
                                          </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">Middle Name<span class="required">*</span></label>
                                          <div class="controls">
                                            <input name="middlename" id="middlename" class="input-xlarge focused" type="text" value="<?php if(isset($middlename)) echo $middlename; ?>">
                                          </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">Last Name<span class="required">*</span></label>
                                          <div class="controls">
                                            <input name="lastname" id="lastname" class="input-xlarge focused" type="text" value="<?php if(isset($lastname)) echo $lastname; ?>">
                                          </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">Extension Name</label>
                                          <div class="controls">
                                            <input name="extname" class="input-xlarge focused" type="text" value="<?php if(isset($_GET['extname'])) echo $_GET['extname']; ?>">
                                          </div>
                                        </div>
                                        <div class="form-actions">
                                          <button type="submit" name="save" class="btn btn-primary">Save</button>
                                        </div>
                                      </fieldset>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                </div>

                <!-- Change Password -->
                <div class="span12" id="content">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="pull-left">Change Password</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                     <form class="form-horizontal" method="post" action="profile/save_password" id="form_password">
                                      <fieldset>
                                        <?php 
                                        if(isset($_GET['submit'])) {
                                            if($_GET['submit']==2) echo $changes_saved;
                                            if($_GET['submit']==3) echo $old_pw_incorrect;                                            
                                        }
                                        ?>
                                        <div class="alert alert-error hide">
                                            <button class="close" data-dismiss="alert">&times;</button>
                                            You have some form errors. Please check below.
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">Enter New Password<span class="required">*</span></label>
                                          <div class="controls">
                                            <input name="pw1" class="input-xlarge focused" id="pw1" type="password" value="<?php if(isset($pw1)) echo $pw1; ?>">
                                          </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">Confirm New Password<span class="required">*</span></label>
                                          <div class="controls">
                                            <input name="pw2" class="input-xlarge focused" id="pw2" type="password" value="<?php if(isset($pw2)) echo $pw2; ?>">
                                          </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label" for="focusedInput">Enter Old Password<span class="required">*</span></label>
                                          <div class="controls">
                                            <input name="pw3" class="input-xlarge focused" id="pw3" type="password" value="<?php if(isset($pw3)) echo $pw3; ?>">
                                          </div>
                                        </div>
                                        <div class="form-actions">
                                          <button type="submit" name="save" class="btn btn-primary">Save</button>
                                        </div>
                                      </fieldset>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                </div>
                
            </div>