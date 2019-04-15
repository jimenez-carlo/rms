<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">
					Missing Branches
				</div>
			</div>
			<div class="block-content collapse in">
				<table class="table" style="margin:0">
					<thead>
						<tr>
							<th>Branch Code</th>
							<th># of Affected Sales</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($table as $row)
						{
							print '<tr>';
							print '<td>'.$row->bcode.'</td>';
							print '<td>'.$row->sales.'</td>';
							print '</tr>';
						}

						if(empty($table))
						{
							print '<tr><td>No result.</td><td></td></tr>';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>