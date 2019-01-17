<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<table class="table" style="margin:0px!important;">
	<thead>
		<tr>
			<th width="20"></th>
			<th width="100">Transmittal Date</th>
			<th>Amount</th>
			<th>Details</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$total = 0;
		foreach ($table as $row)
		{
			$total += $row->amount;
			print '<tr>';
			print '<td><input type="checkbox" name="fpid['.$row->fpid.']" value="'.$row->amount.'" onchange="total()" class="amount" checked></td>';
			print '<td>'.$row->date.'</td>';
			print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
			print '<td>
				<div class="row-fluid">
					<div class="span6" style="text-align:right;padding-right:10px;">
				'.number_format($row->amount_cash,2,'.',',').'<br>
				<i>('.$row->unit_cash.' cash units)</i>
					</div>
					<div class="span6" style="text-align:right;padding-right:10px;">
				'.number_format($row->amount_inst,2,'.',',').'<br>
				<i>('.$row->unit_inst.' inst units)</i>
					</div>
				</div>
				</td>';
			print '</tr>';
		}
		?>
	</tbody>
</table>
<hr style="margin:10px!important;">
<div class="control-group">
	<label class="control-label">Total</label>
	<div class="controls text">
		<span id="total-projected"><?php print number_format($total, 2, '.', ','); ?></span>
	</div>
</div>
<div class="control-group hide">
	<label class="control-label">Amount</label>
	<div class="controls">
      	<?php print form_input('amount', set_value('amount', $total)); ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">Debit Memo #</label>
	<div class="controls">
		<input type="text" name="dm_no">
	</div>
</div>
<div class="control-group hide date">
	<label class="control-label">Date Transferred</label>
	<div class="controls">
		<input type="text" name="date" class="datepicker" data-format="yyyy-mm-dd" value="<?php print date("Y-m-d"); ?>">
	</div>
</div>
<div class="control-group">
	<label class="control-label">Offline</label>
	<div class="controls">
		<input type="checkbox" name="offline" onchange="get_offline()">
	</div>
</div>