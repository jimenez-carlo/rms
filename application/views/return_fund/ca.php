<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Return Fund</div>
      </div>
      <div class="block-content collapse in">

        <form class="form-horizontal" method="post" enctype="multipart/form-data">
          <fieldset>
            <div class="control-group">
              <div class="control-label">Reference #</div>
              <div class="controls text"><?php print $fund->reference; ?></div>
            </div>

            <div class="control-group">
              <div class="control-label">Amount</div>
              <div class="controls">
                <?php print form_input('amount', set_value('amount')); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Deposit Slip</div>
              <div class="controls">
                <input type="file" name="slip" class="input-file uniform_on">
                <br><b>Required file format: JPEG, JPG</b>
                <br><b>File must not exceed 1MB</b>
              </div>
            </div>

            <div class="form-actions">
              <input type="submit" name="save" value="Save" class="btn btn-success" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
            </div>
          </fieldset>
        </form>

			</div>
		</div>
	</div>
</div>