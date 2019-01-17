<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid form-horizontal">
	<div class="row-fluid">

    <!-- Attachment Block -->
    <div class="block span8">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Attachment</div>
      </div>
      <div class="block-content collapse in">
        <center><img src="./../../rms_dir/misc/<?php print $misc->mid.'/'.$misc->filename; ?>"></center>
			</div>
		</div>

		<!-- Sales Block -->
    <div class="block span4">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Sales</div>
      </div>
      <div class="block-content collapse in">
				<div class="control-group">
				  <div class="control-label">OR #</div>
				  <div class="controls"><?php print $misc->or_no; ?></div>
				</div>
				<div class="control-group">
				  <div class="control-label">OR Date</div>
				  <div class="controls"><?php print substr($misc->or_date, 0, 10); ?></div>
				</div>
				<div class="control-group">
				  <div class="control-label">Amount</div>
				  <div class="controls"><?php print $misc->amount; ?></div>
				</div>
				<div class="control-group">
				  <div class="control-label">Status</div>
				  <div class="controls"><?php print $misc->status; ?></div>
				</div>

				<?php if($_SESSION['position'] == 108 && $misc->status == "For Approval") { ?>
				<form method="post">
        <div class="control-group">
          <div class="control-label"></div>
          <div class="controls"><input type="submit" name="approve" value="Approve" class="btn btn-success"> <input type="submit" name="reject" value="Reject" class="btn btn-danger"></div>
        </div>
        </form>
        <?php } ?>

			</div>
		</div>
	</div>
</div>
