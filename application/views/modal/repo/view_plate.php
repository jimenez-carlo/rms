
<?php if (!empty($record->plate_number)) { ?>

<style>
  input{
    /* font-weight: bold; */
  }
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
    <!-- <div class="span6">
      <label>Branch</label>
      <select name="branch" style="width:100%">
      </select>
    </div> -->
    <div class="span6">
      <label>Date Encoded</label>
      <input type="text" step="0.01" class="span6" value="<?php echo $record->date_encoded; ?>" disabled>
    </div>
    <div class="span3">
      <label>Plate Transaction No#</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->plate_trans_no; ?>" disabled>
    </div>
    <div class="span3">
      <label>SI</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->si_no; ?>" disabled>
    </div>
    <div class="span3">
      <label>AR</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->ar_no; ?>" disabled>
    </div>
    <div class="span3">
      <label>Status</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->status_name; ?>" disabled>
    </div>
    <div class="span3">
      <label>Received Date</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->received_dt; ?>" disabled>
    </div>
    <div class="span3">
      <label>Customer Claimed Date</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->received_cust; ?>" disabled>
    </div>
    <div class="span6">
      <label>Customer Name</label>
      <input type="text" step="0.01" class="span6" value="<?php echo $record->customer_name; ?>" disabled>
    </div>
    <div class="span3">
      <label>Branch</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->branch; ?>" disabled>
    </div>
    <div class="span3">
      <label>Company</label>
      <input type="text" step="0.01" class="span3" value="<?php echo $record->company_code; ?>" disabled>
    </div>
    


  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
  </div>
</form>
<?php } ?>