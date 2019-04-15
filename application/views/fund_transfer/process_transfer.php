<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<form action="#" id="form" class="form-horizontal" style="margin:0px!important;">
	<p style="padding-left:50px;font-weight:bold;">Make sure that all information are correct.</p>

	<div class="control-group">
		<label class="control-label">Reference #</label>
		<div class="controls text"><?php print $voucher->reference; ?></div>
	</div>
	<div class="control-group">
		<label class="control-label">Document #</label>
		<div class="controls text"><?php print $voucher->voucher_no; ?></div>
	</div>
	<div class="control-group">
		<label class="control-label">Amount</label>
		<div class="controls text"><?php print $voucher->amount; ?></div>
	</div>

  <div class="control-group">
    <label class="control-label">Debit Memo #</label>
    <div class="controls">
      <input type="text" name="dm_no">
    </div>
  </div>
	<div class="control-group hide date">
		<label class="control-label">Date Processed</label>
		<div class="controls">
			<input type="text" name="process_date" class="datepicker" data-format="yyyy-mm-dd" value="<?php print date("Y-m-d"); ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Offline</label>
		<div class="controls">
			<input type="checkbox" name="offline" onchange="get_offline()">
		</div>
	</div>
</form>