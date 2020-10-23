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
                <select id="misc-type" name="expense_type">
                  <option value="Transportation" <?php echo set_select('expense_type', 'Transportation', true); ?> >Transportation</option>
                  <option value="Meals" <?php echo set_select('expense_type', 'Meals'); ?> >Meals</option>
                  <option value="Xerox" <?php echo set_select('expense_type', 'Xerox'); ?> >Xerox</option>
                  <option value="Others" <?php echo set_select('expense_type', 'Others'); ?> >Others</option>
                </select>
              </div>
            </div>
            <div id="input-other" class="control-group <?php echo $hidden; ?>">
              <label class="control-label">Others</label>
              <div class="controls">
                <input id="other" name="others" type="text" placeholder="Input Misc Expense" value="<?php echo set_value('others'); ?>" <?php echo $disabled; ?>>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Amount</label>
              <div class="controls">
                <input name="amount" type="text" placeholder="0.00" value="<?php echo set_value('amount'); ?>">
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

<script>
$('#misc-type').on('change', function() {
  var misc = $(this).val();
  if (misc === 'Others') {
    $('#input-other').removeClass('hidden');
    $('#other').prop('disabled',false);
  } else {
    $('#input-other').addClass('hidden');
    $('#other').prop('disabled',true);
  }
});
</script>
