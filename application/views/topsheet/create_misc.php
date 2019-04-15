<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
	.form-horizontal .control-group {
    margin-bottom: 5px;
	}
</style>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Create Topsheet</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post" style="margin:0px;">
          <?php
          print form_hidden('summary', '1');
          foreach ($rid as $id) print form_hidden('rid[]', $id);
          print form_hidden('tot_amt', $tot_amt);
          print form_hidden('tot_exp', $tot_exp);
          ?>

          <table class="table">
            <thead>
              <tr>
                <th><p></p></th>
                <th><p>OR #</p></th>
                <th><p>OR Date</p></th>
                <th><p>Amount</p></th>
                <th><p>Type</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($miscs as $misc)
              {
                print '<tr>';
                if ($misc->topsheet == 0) print '<td>'.form_checkbox('mid['.$misc->mid.']', $misc->mid, FALSE).'</td>';
                else print '<td>'.form_checkbox('mid['.$misc->mid.']', $misc->mid, TRUE, array('class' => 'hide')).'<span style="color:green">included</span></td>';

                print '<td>'.$misc->or_no.'</td>';
                print '<td>'.$misc->or_date.'</td>';
                print '<td>'.$misc->amount.'</td>';
                print '<td>'.$misc->type.'</td>';
                print '</tr>';
              }

              if (empty($misc))
              {
                print '<tr>
                  <td></td>
                  <td>No pending miscellaneous expense.</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  </tr>';
              }
              ?>
            </tbody>
          </table>

          <hr>
          <fieldset>
          	<div class="span1"></div>
          	<div class="span4">
	            <div class="control-group">
	              <label class="control-label">Meal</label>
	              <div class="controls text tot-meal">0.00</div>
	            </div>
	            <div class="control-group">
	              <label class="control-label">Photocopy</label>
	              <div class="controls text tot-photo">0.00</div>
	            </div>
	            <div class="control-group">
	              <label class="control-label">Transportation</label>
	              <div class="controls text tot-trans">0.00</div>
	            </div>
	            <div class="control-group">
	              <label class="control-label">Others</label>
	              <div class="controls text tot-other">0.00</div>
	            </div>
	            <div class="control-group">
	              <label class="control-label"><b>Total Miscellaneous</b></label>
	              <div class="controls text tot-misc">0.00</div>
	            </div>
            </div>

            <div class="span7">
	            <div class="control-group hide">
	              <label class="control-label">Topsheet Expense</label>
	              <div class="controls text ts-exp"><?php print $tot_exp; ?></div>
	            </div>
	            <div class="control-group">
	              <label class="control-label">Total Amount</label>
	              <div class="controls text tot-amt"><?php print $tot_amt; ?></div>
	            </div>
	            <div class="control-group">
	              <label class="control-label">Total Expense</label>
	              <div class="controls text tot-exp">0.00</div>
	            </div>
	            <div class="control-group">
	              <label class="control-label"><b>Topsheet Balance</b></label>
	              <div class="controls text ts-bal">0.00</div>
	            </div>

		          <div class="form-actions span12">
		            <b><p class="form-msg m1 hide" style="color:red">No miscellaneous expense included.</p></b>
		            <b><p class="form-msg m2 hide" style="color:red">Balance must not be negative.</p></b>
		            <b><p class="form-msg m3 hide">Please make sure all information are correct before proceeding.</p></b>

		            <input type="submit" name="submit" value="Preview Summary" class="btn btn-success submit disabled" disabled>
		          </div>
            </div>

          </fieldset>
        </form>
  	  </div>
  	</div>
  </div>
</div>

<script>
$(function(){
	function compute() {
    var tot_meal = 0;
    var tot_photo = 0;
    var tot_trans = 0;
    var tot_other = 0;
    var amt = 0;
    var type = '';
    $("table input:checked").each(function(){
      amt = toFloat($(this).closest('tr').find('td:eq(3)').text());
      type = $(this).closest('tr').find('td:last-child').text();

      switch(type)
      {
        case 'Meal': tot_meal += amt; break;
        case 'Photocopy': tot_photo += amt; break;
        case 'Transportation': tot_trans += amt; break;
        default: tot_other += amt; break;
      }
    });

    var tot_misc = tot_meal+tot_photo+tot_trans+tot_other;
    var ts_exp = toFloat($(".ts-exp").text());
    var tot_amt = toFloat($(".tot-amt").text());
    var tot_exp = ts_exp + tot_misc;
    var ts_bal = tot_amt - tot_exp;

    $(".tot-meal").text(commafy(tot_meal));
    $(".tot-photo").text(commafy(tot_photo));
    $(".tot-trans").text(commafy(tot_trans));
    $(".tot-other").text(commafy(tot_other));
    $(".tot-misc").text(commafy(tot_misc));
    $(".tot-amt").text(commafy(tot_amt));
    $(".tot-exp").text(commafy(tot_exp));
    $(".ts-bal").text(commafy(ts_bal));

    $("table input").attr('disabled', 'disabled');
    $(".form-msg").addClass('hide');
    $(".submit").addClass('disabled').attr('disabled', '');

    if (tot_misc == 0) {
      $("table input").removeAttr('disabled');
      $(".form-msg.m1").removeClass('hide');
      $(".submit").removeClass('disabled').removeAttr('disabled');
    }
    if (ts_bal < 0) {
      $("table input:checked").removeAttr('disabled');
      $(".form-msg.m2").removeClass('hide');
    }
    else {
      $("table input").removeAttr('disabled');
      $(".form-msg.m3").removeClass('hide');
      $(".submit").removeClass('disabled').removeAttr('disabled');
    }
	}

  $(document).ready(function(){
    $('table input').click(function(){
      compute();
    });
    compute();
  });
});
</script>