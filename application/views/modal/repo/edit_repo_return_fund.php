<style>
  .one-hundred {
    width: 97%;
  }

  img {
    width: 100% !important;
    height: 100% !important;
    object-fit: initial !important;
    max-height: 215px;
    max-width: 215px;
  }

  .modal {
    position: fixed;
    width: 26%;
    top: 5% !important;
    left: 35%;
    margin-top: auto;
    /* Negative half of height. */
    margin-left: auto;
    /* Negative half of width. */
  }

  .modal-body {
    overflow-y: unset !important;
  }
</style>
<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" method="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span5" style="text-align:center">
        <input type="hidden" class="span5" value="<?php echo $record->id; ?>" name="return_fund_id">
        <?php
        /* CCN */
        if (in_array($this->session->position, array(72, 73, 83))) {

          if ($record->status_id == 3) { ?>
            <label>Attachment</label>
            <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="">
            <label>Batch#</label>
            <input required type="text" class="one-hundred" value="<?php echo $batch->reference; ?>" step="0.01" disabled>
            <label>Amount</label>
            <input type="number" class="one-hundred" placeholder="0.00" value="<?php echo $record->amount ?>" name="amount" step="0.01">
          <?php } ?>



          <?php if ($record->status_id == 4) { ?>
            <label>Attachment</label>
            <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="">
            <label>Batch#</label>
            <input required type="text" class="one-hundred" value="<?php echo $batch->reference; ?>" step="0.01" disabled>
            <label>Amount</label>
            <input type="text" class="one-hundred" value="<?php echo number_format($batch->amount, 2); ?>" step="0.01" disabled>
          <?php } ?>


          <?php if ($record->status_id == 5) { ?>
            <label>Attachment</label>
            <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="" id="output">
            <input required type="file" class="" placeholder="Input here." name="attachment" id="attachment" onchange="document.getElementById('output').src = window.URL.createObjectURL(this.files[0])">
            <p><b>Required file format: jpeg, jpg You can only upload upto 1MB </b></p>
            <label>Batch#</label>
            <input type="text" class="one-hundred" value="<?php echo $batch->reference; ?>" step="0.01" disabled>
            <label>Amount</label>
            <input type="text" class="one-hundred" value="<?php echo number_format($batch->amount, 2); ?>" step="0.01" disabled>
          <?php } ?>



        <?php }
        /* ACCTG */
        ?>
        <?php if (!in_array($this->session->position, array(72, 73, 83))) {  ?>

          <label>Attachment</label>
          <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="">
          <label>Batch#</label>
          <input type="text" class="one-hundred" value="<?php echo $batch->reference; ?>" step="0.01" disabled>
          <label>Amount</label>
          <input type="text" class="one-hundred" value="<?php echo number_format($record->amount, 2); ?>" step="0.01" disabled>
          <?php if ($record->status_id == 1) { ?>
            <label>Change Status</label>
            <select name="change_status" class="un-select" style="width:100%">
              <?php foreach ($dropdown as $res) { ?>
                <option value="<?php echo $res['status_id']; ?>"><?php echo $res['status_name'] ?></option>
              <?php } ?>
            </select>
          <?php } ?>
        <?php } ?>
      </div>
    </div>

  </div>
  <div class="modal-footer">

    <?php if (!in_array($this->session->position, array(72, 73, 83)) && $record->status_id == 1) {  ?>
      <input type="submit" class="btn btn-success" value="<?php echo ($record->status_id == 4) ? "Delete" : "Save"; ?>">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
    <?php } ?>
    <?php if (in_array($this->session->position, array(72, 73, 83))) {  ?>
      <input type="submit" class="btn btn-success" value="<?php echo ($record->status_id == 4) ? "Delete" : "Save"; ?>">
      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
    <?php } ?>
  </div>
</form>