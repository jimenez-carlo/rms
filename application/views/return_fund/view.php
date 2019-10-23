<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid form-horizontal">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Return Fund # <?php print $return->rfid; ?></div>
      </div>
      <div class="block-content collapse in">
        <div class="row-fluid">

          <div class="span5">

            <?php if (empty($return->liq_date) && $_SESSION['position'] == 3) { ?>
            <form method="post" class="form-horizontal">
              <div class="form-actions">
                <input type="submit" name="liquidate" value="Liquidate" class="btn btn-success" onclick="return confirm('This action cannot be undone: Liquidate. Continue?')">
              </div>
            </form>
            <?php } ?>

            <div class="control-group">
              <div class="control-label">Reference #</div>
              <div class="controls text"><?php print $return->reference; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Amount</div>
              <div class="controls text"><?php print number_format($return->amount, 2, '.', ','); ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Deposit Slip</div>
              <div class="controls text"><?php print '<a href="'.base_url().'rms_dir/deposit_slip/'.$return->rfid.'/'.$return->slip.'" target="_blank">'.$return->slip.'</a>'; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Date Liquidated</div>
              <div class="controls text"><?php print (empty($return->liq_date)) ? '-' : $return->liq_date; ?></div>
            </div>
          </div>

        </div>
			</div>
		</div>
	</div>
</div>
