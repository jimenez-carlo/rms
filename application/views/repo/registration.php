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
          <fieldset>
            <legend>
              Rerfo# <?php echo $_SESSION['branch_code'].'-'.date('Ymd'); ?>
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
              <div class="control-group span2 offset1 <?php echo (form_error('repo_sale[date_sold]')) ? 'error' : ''; ?>">
                <label class="control-label" for="date-sold">Date Sold</label>
                <div class="controls">
                  <input id="date-sold" class="datepicker" type="text" placeholder="yyyy-mm-dd" name="repo_sale[date_sold]" value="<?php echo set_value('repo_sale[date_sold]', $repo['date_sold']); ?>" disabled>
                </div>
              </div>
              <div id="regn-status" class="control-group span2 <?php echo $repo['expire_status']; ?>" style="margin-left:2rem;">
                <label class="control-label" for="date-regn">Date Registered</label>
                <div class="controls">
                  <input id="date-regn" class="datepicker" type="text" name="repo_registration[date_registered]" value="<?php echo set_value('repo_registration[date_registered]', $repo['date_registered']); ?>" autocomplete="off">
                  <span id="status-message" class="help-inline"><?php echo $repo['expire_message']; ?></span>
                </div>
              </div>
              <div class="control-group span4 <?php echo (form_error('attachments[macro_etching][]')) ? 'error' : ''; ?>" style="margin-left:7.4rem;">
                <label class="control-label" for="macro-etching">Macro Etching:</label>
                <div class="controls">
                  <input type="file" id="macro-etching" name="attachments[macro_etching][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_sale[rsf_num]')) ? 'error' : ''; ?>">
                <label class="control-label" for="rsf">RSF#</label>
                <div class="controls">
                  <input id="rsf" type="text" name="repo_sale[rsf_num]" <?php echo 'value="'.$repo['rsf_num'].'"'; ?> disabled>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('customer[cust_code]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="get-cust">Customer Code</label>
                <div class="controls">
                  <input id="get-cust" type="text" name="customer[cust_code]" <?php echo 'value="'.set_value('customer[cust_code]', '').'"'; //echo $disable; ?>>
                </div>
              </div>
              <div class="control-group span4 <?php echo (form_error('attachments[insurance_or][]')) ? 'error' : ''; ?>" style="margin-left:8rem;">
                <label class="control-label" for="insurance-or">Insurance OR:</label>
                <div class="controls">
                  <input type="file" id="insurance-or" name="attachments[insurance_or][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="row">
            </div>
            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('customer[first_name]')) ? 'error' : ''; ?>">
                <label class="control-label">First Name</label>
                <div class="controls">
                  <input id="first-name" type="text" name="customer[first_name]" <?php echo 'value="'.set_value('customer[first_name]', '').'"'; //echo $disable; ?>>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('customer[last_name]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label">Last Name</label>
                <div class="controls">
                  <input id="last-name" type="text" name="customer[last_name]" <?php echo 'value="'.set_value('customer[last_name]', '').'"'; //echo $disable; ?>>
                </div>
              </div>
              <div class="control-group span4 <?php echo (form_error('attachments[emission_or][]')) ? 'error' : ''; ?>" style="margin-left:8rem;">
                <label class="control-label" for="emission-or">Emission OR:</label>
                <div class="controls">
                  <input type="file" id="emission-or" name="attachments[emission_or][]" accept="image/jpeg">
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_sale[ar_num]')) ? 'error' : ''; ?>">
                <label class="control-label" for="ar-num">AR#</label>
                <div class="controls">
                  <input id="ar-num" type="text" name="repo_sale[ar_num]" <?php echo 'value="'.set_value('repo_sale[ar_num]', '').'"'; //echo $disable; ?>>
                </div>
              </div>
              <div class="control-group span2 <?php echo (form_error('repo_sale[ar_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="ar-amount">Amount Given</label>
                <div class="controls">
                  <input id="ar-amount" type="text" name="repo_sale[ar_amt]" placeholder="0.00" <?php echo 'value="'.set_value('repo_sale[ar_amt]', '').'"'; //echo $disable; ?>>
                </div>
              </div>
            </div>

            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_registration[registration_amt]')) ? 'error' : ''; ?>">
                <label class="control-label" for="regn-amount">Registration</label>
                <div class="controls">
                  <input id="regn-amount" type="text" name="repo_registration[registration_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[registration_amt]', ''); ?>">
                </div>
              </div>

              <div class="control-group span2 <?php echo (form_error('repo_registration[pnp_clearance_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="pnp-clearance-amount">PNP Clearance</label>
                <div class="controls">
                  <input id="pnp-clearance-amount" type="text" name="repo_registration[pnp_clearance_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[pnp_clearance_amt]', ''); ?>">
                </div>
              </div>
            </div>
            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_registration[macro_etching_amt]')) ? 'error' : ''; ?>">
                <label class="control-label" for="macro-etching-amount">Macro Etching</label>
                <div class="controls">
                  <input id="macro-etching-amount" type="text" name="repo_registration[macro_etching_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[macro_etching_amt]', ''); ?>">
                </div>
              </div>

              <div class="control-group span2 <?php echo (form_error('repo_registration[insurance_amt]')) ? 'error' : ''; ?>" style="margin-left:2rem;">
                <label class="control-label" for="insurance-amount">Insurance</label>
                <div class="controls">
                  <input id="insurance-amount" type="text" name="repo_registration[insurance_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[insurance_amt]', ''); ?>">
                </div>
              </div>
            </div>
            <div class="form-inline row">
              <div class="control-group span2 offset1 <?php echo (form_error('repo_registration[emission_amt]')) ? 'error' : ''; ?>">
                <label class="control-label" for="emmission-amount">Emission</label>
                <div class="controls">
                  <input id="emission-amount" type="text" name="repo_registration[emission_amt]" placeholder="0.00" value="<?php echo set_value('repo_registration[emission_amt]', ''); ?>">
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

