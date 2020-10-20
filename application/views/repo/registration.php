<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Repo Registration</div>
      </div>
      <div class="block-content collapse in">
        <?php echo form_open_multipart('', ["class" => "form-inline", "onsubmit" => "return confirm('Are you sure?');"]);?>
          <?php echo form_hidden('repo_registration[repo_registration_id]', $repo['repo_registration_id']); ?>
          <fieldset>
            <legend>
              Rerfo# <?php echo $repo['rerfo_number']; ?>
              <button id="history" class="btn btn-warning" style="margin-left:20px;font-size:14px" value="<?php echo 1; ?>">History</button>
            </legend>
            <div class="form-inline row">
              <div class="control-group span2 offset1">
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
              <div class="control-group span4 <?php echo (form_error('attachments[registration_or][]')) ? 'error' : ''; ?>" style="margin-left:8rem;">
                <label class="control-label" for="or">Registration OR:</label>
                <div class="controls">
                  <input type="file" id="or" name="attachments[registration_or][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1">
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
              <div class="control-group span4 <?php echo (form_error('attachments[pnp_clearance][]')) ? 'error' : ''; ?>" style="margin-left:8rem;">
                <label class="control-label" for="pnp-clearance">PNP Clearance:</label>
                <div class="controls">
                  <input type="file" id="pnp-clearance" name="attachments[pnp_clearance][]" accept="image/jpeg">
                </div>
              </div>
            </div>
            <br>
            <div class="row">
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1">
                <label class="control-label" for="rsf">RSF#</label>
                <div class="controls">
                  <input id="rsf" type="text" <?php echo 'value="'.$repo['rsf_num'].'"'; ?> disabled>
                </div>
              </div>
              <div class="control-group span2" style="margin-left:2rem;">
                <label class="control-label" for="date-sold">Date Sold</label>
                <div class="controls">
                  <input id="date-sold" class="datepicker" type="text" placeholder="yyyy-mm-dd" name="repo_sale[date_sold]" value="<?php echo set_value('repo_sale[date_sold]', $repo['date_sold']); ?>" disabled>
                </div>
              </div>
              <div class="control-group span4 <?php echo (form_error('attachments[insurance_or][]')) ? 'error' : ''; ?>" style="margin-left:8rem;">
                <label class="control-label" for="insurance-or">Insurance OR:</label>
                <div class="controls">
                  <input type="file" id="insurance-or" name="attachments[insurance_or][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span4 offset1">
                <label class="control-label" for="get-cust">Customer Code</label>
                <div class="controls">
                  <input id="get-cust" type="text" <?php echo 'value="'.$repo['cust_code'].'"'; ?> disabled>
                </div>
              </div>
              <div class="control-group span4 <?php echo (form_error('attachments[emission_or][]')) ? 'error' : ''; ?>" style="margin-left:7.4rem;">
                <label class="control-label" for="emission-or">Emission OR:</label>
                <div class="controls">
                  <input type="file" id="emission-or" name="attachments[emission_or][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1">
                <label class="control-label">First Name</label>
                <div class="controls">
                  <input id="first-name" type="text" <?php echo 'value="'.$repo['first_name'].'"'; ?> disabled>
                </div>
              </div>
              <div class="control-group span2" style="margin-left:2rem;">
                <label class="control-label">Last Name</label>
                <div class="controls">
                  <input id="last-name" type="text" <?php echo 'value="'.$repo['last_name'].'"'; ?> disabled>
                </div>
              </div>
              <div class="control-group span4 <?php echo (form_error('attachments[macro_etching][]')) ? 'error' : ''; ?>" style="margin-left:7.9rem;">
                <label class="control-label" for="macro-etching">Macro Etching:</label>
                <div class="controls">
                  <input type="file" id="macro-etching" name="attachments[macro_etching][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1">
                <label class="control-label" for="ar-num">AR#</label>
                <div class="controls">
                  <input id="ar-num" type="text" value="<?php echo $repo['ar_num']; ?>" disabled>
                </div>
              </div>
              <div class="control-group span2" style="margin-left:2rem;">
                <label class="control-label" for="ar-amount">Amount Given</label>
                <div class="controls">
                  <input id="ar-amount" type="text"  value="<?php echo $repo['ar_amt']; ?>" disabled>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div id="regn-status" class="control-group span2 offset1 <?php echo $repo['expire_status']; ?>">
                <label class="control-label" for="date-regn">Date Registered</label>
                <div class="controls">
                  <input id="date-regn" class="datepicker" type="text" name="repo_registration[date_registered]" value="<?php echo set_value('repo_registration[date_registered]', $repo['date_registered']); ?>" autocomplete="off">
                  <span id="status-message" class="help-inline"><?php echo $repo['expire_message']; ?></span>
                </div>
              </div>
              <div class="control-group span2<?php echo (form_error('repo_registration[registration_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="regn-amount">Registration</label>
                <div class="controls">
                  <input id="regn-amount" type="text" name="repo_registration[registration_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[registration_amt]', ''); ?>" required>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_registration[pnp_clearance_amt]')) ? 'error' : ''; ?>">
                <label class="control-label" for="pnp-amount">PNP Clearance</label>
                <div class="controls">
                  <input id="pnp-amount" type="text" name="repo_registration[pnp_clearance_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[pnp_clearance_amt]', ''); ?>" required>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('repo_registration[insurance_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="insurance-amount">Insurance</label>
                <div class="controls">
                  <input id="insurance-amount" type="text" name="repo_registration[insurance_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[insurance_amt]', ''); ?>" required>
                </div>
              </div>
            </div>
            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_registration[emission_amt]')) ? 'error' : ''; ?>">
                <label class="control-label" for="emmission-amount">Emission</label>
                <div class="controls">
                  <input id="emission-amount" type="text" name="repo_registration[emission_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[emission_amt]', ''); ?>" required>
                </div>
              </div>

              <div class="control-group span2 <?php echo (form_error('repo_registration[macro_etching_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="macro-etching-amount">Macro Etching</label>
                <div class="controls">
                  <input id="macro-etching-amount" type="text" name="repo_registration[macro_etching_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[macro_etching_amt]', ''); ?>" required>
                </div>
              </div>
            </div>

            <div class="row">
              <button id="save" class="btn btn-success offset1" type="submit" name="save" value="true">Save</button>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>

