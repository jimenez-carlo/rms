<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Repo Registration</div>
      </div>
      <div class="block-content collapse in">
        <fieldset class="span6">
          <!-- <legend></legend> -->
          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">Engine#</label>
              <div class="controls">
                <input  id="engine-no" type="text" name="engine_no" value="<?php echo $repo['engine_no']; ?>" disabled>
              </div>
            </div>
            <div class="control-group span4" style="margin-left:2rem;">
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
            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label">MVF No.</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['mvf_no']; ?>" disabled>
              </div>
            </div>
          </div>
          <br>
          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label" for="date-sold">Date Sold</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['date_sold']; ?>" disabled>
              </div>
            </div>
            <div class="control-group span4" style="margin-left:2rem;">
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

          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">RSF#</label>
              <div class="controls">
                <input type="text" <?php echo 'value="'.$repo['rsf_num'].'"'; ?> disabled>
              </div>
            </div>
            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label" for="get-cust">Customer Code</label>
              <div class="controls">
                <input type="text" <?php echo 'value="'.$repo['cust_code'].'"'; ?> disabled>
              </div>
            </div>
          </div>

          <div class="row">
          </div>
          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">First Name</label>
              <div class="controls">
                <input type="text" <?php echo 'value="'.$repo['first_name'].'"'; ?> disabled>
              </div>
            </div>
            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label">Last Name</label>
              <div class="controls">
                <input type="text" <?php echo 'value="'.$repo['last_name'].'"'; ?> disabled>
              </div>
            </div>
          </div>

          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">AR#</label>
              <div class="controls">
                <input type="text"  <?php echo 'value="'.$repo['ar_num'].'"'; ?> disabled>
              </div>
            </div>
            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label">Amount Given</label>
              <div class="controls">
                <input type="text" <?php echo 'value="'.$repo['ar_amt'].'"'; ?> disabled>
              </div>
            </div>
          </div>

          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">Registration</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['registration_amt']; ?>" disabled>
              </div>
            </div>

            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label">PNP Clearance</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['pnp_clearance_amt']; ?>" disabled>
              </div>
            </div>
          </div>
          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">Macro Etching</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['macro_etching_amt']; ?>" disabled>
              </div>
            </div>

            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label">Insurance</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['insurance_amt']; ?>" disabled>
              </div>
            </div>
          </div>
          <div class="form-inline row">
            <div class="control-group span4 offset1">
              <label class="control-label">Emission</label>
              <div class="controls">
                <input type="text" value="<?php echo $repo['emission_amt']; ?>" disabled>
              </div>
            </div>
            <div class="control-group span4" style="margin-left:2rem;">
              <label class="control-label" for="tip-amount">Tip</label>
              <div class="controls">
                <input id="tip-amount" type="text" value="<?php echo $repo['rr_tip_amt']; ?>" disabled>
              </div>
            </div>
          </div>
        </fieldset>
        <div class="span6" style="overflow-x:auto; height: 700px; border:solid 0.1rem;">
           <div class="control-group row" style="margin-left:8rem;">
             <label class="control-label">Registration OR:</label>
             <div class="controls">
               <img class="img-rounded" src="<?php echo $registration_or; ?>" alt="Registration OR"/>
             </div>
           </div>

           <div class="control-group row" style="margin-left:8rem;">
             <label class="control-label">PNP Clearance:</label>
             <div class="controls">
               <img src="<?php echo $pnp_clearance; ?>" alt="PNP Clearance"/>
             </div>
           </div>

           <div class="control-group row" style="margin-left:7.4rem;">
             <label class="control-label">Macro Etching:</label>
             <div class="controls">
               <img src="<?php echo $macro_etching; ?>" alt="Macro Etching"/>
             </div>
           </div>

           <div class="control-group row" style="margin-left:8rem;">
             <label class="control-label">Insurance OR:</label>
             <div class="controls">
               <img src="<?php echo $insurance_or; ?>" alt="Insurance OR"/>
             </div>
           </div>
           <div class="control-group row" style="margin-left:8rem;">
             <label class="control-label">Emission OR:</label>
             <div class="controls">
               <img src="<?php echo $emission_or; ?>" alt="Emission OR"/>
             </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>

