<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo View</div>
      </div>
      <ul class="nav nav-tabs">
        <li class="active"><a href="#view" data-toggle="tab">View</a></li>
        <li ><a href="#history" data-toggle="tab">History</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="view">
          <div class="block-content collapse in">
            <fieldset>
              <legend>
                CA Reference# <?php echo $repo['reference']; ?>
              </legend>
                          
                <div class="span6">
                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">Repo Type</label>
                    <div class="controls">
                      <input  type="text" value="<?php echo $repo['repo_reg_type']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">Repo Date Registered</label>
                    <div class="controls">
                      <input  type="text" value="<?php echo $repo['repo_date_registered']; ?>" disabled>
                    </div>
                  </div>
                </div>

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
                  <div class="control-group span6">
                    <label class="control-label">Date Registered</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['date_registered']; ?>" disabled>
                      <span class="help-inline">
                        <span class="text-<?php echo $repo['expire_status']; ?>">
                          <?php if($repo['expire_status'] === 'error'): ?>
                            <strong><?php echo $repo['expire_message']; ?></strong>
                          <?php else: ?>
                            <strong>Expire in:</strong><?php echo $repo['expire_message']; ?>
                          <?php endif; ?>
                        </span>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">OR/CR Amt.</label>
                    <div class="controls">
                      <input type="number"value="<?php echo $repo['orcr_amt']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">Renewal Amt.</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['renewal_amt']; ?>" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">Transfer Amt.</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['transfer_amt']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">HPG / PNP Clearance Amt.</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['hpg_pnp_clearance_amt']; ?>" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">Insurance Amt.</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['insurance_amt']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">Emission Amt.</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['emission_amt']; ?>" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">Macro Etching Amt.</label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['macro_etching_amt']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">Renewal Tip<span class="help-inline">(Unreceipted)</span></label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['renewal_tip']; ?>" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">Transfer Tip<span class="help-inline">(Unreceipted)</span></label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['transfer_tip']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">HPG / PNP Clearance Tip<span class="help-inline">(Unreceipted)</span></label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['hpg_pnp_clearance_tip']; ?>" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline">
                  <div class="control-group span6">
                    <label class="control-label">Macro Etching Tip<span class="help-inline">(Unreceipted)</span></label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['macro_etching_tip']; ?>" disabled>
                    </div>
                  </div>
                  <div class="control-group span6">
                    <label class="control-label">Plate Tip<span class="help-inline">(Unreceipted)</span></label>
                    <div class="controls">
                      <input type="text" value="<?php echo $repo['plate_tip']; ?>" disabled>
                    </div>
                  </div>
                </div>
              </div>
              <?php if($attachment): ?>
              <div class="span6" style="height: 887px; overflow-y:auto;">
                <ul id="imgTab" class="nav nav-tabs">
                  <?php if(isset($registration_orcr_img)): ?>
                  <li class="active"><a href="#orcr-img">OR/CR</a></li>
                  <?php endif; ?>
                  <?php if(isset($renewal_or_img)): ?>
                  <li><a href="#renewal-img">Renewal</a></li>
                  <?php endif; ?>
                  <?php if(isset($transfer_or_img)): ?>
                  <li><a href="#transfer-img">Transfer</a></li>
                  <?php endif; ?>
                  <?php if(isset($hpg_pnp_clearance_or_img)): ?>
                  <li><a href="#clearance-img">HPG/PNP Clearance</a></li>
                  <?php endif; ?>
                  <?php if(isset($insurance_or_img)): ?>
                  <li><a href="#insurance-img">Insurance</a></li>
                  <?php endif; ?>
                  <?php if(isset($emission_or_img)): ?>
                  <li><a href="#emission-img">Emission</a></li>
                  <?php endif; ?>
                  <?php if(isset($macro_etching_or_img)): ?>
                  <li><a href="#macro-etching-img">Macro Etching</a></li>
                  <?php endif; ?>
                </ul>

                <div class="tab-content">
                  <?php if(isset($registration_orcr_img)): ?>
                  <div class="tab-pane active" id="orcr-img">
                    <div class="control-group">
                      <label class="control-label">Registration OR/CR</label>
                      <div class="controls">
                        <img class="img-polariod" src="<?php echo $registration_orcr_img; ?>" alt="OR/CR"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($renewal_or_img)): ?>
                  <div class="tab-pane" id="renewal-img">
                    <div class="control-group">
                      <label class="control-label">Renewal OR</label>
                      <div class="controls">
                        <img class="img-rounded" src="<?php echo $renewal_or_img; ?>" alt="Renewal OR"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($transfer_or_img)): ?>
                  <div class="tab-pane" id="transfer-img">
                    <div class="control-group">
                      <label class="control-label">Transfer OR</label>
                      <div class="controls">
                        <img class="img-rounded" src="<?php echo $transfer_or_img; ?>" alt="Transfer OR"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($hpg_pnp_clearance_or_img)): ?>
                  <div class="tab-pane" id="clearance-img">
                    <div class="control-group">
                      <label class="control-label">HPG/PNP Clearance OR</label>
                      <div class="controls">
                        <img src="<?php echo $hpg_pnp_clearance_or_img; ?>" alt="HPG/PNP Clearance"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($insurance_or_img)): ?>
                  <div class="tab-pane" id="insurance-img">
                    <div class="control-group">
                      <label class="control-label">Insurance OR</label>
                      <div class="controls">
                        <img src="<?php echo $insurance_or_img; ?>" alt="Insurance OR"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($emission_or_img)): ?>
                  <div class="tab-pane" id="emission-img">
                    <div class="control-group">
                      <label class="control-label">Emission OR</label>
                      <div class="controls">
                        <img src="<?php echo $emission_or_img; ?>" alt="Emission OR"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                  <?php if(isset($macro_etching_or_img)): ?>
                  <div class="tab-pane" id="macro-etching-img">
                    <div class="control-group">
                      <label class="control-label">Macro Etching OR</label>
                      <div class="controls">
                        <img src="<?php echo $macro_etching_or_img; ?>" alt="Macro Etching"/>
                      </div>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>

              </div>
              <?php endif; ?>
            </fieldset>
          </div>
        </div>

        <div class="tab-pane" id="history">
          <div class="block-content collapse in">
            <?php foreach($histories AS $history): ?>
              <?php
                $data = json_decode($history['data'], true);
                switch ($history['action']) {
                  case 'BNEW':
                    echo '<ul style="list-style: none;">';
                    echo '<li>Title: Brand New</li>';
                    echo '<li>Branch: '.$data['bcode'].' '.$data['bname'].' | Region: '.$data['region'].'</li>';
                    echo '<li>Date Sold: '.$data['date_sold'].' | '.'Date Registered: '.$data['date_registered'].'</li>';
                    echo '<li>Customer Code: '.$data['cust_code'].' | Customer Name: '.$data['first_name'].' '.$data['last_name'].'</li>';
                    echo '<li>Engine#: '.$data['engine_no'].'</li>';
                    echo '</ul>';
                    break;
                  case 'REPO_IN':
                    echo '<ul style="list-style: none;">';
                    echo '<li>Title: Repo In</li>';
                    echo '<li>Repo In By: '.$data['bcode'].' '.$data['bname'].' | User: '.$history['user'].'</li>';
                    echo '<li>Date Repo In: '.$history['date_created'].'</li>';
                    echo '</ul>';
                    break;
                  case 'REPO_SALES':
                    echo '<ul style="list-style: none;">';
                    echo '<li>Title: Repo Sales</li>';
                    echo '<li>Date Sold: '.$data['date_sold'].' | Date Encoded: '.$data['date_created'].'</li>';
                    echo '<li>Customer Code: '.$data['cust_code'].' | Customer Name: '.$data['first_name'].' '.$data['last_name'].'</li>';
                    echo '<li>RSF#: '.$data['rsf_num'].'</li>';
                    echo '</ul>';
                    break;
                  case 'REGISTERED':
                    echo '<ul style="list-style: none;">';
                    echo '<li>Title: Repo Registration</li>';
                    echo '<li>Date Registered: '.$data['date_registered'].' | Date Encoded: '.$data['date_created'].'</li>';
                    echo '</ul>';
                    break;
                }
              ?>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script>
$('#imgTab a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})
</script>
