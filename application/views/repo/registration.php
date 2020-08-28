<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Repo Registration</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-inline span6 offset3" method="post" action=""><!-- action="./../save_registration" -->
          <fieldset>
            <legend>Rerfo# <?php echo $_SESSION['branch_code'].'-'.date('Ymd'); ?></legend>
              <div class="form-inline row">
                <div class="control-group span4 offset1">
                  <label class="control-label">Engine#</label>
                  <div class="controls">
                    <input  id="engine-no" type="text" name="engine_no" value="<?php echo $repo['engine_no']; ?>" disabled>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Chassis#</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['chassis_no']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline row">
                <div class="control-group span4 offset1">
                  <label class="control-label">MAT#</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['mat_no']; ?>" disabled>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">MVF No.</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['mvf_no']; ?>" disabled>
                  </div>
                </div>
              </div>

              <br>
              <div class="row">
                <div class="control-group offset1 <?php echo (form_error('regn_type[]')) ? 'error' : ''; ?>" style="margin-bottom:0;">
                  <label style="position: relative;left: 15px;background-color: white;z-index: 1;padding: 0 .2rem 0 .1rem">Registration Type</label>
                  <div style="border: .1rem solid #ccc;position: relative;height: 44px;bottom: 15px;border-radius: 0.2rem;padding: 0 0 0 13px;width: 446px;">
                    <label class="checkbox" style="margin-top: .8rem;">
                      <input id="renew" type="checkbox" name="regn_type[renew]" value="1" <?php echo set_checkbox('regn_type[renew]', '1', true); ?>>
                      Renew
                    </label>
                    <label class="checkbox" style="margin: .8rem 0 0 1rem;">
                      <input id="transfer" type="checkbox" name="regn_type[transfer]" value="2" <?php echo set_checkbox('regn_type[transfer]', '2'); ?>>
                      Transfer
                    </label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="control-group offset1" style="margin-bottom:0;">
                  <label style="position: relative;left: 15px;background-color: white;z-index: 1;padding: 0 .2rem 0 .1rem">Repo Sales?</label>
                  <div style="border: .1rem solid #ccc;position: relative;height: 44px;bottom: 15px;border-radius: 0.2rem;padding: 0 0 0 13px;width: 446px;">
                    <label class="radio" style="margin-top: .8rem;">
                      <input id="sold-y" type="radio" name="sold" value="yes" <?php echo set_radio('sold', 'yes'); ?>>
                      Yes
                    </label>
                    <label class="radio" style="margin: .8rem 0 0 1rem;">
                      <input id="sold-n" type="radio" name="sold" value="no" <?php echo set_radio('sold', 'no', true); ?>>
                      No
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-inline row">
                <div class="control-group span4 offset1">
                  <label class="control-label" for="date-sold">Date Sold</label>
                  <div class="controls">
                    <input id="date-sold" class="datepicker" type="text" placeholder="yyyy-mm-dd" name="date_sold" value="<?php echo set_value('date_sold', $repo['date_sold']); ?>" disabled>
                  </div>
                </div>
                <div class="control-group <?php echo $repo['expire_status']; ?>">
                  <label class="control-label" for="date-regn">Date Registered</label>
                  <div class="controls">
                    <input id="date-regn" class="datepicker" type="text" name="repo_sales[date_regn]" value="<?php echo set_value('repo_sales[date_regn]', $repo['date_regn']); ?>" autocomplete="off">
                    <span class="help-inline"><?php echo ($repo['expire_status'] === 'warning') ? 'Expire on' : '' ; echo $repo['expire_message']; ?></span>
                  </div>
                </div>
              </div>

              <div class="form-inline row">
                <div class="control-group span4 offset1 <?php echo (form_error('rsf')) ? 'error' : ''; ?>">
                  <label class="control-label" for="rsf">RSF#</label>
                  <div class="controls">
                    <input id="rsf" type="text" name="rsf" <?php echo 'value="'.set_value('rsf', $repo['rsf_num']).'"'; echo $disable; ?>>
                  </div>
                </div>
                <div class="control-group <?php echo (form_error('cust_code')) ? 'error' : ''; ?>">
                  <label for="get-cust">Customer Code</label>
                  <div class="controls">
                    <input id="get-cust" type="text" name="cust_code" <?php echo 'value="'.set_value('cust_code', $repo['cust_code']).'"'; echo $disable; ?>>
                  </div>
                </div>
              </div>

              <div class="row">
              </div>
              <div class="form-inline row">
                <div class="control-group span4 offset1 <?php echo (form_error('first_name')) ? 'error' : ''; ?>">
                  <label class="control-label">First Name</label>
                  <div class="controls">
                    <input id="first-name" type="text" name="first_name" <?php echo 'value="'.set_value('first_name', $repo['first_name']).'"'; echo $disable; ?>>
                  </div>
                </div>
                <div class="control-group <?php echo (form_error('last_name')) ? 'error' : ''; ?>">
                  <label class="control-label">Last Name</label>
                  <div class="controls">
                    <input id="last-name" type="text" name="last_name" <?php echo 'value="'.set_value('last_name', $repo['last_name']).'"'; echo $disable; ?>>
                  </div>
                </div>
              </div>

              <div class="form-inline row">
                <div class="control-group span4 offset1 <?php echo (form_error('ar_num')) ? 'error' : ''; ?>">
                  <label class="control-label" for="ar-num">AR#</label>
                  <div class="controls">
                    <input id="ar-num" type="text" name="ar_num" <?php echo 'value="'.set_value('ar_num', '').'"'; echo $disable; ?>>
                  </div>
                </div>
                <div class="control-group <?php echo (form_error('ar_amt')) ? 'error' : ''; ?>">
                  <label class="control-label" for="ar-amount">Amount Given</label>
                  <div class="controls">
                    <input id="ar-amount" type="text" name="ar_amt" placeholder="0.00" <?php echo 'value="'.set_value('ar_amt', '').'"'; echo $disable; ?>>
                  </div>
                </div>
              </div>

              <div class="form-inline row">
                <div class="control-group span4 offset1 <?php echo (form_error('repo_sales[registration_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="regn-amount">Registration</label>
                  <div class="controls">
                    <input id="regn-amount" type="text" name="repo_sales[registration_amt]" placeholder="0.00" value="<?php echo set_value('repo_sales[registration_amt]', $repo['registration_amt']); ?>">
                  </div>
                </div>
                <div class="control-group <?php echo (form_error('repo_sales[insurance_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="insurance-amount">Insurance</label>
                  <div class="controls">
                    <input id="insurance-amount" type="text" name="repo_sales[insurance_amt]" placeholder="0.00" value="<?php echo set_value('repo_sales[insurance_amt]', $repo['insurance_amt']); ?>">
                  </div>
                </div>
              </div>
              <div class="form-inline row">
                <div class="control-group span4 offset1 <?php echo (form_error('repo_sales[emission_amt]')) ? 'error' : ''; ?>">
                  <label class="control-label" for="emmission-amount">Emission</label>
                  <div class="controls">
                    <input id="emission-amount" type="text" name="repo_sales[emission_amt]" placeholder="0.00" value="<?php echo set_value('repo_sales[emission_amt]', $repo['emission_amt']); ?>">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="tip-amount">Tip</label>
                  <div class="controls">
                    <input id="tip-amount" type="text" value="99.00" disabled>
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
<!--
  <div class="row-fluid">
    <pre id="result"><?php print_r($repo); ?></pre>
  </div>
-->
</div>

