<style>
	.modal {
		position: fixed;
		width: 60%;
		top: 10% !important;
		left: 20%;
		margin-top: auto;
		/* Negative half of height. */
		margin-left: auto;
		/* Negative half of width. */
	}

	.tab-pane {
		border: 1px solid;
		border-color: #ddd #ddd #ddd #ddd;
		padding: 20px;
	}

	.tabs-right>.nav-tabs {
		float: right;
		margin-left: 0px;
	}

	img {
		width: auto;
		height: 250px;
	}
</style>
<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" metho="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span4">
          <input required type="hidden" class="span4" name="edit_id" value="<?php echo $record->repo_registration_id;?>">
          <label>OR/CR Amount</label>
          <label><?php echo $record->orcr_amt; ?> </label>
          <label>Transfer Amount</label>
          <label><?php echo $record->transfer_amt; ?> </label>
          <label>Insurance Amount</label>
          <label><?php echo $record->insurance_amt; ?> </label>
          <label>Macro Etching Amount</label>
          <label><?php echo $record->macro_etching_amt; ?> </label>
          <label>Renewal Amount</label>
          <label><?php echo $record->renewal_amt; ?> </label>
          <label>HPG / PNP Clearance Amount</label>
          <label><?php echo $record->hpg_pnp_clearance_amt; ?> </label>
          <label>Emission Amount</label>
          <label><?php echo $record->emission_amt; ?> </label>
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
                <img src="<?php echo !empty($record->att_reg_orcr) ? BASE_URL . $record->att_reg_orcr : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output1">
              </center>

            </div>
            <div class="tab-pane" id="tab2">
              <center>
                <img src="<?php echo !empty($record->att_renew_or) ? BASE_URL . $record->att_renew_or : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output2">
              </center>

            </div>
            <div class="tab-pane" id="tab3">
              <center>
                <img src="<?php echo !empty($record->att_trans_or) ? BASE_URL . $record->att_trans_or : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output3">
              </center>

            </div>
            <div class="tab-pane" id="tab4">
              <center>
                <img src="<?php echo !empty($record->att_pnp_or) ? BASE_URL . $record->att_pnp_or : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output4">
              </center>

            </div>
            <div class="tab-pane" id="tab5">
              <center>
                <img src="<?php echo !empty($record->att_ins_or) ? BASE_URL . $record->att_ins_or : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output5">
              </center>

            </div>
            <div class="tab-pane" id="tab6">
              <center>
                <img src="<?php echo !empty($record->att_em_or) ? BASE_URL . $record->att_em_or : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output6">
              </center>
            </div>
            <div class="tab-pane" id="tab7">
              <center>
                <img src="<?php echo !empty($record->att_macro_e_or) ? BASE_URL . $record->att_macro_e_or : BASE_URL . "img/NoImage.jpg";  ?>" alt="" style="width: auto;height:250px" id="output7">
              </center>
            </div>

          </div>
        </div>

      </div>
    </div>

  </div>
  <div class="modal-footer">
  <select name="new_status" id="" class="un-select" style="width:unset">
<?php foreach ($status as $res ) { ?>
  <option value="<?php echo $res['id']; ?>"><?php echo $res['value']; ?></option>
<?php } ?>
  </select>
    <input type="submit" class="btn btn-success" value="Save">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>