<style>
  .modal {
    position: fixed;
    width: 45%;
    top: 10% !important;
    left: 30%;
    margin-top: auto;
    /* Negative half of height. */
    margin-left: auto;
    /* Negative half of width. */
  }
</style>
<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" method="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="span6">
      <label>Branch</label>
      <input type="hidden" value="<?php echo $_POST['branch']; ?>" name="branch">
      <input required type="text" step="0.01" class="span6" placeholder="0.00" autocomplete="off" value="<?php echo $record->display;?>" disabled>
    </div>
    <div class="span3">
      <label>Renewal</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->sop_renewal; ?>" name="renewal">
    </div>

    <div class="span3">
      <label>Unreceipted Renewal</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->unreceipted_renewal_tip; ?>" name="un_renewal">
    </div>

    <div class="span3">
      <label>Transfer</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->sop_transfer; ?>" name="transfer">
      <label>Hpg Pnp Clearance</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->sop_hpg_pnp_clearance; ?>" name="hpg_pnp_clearance">
    </div>

    <div class="span3">
      <label> Unreceipted Transfer</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->unreceipted_transfer_tip; ?>" name="un_transfer">
      <label>Unreceipted Hpg Pnp Clearance </label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->unreceipted_hpg_pnp_clearance_tip; ?>" name="un_hpg_pnp_clearance">
    </div>

    <div class="span3">
      <label>Unreceipted Macro Etching</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->unreceipted_macro_etching_tip; ?>" name="un_macro">
      <label>Insurance</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->insurance; ?>" name="insurance">
    </div>
    <div class="span3">
      <label>Unreceipted Plate
      </label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->unreceipted_plate_tip; ?>" name="un_plate">
      <label>Emission</label>
      <input required type="number" step="0.01" class="span3" placeholder="0.00" autocomplete="off" value="<?php echo $record->emission; ?>" name="emission">
    </div>
  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success" value="Save">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>