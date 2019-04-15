<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="control-group">
	<label class="control-label">Region</label>
	<div class="controls text">
		<span id="total-projected"><?php print $fund->region; ?></span>
	</div>
</div>
<div class="control-group">
	<label class="control-label">Company</label>
	<div class="controls text">
		<span id="total-projected"><?php print $fund->company; ?></span>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><text style="color:red;">*</text> Maintaining Balance</label>
	<div class="controls">
		<input type="text" name="m_balance" class="form-control numeric" value="<?php print number_format($fund->m_balance,2,'.',''); ?>">
	</div>
</div>
<div class="control-group">
	<label class="control-label"><text style="color:red;">*</text> Account Number</label>
	<div class="controls">
		<input type="text" name="acct_number" value="<?php print $fund->acct_number; ?>">
	</div>
</div>
<div class="control-group">
	<label class="control-label"><text style="color:red;">*</text> Signatory #1</label>
	<div class="controls">
		<input type="text" name="sign_1" value="<?php print $fund->sign_1; ?>">
	</div>
</div>
<div class="control-group">
	<label class="control-label"><text style="color:red;">*</text> Signatory #2</label>
	<div class="controls">
		<input type="text" name="sign_2" value="<?php print $fund->sign_2; ?>">
	</div>
</div>
<div class="control-group">
	<label class="control-label"><text style="color:red;">*</text> Signatory #3</label>
	<div class="controls">
		<input type="text" name="sign_3" value="<?php print $fund->sign_3; ?>">
	</div>
</div>