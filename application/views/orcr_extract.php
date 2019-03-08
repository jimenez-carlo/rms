<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">ORCR Extract</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" action="<?= base_url().'orcr_extract/csv'?>" target="_blank" class="form-horizontal">
          <div class="control-group">
            <div class="control-label">For Extraction</div>
            <div class="controls"><?php print $count.' Units'; ?></div>
          </div>

          <div class="form-actions">
            <input type="hidden" name="batch_no" value="0">
            <input type="submit" name="extract" value="Extract" class="btn btn-success" data-batch="0">
          </div>

          <hr>

					<table class="table">
						<thead>
							<tr>
								<th>Batch #</th>
								<th>Timestamp</th>
								<th># of Units</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($table as $row) {
								print '<tr>';
								print '<td>Batch-'.$row->batch_no.'</td>';
								print '<td>'.$row->extract_date.'</td>';
								print '<td>'.$row->count.'</td>';
								print '<td><input type="submit" name="extract" value="Extract" class="btn btn-success" data-batch="'.$row->batch_no.'"></td>';
								print '</tr>';
							}

							if (empty($table)) {
								print '<tr>';
								print '<td>No batch result.</td>';
								print '<td></td>';
								print '<td></td>';
								print '</tr>';
							}
							?>
						</tbody>
					</table>
				</form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
  $('form input[type=submit]').click(function(){
    if (!confirm('Confirm to proceed.')) return false;

    var data_batch = $(this).attr('data-batch');
    $('form input[name=batch_no]').val(data_batch);
    setTimeout(function(){
      location.reload();
    }, 1000);
  });
});
</script>
