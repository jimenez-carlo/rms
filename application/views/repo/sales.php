<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Repo Encode Sale</div>
      </div>
      <div class="block-content collapse in">
        <?php echo form_open('', ["class" => "form-inline", "onsubmit" => "return confirm('Are you sure?');"]); ?>
<!--
          <fieldset>
            <legend>
              <p class="offset4" style="margin-top:0;margin-bottom:0;">
                CA Reference# <?php echo 'REPO-'.$_SESSION['branch_code'].'-'.date('Ymd'); ?>
              </p>
            </legend>
-->
            <div class="form-inline row">
              <div class="control-group span2 offset4">
                <label class="control-label">Engine#</label>
                <div class="controls">
                  <input  id="engine-no" type="text" name="engine_no" value="<?php echo $repo['engine_no']; ?>" disabled>
                </div>
              </div>
              <div class="control-group span2" style="margin-left:2rem;">
                <label class="control-label">Chassis#</label>
                <div class="controls">
                  <input type="text" value="<?php echo $repo['chassis_no']; ?>" disabled>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset4">
                <label class="control-label">MAT#</label>
                <div class="controls">
                  <input type="text" value="<?php echo $repo['mat_no']; ?>" disabled>
                </div>
              </div>
              <div class="control-group span2" style="margin-left:2rem;">
                <label class="control-label">MVF No.</label>
                <div class="controls">
                  <input type="text" value="<?php echo $repo['mvf_no']; ?>" disabled>
                </div>
              </div>
            </div>
            <br>
            <div class="form-inline row">
              <div class="control-group span2 offset4 <?php echo (form_error('repo_sale[rsf_num]')) ? 'error' : ''; ?>">
                <label class="control-label" for="rsf">RSF#</label>
                <div class="controls">
                  <input id="rsf" type="text" name="repo_sale[rsf_num]" <?php echo 'value="'.set_value('repo_sale[rsf_num]', '').'"'; ?> required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('repo_sale[date_sold]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="date-sold">Date Sold</label>
                <div class="controls">
                  <input id="date-sold" class="datepicker" type="text" placeholder="yyyy-mm-dd" name="repo_sale[date_sold]" value="<?php echo set_value('repo_sale[date_sold]', $repo['date_sold']); ?>" autocomplete="off" required>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset4 <?php echo (form_error('customer[cust_code]')) ? 'error' : ''; ?>">
                <label class="control-label" for="get-cust">Customer Code</label>
                <div class="controls">
                  <input id="get-cust" type="text" name="customer[cust_code]" <?php echo 'value="'.set_value('customer[cust_code]', '').'"'; ?> autocomplete="off" required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('customer[date_of_birth]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="bday">Date of Birth</label>
                <div class="controls">
                  <input id="bday" class="datepicker" type="text" placeholder="yyyy-mm-dd" name="customer[date_of_birth]" <?php echo 'value="'.set_value('customer[date_of_birth]', '').'"'; ?> autocomplete="off" required>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset4 <?php echo (form_error('customer[first_name]')) ? 'error' : ''; ?>">
                <label class="control-label" for="first-name">First Name</label>
                <div class="controls">
                  <input id="first-name" type="text" name="customer[first_name]" <?php echo 'value="'.set_value('customer[first_name]', '').'"'; ?> required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('customer[middle_name]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="middle-name">Middle Name</label>
                <div class="controls">
                  <input id="middle-name" type="text" name="customer[middle_name]" <?php echo 'value="'.set_value('customer[middle_name]', '').'"'; ?>>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset4 <?php echo (form_error('customer[last_name]')) ? 'error' : ''; ?>">
                <label class="control-label" for="last-name">Last Name</label>
                <div class="controls">
                  <input id="last-name" type="text" name="customer[last_name]" <?php echo 'value="'.set_value('customer[last_name]', '').'"'; ?> required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('customer[suffix]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="suffix">Suffix<span class="help-inline">(e.g. Jr., Sr.)</span></label>
                <div class="controls">
                  <input id="suffix" type="text" name="customer[suffix]" <?php echo 'value="'.set_value('customer[suffix]', '').'"'; ?>>
                </div>
              </div>
            </div>
            <div class="form-inline row">
              <div class="control-group span2 offset4 <?php echo (form_error('customer[email]')) ? 'error' : ''; ?>">
                <label class="control-label" for="email">Email</label>
                <div class="controls">
                  <input id="email" type="email" name="customer[email]" <?php echo 'value="'.set_value('customer[email]', '').'"'; ?> required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('customer[phone_number]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="phone">Mobile No.</label>
                <div class="controls">
                  <input id="phone" type="text" name="customer[phone_number]" <?php echo 'value="'.set_value('customer[phone_number]', '').'"'; ?> required>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset4 <?php echo (form_error('repo_sale[ar_num]')) ? 'error' : ''; ?>">
                <label class="control-label" for="ar-num">AR#</label>
                <div class="controls">
                  <input id="ar-num" type="text" name="repo_sale[ar_num]" <?php echo 'value="'.set_value('repo_sale[ar_num]', '').'"'; ?> required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('repo_sale[ar_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="ar-amount">Amount Given</label>
                <div class="controls">
                  <input id="ar-amount" type="text" name="repo_sale[ar_amt]" placeholder="0.00" <?php echo 'value="'.set_value('repo_sale[ar_amt]', '').'"'; ?> required>
                </div>
              </div>
            </div>

            <div class="row">
              <button id="save" class="btn btn-success offset4" type="submit" name="save" value="true">Save</button>
            </div>
<!--
          </fieldset>
-->
        </form>
      </div>
    </div>
  </div>
</div>

