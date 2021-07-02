<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" metho="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span4">
        <input type="hidden" class="span4" value="update_misc" name="action">
        <input required type="hidden" class="span4" name="edit_id" value="<?php echo $record->repo_registration_id;?>">
        <?php if($record->da_id == 1){ ?>
        <label>OR/CR Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->orcr_amt;?>" name="orcr_amt">
        <label>Transfer Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->transfer_amt;?>" name="trans_amt">
        <label>Insurance Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->insurance_amt;?>" name="ins_amt">
        <label>Macro Etching Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->macro_etching_amt;?>" name="macro_amt">
        <label>Renewal Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->renewal_amt;?>" name="re_amt">
        <label>HPG / PNP Clearance Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->hpg_pnp_clearance_amt;?>" name="pnp_amt">
        <label>Emission Amount</label>
        <input required type="number" step=".01" class="span4" placeholder="0.00" value="<?php echo $record->emission_amt;?>" name="em_amt">
        <?php } ?>
        
        <?php if($record->da_id == 2){ ?>
        <label>OR/CR Amount</label>
        <label><?php echo $record->orcr_amt;?> </label>
        <label>Transfer Amount</label>
        <label><?php echo $record->transfer_amt;?> </label>
        <label>Insurance Amount</label>
        <label><?php echo $record->insurance_amt;?> </label>
        <label>Macro Etching Amount</label>
        <label><?php echo $record->macro_etching_amt;?> </label>
        <label>Renewal Amount</label>
        <label><?php echo $record->renewal_amt;?> </label>
        <label>HPG / PNP Clearance Amount</label>
        <label><?php echo $record->hpg_pnp_clearance_amt;?> </label>
        <label>Emission Amount</label>
        <label><?php echo $record->emission_amt;?> </label>
        <?php } ?>
      </div>

      <div class="span6">
      <label>Attachment </label>
        <div class="tabbable tabs-right">
          <!-- Only required for left/right tabs -->
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Registration</a></li>
            <li><a href="#tab2" data-toggle="tab">Renewal</a></li>
            <li><a href="#tab3" data-toggle="tab">Transfer</a></li>
            <li><a href="#tab4" data-toggle="tab">PNP Clearance</a></li>
            <li><a href="#tab5" data-toggle="tab">Insurance</a></li>
            <li><a href="#tab6" data-toggle="tab">Emission</a></li>
            <li><a href="#tab7" data-toggle="tab">Macro Etching</a></li>
          </ul>
          <div class="tab-content">
          <div class="tab-pane active" id="tab1">
            <center>
              <img src="<?php echo !empty($record->att_reg_orcr) ? BASE_URL . $record->att_reg_orcr : BASE_URL . "img/NoImage.jpg"; ?>" alt=""  id="output1">
              </center>
             <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="reg_img" id="reg_img" onchange="document.getElementById('output1').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <div class="tab-pane" id="tab2">
            <center>
              <img src="<?php echo !empty($record->att_renew_or) ? BASE_URL . $record->att_renew_or : BASE_URL . "img/NoImage.jpg"; ?>" alt=""  id="output2">
              </center>
             <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="ren_img" id="ren_img" onchange="document.getElementById('output2').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <div class="tab-pane" id="tab3">
            <center>
              <img src="<?php echo !empty($record->att_trans_or) ? BASE_URL . $record->att_trans_or : BASE_URL . "img/NoImage.jpg"; ?>" alt=""  id="output3">
              </center>
             <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="reg_trans" id="reg_trans" onchange="document.getElementById('output3').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <div class="tab-pane" id="tab4">
            <center>
              <img src="<?php echo !empty($record->att_pnp_or) ? BASE_URL . $record->att_pnp_or : BASE_URL . "img/NoImage.jpg"; ?>" alt=""  id="output4">
              </center>
             <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="reg_pnp" id="reg_pnp" onchange="document.getElementById('output4').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <div class="tab-pane" id="tab5">
            <center>
              <img src="<?php echo !empty($record->att_ins_or) ? BASE_URL . $record->att_ins_or : BASE_URL . "img/NoImage.jpg"; ?>" alt=""  id="output5">
              </center>
             <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="reg_ins" id="reg_ins" onchange="document.getElementById('output5').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <div class="tab-pane" id="tab6">
            <center>
              <img src="<?php echo !empty($record->att_em_or) ? BASE_URL . $record->att_em_or : BASE_URL . "img/NoImage.jpg"; ?>" alt=""  id="output6">
              </center>
             <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="reg_em" id="reg_em" onchange="document.getElementById('output6').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <div class="tab-pane" id="tab7">
              <center>
              <img src="<?php echo !empty($record->att_macro_e_or) ? BASE_URL . $record->att_macro_e_or : BASE_URL . "img/NoImage.jpg";  ?>" alt="" style="width: auto;height:250px" id="output7">
              </center>
              <?php if($record->da_id == 2){ ?>
              <input type="file" class="span4" placeholder="Input here." name="reg_mac" id="reg_mac" onchange="document.getElementById('output7').src = window.URL.createObjectURL(this.files[0])">
            <?php } ?>
            </div>
            <?php if($record->da_id == 2){ ?>
            <b>Required file format: jpeg, jpg You can only upload upto 1MB </b>
            <?php } ?>
          </div>
        </div>

      </div>
    </div>

  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success" value="Resolve">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>