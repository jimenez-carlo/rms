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
              CA Reference# <?php echo $repo['reference']; ?>
            </legend>
            <div class="span6">
              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label">Engine#</label>
                  <div class="controls">
                    <input  id="engine-no" type="text" name="engine_no" value="<?php echo $repo['engine_no']; ?>" disabled>
                  </div>
                </div>
                <div class="control-group span6">
                  <label class="control-label">Chassis#</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['chassis_no']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label">MAT#</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['mat_no']; ?>" disabled>
                  </div>
                </div>
                <div class="control-group span6">
                  <label class="control-label">MVF No.</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['mvf_no']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label" for="rsf">RSF#</label>
                  <div class="controls">
                    <input type="text" <?php echo 'value="'.$repo['rsf_num'].'"'; ?> disabled>
                  </div>
                </div>
                <div class="control-group span6">
                  <label class="control-label" for="date-sold">Date Sold</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['date_sold']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label">Customer Code</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['cust_code']; ?>" disabled>
                  </div>
                </div>
                <div class="control-group span6">
                  <label class="control-label">Customer Name</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['customer_name']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label">Date of Birth</label>
                  <div class="controls">
                    <input type="text" <?php echo 'value="'.$repo['date_of_birth'].'"'; ?> disabled>
                  </div>
                </div>
                <div class="control-group span6">
                  <label class="control-label">Mobile No.</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['phone_number']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label">Email Address</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['email']; ?>" disabled>
                  </div>
                </div>
                <div class="control-group span6">
                  <label class="control-label" for="ar-num">AR#</label>
                  <div class="controls">
                    <input id="ar-num" type="text" value="<?php echo $repo['ar_num']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6">
                  <label class="control-label" for="ar-amount">Amount Given</label>
                  <div class="controls">
                    <input id="ar-amount" type="text"  value="<?php echo $repo['ar_amt']; ?>" disabled>
                  </div>
                </div>
                <div id="regn-status" class="control-group span6 <?php echo $repo['expire_status']; ?>">
                  <label class="control-label" for="date-regn">Date Registered</label>
                  <div class="controls">
                    <input id="date-regn" class="datepicker" type="text" name="repo_registration[date_registered]" value="<?php echo set_value('repo_registration[date_registered]', $repo['date_registered']); ?>" autocomplete="off">
                    <small><span id="status-message" class="help-inline"><?php echo $repo['expire_message']; ?></span></small>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6 <?php echo (form_error('repo_registration[orcr_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="orcr-amount">OR/CR Amt.</label>
                  <div class="controls">
                    <input id="orcr-amount" type="number" name="repo_registration[orcr_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[orcr_amt]', ''); ?>" required>
                  </div>
                </div>
                <div class="control-group span6 <?php echo (form_error('repo_registration[renewal_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="renewal-amount">Renewal Amt.</label>
                  <div class="controls">
                    <input id="renewal-amount" type="number" name="repo_registration[renewal_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[renewal_amt]', ''); ?>" required>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6 <?php echo (form_error('repo_registration[transfer_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="transfer-amount">Transfer Amt.</label>
                  <div class="controls">
                    <input id="transfer-amount" type="number" name="repo_registration[transfer_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[transfer_amt]', ''); ?>" required>
                  </div>
                </div>
                <div class="control-group span6 <?php echo (form_error('repo_registration[hpg_pnp_clearance_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="pnp-amount">HPG / PNP Clearance Amt.</label>
                  <div class="controls">
                    <input id="pnp-amount" type="number" name="repo_registration[hpg_pnp_clearance_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[hpg_pnp_clearance_amt]', ''); ?>" required>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6 <?php echo (form_error('repo_registration[insurance_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="insurance-amount">Insurance Amt.</label>
                  <div class="controls">
                    <input id="insurance-amount" type="number" name="repo_registration[insurance_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[insurance_amt]', ''); ?>" required>
                  </div>
                </div>
                <div class="control-group span6 <?php echo (form_error('repo_registration[emission_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="emission-amount">Emission Amt.</label>
                  <div class="controls">
                    <input id="emission-amount" type="number" name="repo_registration[emission_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[emission_amt]', ''); ?>" required>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6 <?php echo (form_error('repo_registration[macro_etching_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="macro-etching-amount">Macro Etching Amt.</label>
                  <div class="controls">
                    <input id="macro-etching-amount" type="number" name="repo_registration[macro_etching_amt]" min="0" step="0.01" placeholder="0.00" value="<?php echo set_value('repo_registration[macro_etching_amt]', ''); ?>" required>
                  </div>
                </div>
                <div class="control-group span6 <?php echo (form_error('repo_registration[renewal_tip]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="renewal-tip">Renewal Tip<span class="help-inline">(Unreceipted)</span></label>
                  <div class="controls">
                    <?php echo form_input([
                      "id"=>"renewal-tip", "type"=>"number", "name"=>"repo_registration[renewal_tip]",
                      "min"=>"0", "step"=>"0.01", "max"=>$unreceipted_renewal_tip, "placeholder"=>"0.00",
                      "value"=>set_value('repo_registration[renewal_tip]', ''),
                      "required"=>true,
                    ]); ?>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6 <?php echo (form_error('repo_registration[transfer_tip]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="transfer-tip">Transfer Tip<span class="help-inline">(Unreceipted)</span></label>
                  <div class="controls">
                    <?php echo form_input([
                      "id"=>"transfer-tip", "type"=>"number", "name"=>"repo_registration[transfer_tip]",
                      "min"=>"0", "step"=>"0.01", "max"=>$unreceipted_transfer_tip, "placeholder"=>"0.00",
                      "value"=>set_value('repo_registration[transfer_tip]', ''),
                      "required"=>true,
                    ]); ?>
                  </div>
                </div>
                <div class="control-group span6 <?php echo (form_error('repo_registration[hpg_pnp_clearance_tip]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="hpg-pnp-tip">HPG / PNP Clearance Tip<span class="help-inline">(Unreceipted)</span></label>
                  <div class="controls">
                    <?php echo form_input([
                      "id"=>"hpg-pnp-tip", "type"=>"number", "name"=>"repo_registration[hpg_pnp_clearance_tip]",
                      "min"=>"0", "step"=>"0.01", "max"=>$unreceipted_hpg_pnp_clearance_tip, "placeholder"=>"0.00",
                      "value"=>set_value('repo_registration[hpg_pnp_clearance_tip]', ''),
                      "required"=>true,
                    ]); ?>
                  </div>
                </div>
              </div>

              <div class="form-inline">
                <div class="control-group span6 <?php echo (form_error('repo_registration[macro_etching_tip]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="macro-etching-tip">Macro Etching Tip<span class="help-inline">(Unreceipted)</span></label>
                  <div class="controls">
                    <?php echo form_input([
                      "id"=>"macro-etching-tip", "type"=>"number", "name"=>"repo_registration[macro_etching_tip]",
                      "min"=>"0", "step"=>"0.01", "max"=>$unreceipted_macro_etching_tip, "placeholder"=>"0.00",
                      "value"=>set_value('repo_registration[macro_etching_tip]', ''),
                      "required"=>true,
                    ]); ?>
                  </div>
                </div>
                <div class="control-group span6 <?php echo (form_error('repo_registration[plate_tip]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="plate-tip">Plate Tip<span class="help-inline">(Unreceipted)</span></label>
                  <div class="controls">
                    <?php echo form_input([
                      "id"=>"plate-tip", "type"=>"number", "name"=>"repo_registration[plate_tip]",
                      "min"=>"0", "step"=>"0.01", "max"=>$unreceipted_plate_tip, "placeholder"=>"0.00",
                      "value"=>set_value('repo_registration[plate_tip]', ''),
                      "required"=>true
                    ]); ?>
                  </div>
                </div>
              </div>
              <div class="form-inline">
                <div class="control-group span6">
                  <div class="controls">
                    <button id="save" class="btn btn-success" type="submit" name="save" value="true">Save</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="span6" style="height: 887px; overflow-y:auto;">
              <div class="control-group <?php echo (form_error('attachments[registration_orcr_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="orcr">Registration OR / CR</label>
                <div class="controls">
                  <input type="file" id="orcr" name="attachments[registration_orcr_img][]" data-img_id="#orcr-img" accept="image/jpeg" required>
                </div>
                <img id="orcr-img" class="img-polariod">
              </div>
              <div class="control-group <?php echo (form_error('attachments[renewal_or_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="renewal-or">Renewal OR</label>
                <div class="controls">
                  <input type="file" id="renewal-or" name="attachments[renewal_or_img][]" data-img_id="#renewal-img" accept="image/jpeg" required>
                </div>
                <img id="renewal-img" class="img-polariod">
              </div>
              <div class="control-group <?php echo (form_error('attachments[transfer_or_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="transfer-or">Transfer OR</label>
                <div class="controls">
                  <input type="file" id="transfer-or" name="attachments[transfer_or_img][]" data-img_id="#transfer-img" accept="image/jpeg" required>
                </div>
                <img id="transfer-img" class="img-polariod">
              </div>
              <div class="control-group <?php echo (form_error('attachments[hpg_pnp_clearance_or_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="pnp-clearance">PNP Clearance OR</label>
                <div class="controls">
                  <input type="file" id="pnp-clearance" name="attachments[hpg_pnp_clearance_or_img][]" data-img_id="#hpg-pnp-img" accept="image/jpeg" required>
                </div>
                <img id="hpg-pnp-img" class="img-polariod">
              </div>

              <div class="control-group <?php echo (form_error('attachments[insurance_or_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="insurance-or">Insurance OR</label>
                <div class="controls">
                  <input type="file" id="insurance-or" name="attachments[insurance_or_img][]" data-img_id="#insurance-img" accept="image/jpeg" required>
                </div>
                <img id="insurance-img" class="img-polariod">
              </div>

              <div class="control-group <?php echo (form_error('attachments[emission_or_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="emission-or">Emission OR</label>
                <div class="controls">
                  <input type="file" id="emission-or" name="attachments[emission_or_img][]" data-img_id="#emission-img" accept="image/jpeg" required>
                </div>
                <img id="emission-img" class="img-polariod">
              </div>

              <div class="control-group <?php echo (form_error('attachments[macro_etching_or_img][]')) ? 'error' : ''; ?>">
                <label class="control-label" for="macro-etching">Macro Etching OR</label>
                <div class="controls">
                  <input type="file" id="macro-etching" name="attachments[macro_etching_or_img][]" data-img_id="#macro-img" accept="image/jpeg" required>
                </div>
                <img id="macro-img" class="img-polariod">
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>

