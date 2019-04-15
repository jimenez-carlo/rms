<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
table th:first-child,
table th:nth-child(4) {
	text-align: right; 
}
table th:first-child {
	width: 20%;
}
table th:last-child {
	width: 30%;
}
</style>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">NRU summary</div>
      </div>
      <div class="block-content collapse in span">

        <form method="post" class="form-horizontal" style="margin: 0px;" onkeypress="return event.keyCode != 13;" onsubmit="return confirm('Please make sure all information are correct before proceeding. Continue?');>">
          <?php
          print form_hidden('ltid', $ltid);
          print form_hidden('company', $company);
          foreach ($registration as $sid => $value)
          {
            print form_hidden('registration['.$sid.']', $registration[$sid]);
            print form_hidden('fund['.$sid.']', $fund[$sid]);
          }
          print form_hidden('total_mc', $total_mc);
          print form_hidden('total_regn', $total_regn);
          // print form_hidden('total_mc_check', $total_mc_check);
          // print form_hidden('total_regn_check', $total_regn_check);
          // print form_hidden('total_mc_cash', $total_mc_cash);
          // print form_hidden('total_regn_cash', $total_regn_cash);
          // print form_hidden('cash', $cash);

          // $check_tot = 0;
          // if (!empty($check)) {
          // 	foreach ($check as $row)
          //  {
	        //  	print form_hidden('check[]', $row->cid);
	        //  	$check_tot += $row->amount;
	        //  }
          // }

          // $comp_cash = $cash - $total_regn_cash;
          // $comp_check = $check_tot - $total_regn_check;
          $comp_cash = $cash - $total_regn;
          $comp_check = 0;

          // $new_fund = $account->fund + $comp_check;
          // $new_check = $account->cash_on_check - $check_tot;
          $new_fund = $account->fund;
          ?>

          <div class="control-group">
            <div class="controls text">
              <?php
              if ($comp_cash < 0) {
                print '<b><p style="color:red">Cash fund is not enough to accomodate total expense.</p></b>';
              }
              else {
                print '<b><p>Remaining check amount must be deposited back to respective bank account.</p></b>';
              }
            	?>
            </div>
          </div>

          <table class="table">
          	<tbody>
          		<tr>
          			<th>Account</th>
          			<td>
          				<?php print $account->region_name.' '.$account->company_name; ?>
          			</td>
          			<th></th>
          			<th></th>
          			<th></th>
          		</tr>
          		<tr>
          			<th>Bank Fund</th>
          			<td>
	              	<?php
	              	print number_format($account->fund, 2, '.', ',');
	              	if ($comp_check > 0) {
	              		print ' <span style="color:green">(+'.number_format($comp_check, 2, '.', ',').')</span> ';
	              	}
	              	?>
	              </td>
          			<th>
          				<?php if ($comp_check > 0) print ' =====>> '; ?>
          			</th>
          			<th>New Bank Fund</th>
          			<td>
	              	<?php
	              	if ($comp_check > 0) {
	              		print number_format($new_fund, 2, '.', ',');
	              	} else {
	              		print number_format($account->fund, 2, '.', ',');
	              	}
	              	?>
          			</td>
          		</tr>
          		<tr>
          			<th>On Hand Fund</th>
          			<td>
	              	<?php
	              	print number_format($account->cash_on_hand, 2, '.', ',');
	              	if ($total_regn > 0) {
	              		print ' <span style="color:red">(-'.number_format($total_regn, 2, '.', ',').')</span> ';
	              	}
	              	?>
          			</td>
          			<th>
          				<?php if ($total_regn > 0) print ' =====>> '; ?>
          			</th>
          			<th>Remaining On Hand Fund</th>
          			<td>
	              	<?php
	              	if ($total_regn > 0) {
	              		print number_format($comp_cash, 2, '.', ',');
	              	} else {
	              		print number_format($account->cash_on_hand, 2, '.', ',');
	              	}
	              	?>
          			</td>
          		</tr>
          		<!-- <tr>
          			<th>Check Fund</th>
          			<td>
	              	<?php
	              	print number_format($account->cash_on_check, 2, '.', ',');
	              	if ($check_tot > 0) {
	              		print ' <span style="color:red">(-'.number_format($check_tot, 2, '.', ',').')</span> ';
	              	}
	              	?>
          			</td>
          			<th>
          				<?php if ($check_tot > 0) print ' =====>> '; ?>
          			</th>
          			<th>Remaining Check Fund</th>
          			<td>
	              	<?php
	              	if ($check_tot > 0) {
	              		print number_format($new_check, 2, '.', ',');
	              	} else {
	              		print number_format($account->cash_on_check, 2, '.', ',');
	              	}
	              	?>
          			</td>
          		</tr> -->
          	</tbody>
          </table>
          
          <div class="form-actions span12">
            <?php
						print '<input type="submit" name="submit" value="Submit" class="btn btn-success hide" disabled> ';
						
            $disabled = ($comp_cash < 0) ? 'disabled' : '';
            print '<input type="submit" name="submit_all" value="Submit" class="btn btn-success" '.$disabled.'> ';

            // $back_key = ($total_regn_check == 0) ? 1 : 2;
            $back_key = 1;
            print '<input type="submit" name="back['.$back_key.']" value="Back" class="btn btn-success"> ';
            ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
		$('form').submit(function(){
			$('input[name=submit_all]').addClass('hide');
			$('input[name=submit]').removeClass('hide');
		});
  });
});
</script>