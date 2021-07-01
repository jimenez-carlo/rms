<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" metho="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span4">
        <label>Batch No#</label>
        <input type="hidden" class="span4" value="update_misc" placeholder="Input here." name="action">
        <input required type="hidden" class="span4" value="<?php echo $record->mid; ?>" placeholder="Input here." name="edit_id">
        <select name="batch_no" style="width: 100%;">
          <?php foreach ($batch as $res) { ?>
            <?php echo ($record->ca_ref == $res['value']) ? "<option value='" . $record->ca_ref . "' selected hidden>" . $res['display'] . "</option>" : ""; ?>
            <option value="<?php echo $res['value']; ?>"><?php echo $res['display']; ?></option>
          <?php } ?>
        </select>
        <label>Date</label>
        <input required type="text" class="span4 datepicker" value="<?php echo $record->dt; ?>" placeholder="Input here." name="date">
        <label>OR No#</label>
        <input required type="text" class="span4" value="<?php echo $record->or_no; ?>" placeholder="Input here." name="or_no">
        <label>Expense Type</label>
        <select name="expense_type" id="" style="width: 100%;">
          <?php foreach ($expense_type as $res) { ?>
            <?php echo ($record->type == $res['type']) ? "<option value='{$record->type}' selected>{$record->type}</option>" : ""; ?>
            <option value="<?php echo $res['type']; ?>"><?php echo $res['type']; ?></option>
          <?php } ?>
        </select>
        <label>Amount</label>
        <input required type="text" class="span4" placeholder="0.00" value="<?php echo number_format($record->amount, 2); ?>" name="amount">
      </div>

      <div class="span4">
        <label>Attachment</label>
        <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="" style="width: 250px;height:250px" id="output">
        <input type="file" class="span4" placeholder="Input here." name="file" id="file" onchange="document.getElementById('output').src = window.URL.createObjectURL(this.files[0])">
        <b>Required file format: jpeg, jpg You can only upload upto 1MB </b>
      </div>
    </div>

  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success" id="btn-submit" value="Save">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>