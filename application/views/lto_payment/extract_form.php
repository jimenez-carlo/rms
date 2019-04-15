<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">LTO Payment</div>
			</div>
			<div class="block-content collapse in">
				<form class="form-horizontal" method="post" action="/lto_payment/csv" target="_blank">
					<fieldset>
            <div class="control-group span5">
              <div class="control-label">Pending Date</div>
              <div class="controls">
                <span style="display: inline-block; width: 50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-1 days'))), array('class' => 'datepicker')); ?>
                <br>
                <span style="display: inline-block; width: 50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d', strtotime('-1 days'))), array('class' => 'datepicker')); ?>
              </div>
            </div>

						<div class="control-group span5">
							<div class="control-label">Company</div>
							<div class="controls">
								<?php print form_dropdown('company', $company, set_value('company', 1)); ?>
							</div>
						</div>

						<div class="form-actions span5">
							<input type="submit" class="btn btn-success" value="Extract" name="extract" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
