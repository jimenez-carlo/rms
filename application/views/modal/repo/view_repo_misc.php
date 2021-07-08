<style>
.modal{
position: fixed;
width: 50% !important;
top:10% !important;
left: 25%;
margin-top: auto; /* Negative half of height. */
margin-left: auto; /* Negative half of width. */
}
</style>
<form id="FormModal-Misc" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" method="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span4">
        <label>Batch No#</label>
        <input required type="hidden" class="span4" value="<?php echo $record->mid; ?>" placeholder="Input here." name="edit_id">
        <label><b><?php echo $record->reference; ?></b></label>
        <label>Date</label>
        <label><b><?php echo $record->dt; ?></b></label>
        <label>OR No#</label>
        <label><b><?php echo $record->or_no; ?></b></label>
        <label>Expense Type</label>
          <?php foreach ($expense_type as $res) { ?>
            <?php echo ($record->type == $res['type']) ? "<label><b>{$record->type}</label></b>" : ""; ?>
          <?php } ?>
          <label>Amount</label>
        <label><b><?php echo number_format($record->amount, 2); ?></b></label>
        <label>DA Reason</label>
        <label><b><?php echo $record->da_reason; ?></b></label>
        <label>Status</label>
        <label><b><?php echo $record->misc_status; ?></b></label>
      </div>

      <div class="span4">
        <label>Attachment</label>
        <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="" style="width:auto ;height:320px" id="output">
      </div>
    </div>
  </div>
  <div class="modal-footer">
  <?php if($record->status_id != 5){ ?>
  <select name="new_status" id="" class="un-select" style="width:unset">
<?php foreach ($status as $res ) { ?>
  <option value="<?php echo $res['id']; ?>"><?php echo $res['value']; ?></option>
<?php } ?>
  </select>
    <input type="submit" class="btn btn-success" value="Save">
    <?php } ?>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>