<style>
.modal{
	position: fixed;
width: 35%;
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
        <input type="hidden" class="span5" value="<?php echo $record->repo_batch_id; ?>" name="batch_id">
        <label>Amount</label>
        <input required type="text" class="span5" placeholder="0.00" value="" name="amount">
        <label>Attachment</label>
        <center>
        <img src="<?php echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; ?>" alt="" style="width:auto ;height:250px" id="output">
        </center>
        <input required type="file" class="span5" placeholder="Input here." name="attachment" id="attachment" onchange="document.getElementById('output').src = window.URL.createObjectURL(this.files[0])">
        <b>Required file format: jpeg, jpg You can only upload upto 1MB </b>
      </div>

    </div>

  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success" value="Save">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>