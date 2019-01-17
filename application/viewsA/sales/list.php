<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">
					Sales
				</div>
			</div>
			<div class="block-content collapse in">
			
				<form class="form-horizontal" method="post" style="margin:10px 0px">
					<fieldset>
						<div class="control-group" style="margin:0;">
							<?php
								echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
								echo '<div class="controls">';
								echo form_input('engine_no', set_value('engine_no'));
								echo '<input type="submit" class="btn btn-success" value="Search" name="submit">';
								echo '</div>';
							?>
						</div>
					</fieldset>
				</form>

				<?php if(!empty($sales)) { ?>
				<table class="table" style="margin:0">
					<thead>
						<tr>
							<th>Date Sold</th>
							<th>Engine #</th>
							<th>Customer Name</th>
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						print '<tr>';
						print '<td>'.substr($sales->date_sold, 0, 10).'</td>';
						print '<td>'.$sales->engine_no.'</td>';
						print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
						print '<td>'.$sales->status.'</td>';

						print '<td>';
						print '<a href="sales/view/'.$sales->sid.'" class="btn btn-success">View</a>';

						if ($sales->edit) {
							print '<a href="sales/edit/'.$sales->sid.'" class="btn btn-success">Edit</a>';
						}

						print '</td>';
						print '</tr>';
						?>
					</tbody>
				</table>
				<?php } ?>

			</div>
		</div>
	</div>
</div>