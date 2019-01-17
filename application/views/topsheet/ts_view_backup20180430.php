<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- Topsheet block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Topsheet</div>
      </div>
      <div class="block-content collapse in">

    	<?php if(!empty($topsheet)) { ?>
    		<table class="table">
				<thead>
					<tr>
						<th><p>Branch</p></th>
						<th><p>Expense Date</p></th>
						<th><p class="text-right">Given Amount</p></th>
						<th><p class="text-right">Expense</p></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($topsheet->sales as $sales) { ?>
					<tr>
						<td><?php print $sales->branch->b_code.' '.$sales->branch->name; ?></td>
						<td><?php print $sales->date; ?></td>
						<td><p class="text-right"><?php print number_format($sales->amount, 2, ".", ","); ?></p></td>
						<td><p class="text-right"><?php print number_format($sales->expense, 2, ".", ","); ?></p></td>
					</tr>
					<?php } ?>
				</tbody>
    		</table>
        <?php } ?>
	  </div>
	</div>

    <!-- Misc block -->
    <?php if(!empty($misc)) { ?>
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Miscellaneous</div>
      </div>
      <div class="block-content collapse in">
        <?php print $misc; ?>
      </div>
    </div>
    <?php } ?>
  </div>
 </div>
