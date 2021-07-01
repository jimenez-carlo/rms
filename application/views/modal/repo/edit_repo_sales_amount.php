<form id="FormModal" class="form-horizontal" style="margin:0px!important;" enctype="multipart/form-data" metho="POST">
  <div class="modal-body">
    <div class="alert alert-error hide">
      <button class="close" data-dismiss="alert">&times;</button>
      <div class="error"></div>
    </div>
    <div class="row">
      <div class="span4">
        <label>CA Reference# <?php echo $record->reference; ?></label>
        <input type="hidden" class="span4" value="update_misc" placeholder="Input here." name="action">
        <input required type="hidden" class="span4" placeholder="Input here." name="edit_id">
        <label>Date</label>
        <input required type="text" class="span4 datepicker" value="" placeholder="Input here." name="date">
        <label>OR No#</label>
        <input required type="text" class="span4" value="" placeholder="Input here." name="or_no">
        <label>Expense Type</label>
        <select name="expense_type" id="" style="width: 100%;">
        </select>
        <label>Amount</label>
        <input required type="text" class="span4" placeholder="0.00" value="" name="amount">
      </div>

      <div class="span4">
        <div class="tabbable">
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
              <label>Attachment</label>
              <img src="<?php //echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; 
                        ?>" alt="" style="width: 250px;height:250px" id="output">
              <input type="file" class="span4" placeholder="Input here." name="file" id="file" onchange="document.getElementById('output').src = window.URL.createObjectURL(this.files[0])">
              <b>Required file format: jpeg, jpg You can only upload upto 1MB </b>
            </div>
            <div class="tab-pane" id="tab2">
              <label>Attachment</label>
              <img src="<?php //echo !empty($record->image_path) ? BASE_URL . $record->image_path : BASE_URL . "img/NoImage.jpg"; 
                        ?>" alt="" style="width: 250px;height:250px" id="output">
              <input type="file" class="span4" placeholder="Input here." name="file" id="file" onchange="document.getElementById('output').src = window.URL.createObjectURL(this.files[0])">
              <b>Required file format: jpeg, jpg You can only upload upto 1MB </b>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success" id="btn-submit" value="Save">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
  </div>
</form>