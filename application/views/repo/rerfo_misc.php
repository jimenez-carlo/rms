<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left"> Miscellaneous Expense</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post" enctype="multipart/form-data">
          <div class="span6">
            <div class="control-group">
              <label class="control-label">RERFO#</label>
              <div class="controls">
                <select name="repo_rerfo_id">
                  <?php foreach($rerfos AS $rerfo): ?>
                    <option value="<?php echo $rerfo['repo_rerfo_id']; ?>"><?php echo $rerfo['rerfo_number']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Type</label>
              <div class="controls">
                <select name="expense_type">
                  <option value="Voucher Transportation">Voucher Transportation</option>
                  <option value="Others">Others</option>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Amount</label>
              <div class="controls">
                <input name="amount" type="text" placeholder="0.00">
              </div>
            </div>
            <div class="form-actions">
              <input type="submit" name="save" value="Save" class="btn btn-success" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
            </div>
          </div>
          <div class="span6">
            <div class="span2"></div>
            <div class="control-group" style="margin-top: 10px;">
              <div class="control-label">Attachment</div>
              <div class="controls">
                <input class="input-file uniform_on" type="file" name="misc[expense][]">
                <br><b>Required file format: jpeg, jpg</b>
                <br><b>You can only upload upto 1MB</b>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
