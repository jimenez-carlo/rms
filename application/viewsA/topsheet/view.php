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
        <form class="form-horizontal" method="post" style="margin:10px 0px;">
          <fieldset>
            <div class="row-fluid">
                <div class="control-group span4" style="margin-bottom:0;">
                    <div class="control-label">
                        Company
                    </div>
                    <div class="controls">
                      	<select name="company">
                          <option value=1>MNC</option>
                          <option value=3>HPTI</option>
                          <option value=2>MTI</option>
                      	</select>
                    </div>
                </div>
                <input type="submit" value="Search" class="btn btn-success" name="search">
            </div>
          </fieldset>
        </form>

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
				<tfoot style="font-size: 20px">
					<tr>
						<th></th>
						<th>Total</th>
						<th><p class="text-right">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ","); ?></p></th>
                        <th><p class="text-right exp">&#x20b1 <?php print number_format($topsheet->total_expense, 2, ".", ","); ?></p></th>
					</tr>
				</tfoot>
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
