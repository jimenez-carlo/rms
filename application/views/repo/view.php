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
            <fieldset class="span6">
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
                  <label class="control-label">RSF#</label>
                  <div class="controls">
                    <input type="text" <?php echo 'value="'.$repo['rsf_num'].'"'; ?> disabled>
                  </div>
                </div>
                <div class="control-group span4" style="margin-left:2rem;">
                  <label class="control-label" for="date-sold">Date Sold</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['date_sold']; ?>" disabled>
                  </div>
                </div>
              </div>

              <div class="form-inline row">
                <div class="control-group span4 offset1">
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

                <div class="control-group span4" style="margin-left:2rem;">
                  <label class="control-label">Registration</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['registration_amt']; ?>" disabled>
                  </div>
                </div>
              </div>
              <div class="form-inline row">
                <div class="control-group span4 offset1">
                  <label class="control-label">PNP Clearance</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['pnp_clearance_amt']; ?>" disabled>
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
                  <label class="control-label">Macro Etching</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $repo['macro_etching_amt']; ?>" disabled>
                  </div>
                </div>
              </div>
            </fieldset>
            <?php if($attachment): ?>
            <div class="span6" style="overflow-x:auto; height: 700px; border:solid 0.1rem;">
              <div class="control-group row" style="margin-left:8rem;">
                <label class="control-label">Registration OR:</label>
                <div class="controls">
                  <img class="img-rounded" src="<?php echo $registration_or; ?>" alt="Registration OR"/>
                </div>
              </div>
              <hr>
              <div class="control-group row" style="margin-left:8rem;">
                <label class="control-label">PNP Clearance:</label>
                <div class="controls">
                  <img src="<?php echo $pnp_clearance; ?>" alt="PNP Clearance"/>
                </div>
              </div>
              <hr>
              <div class="control-group row" style="margin-left:7.4rem;">
                <label class="control-label">Macro Etching:</label>
                <div class="controls">
                  <img src="<?php echo $macro_etching; ?>" alt="Macro Etching"/>
                </div>
              </div>
              <hr>
              <div class="control-group row" style="margin-left:8rem;">
                <label class="control-label">Insurance OR:</label>
                <div class="controls">
                  <img src="<?php echo $insurance_or; ?>" alt="Insurance OR"/>
                </div>
              </div>
              <hr>
              <div class="control-group row" style="margin-left:8rem;">
                <label class="control-label">Emission OR:</label>
                <div class="controls">
                  <img src="<?php echo $emission_or; ?>" alt="Emission OR"/>
                </div>
              </div>
            </div>
            <?php endif; ?>
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
                    echo '<li>Date Registered: '.$data['date_registered'].' | Date Encoded: '.$data['date_uploaded'].'</li>';
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

