<style>
.modal{
	position: fixed;
/* width: 50%; */
top:10% !important;
left: 35%;
margin-top: auto; /* Negative half of height. */
margin-left: auto; /* Negative half of width. */
}
</style>
<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" method="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span5">
        <input type="hidden" class="span5" value="<?php echo $record->id; ?>" name="return_fund_id">
        <label>Amount</label>
        <?php if($record->status_id == 3) { ?>
        <input required type="number" class="span5" placeholder="0.00" value="<?php echo $record->amount?>" name="amount" step="0.01">
        <?php }else{ ?>
        <label><?php echo number_format($record->amount,2); ?></label>
        <?php } ?>
        <label>Attachment</label>
        <center>
        <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="" style="width:auto ;height:250px" id="output">
        </center>
        <?php if($record->status_id == 5) { ?>
        <input required type="file" class="span5" placeholder="Input here." name="attachment" id="attachment" onchange="document.getElementById('output').src = window.URL.createObjectURL(this.files[0])">
        <b>Required file format: jpeg, jpg You can only upload upto 1MB </b>
        <?php } ?>
      </div>
    </div>

  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success" value="<?php echo ($record->status_id == 4) ? "Delete" : "Save" ;?>">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>