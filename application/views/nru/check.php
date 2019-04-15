<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Please select a check to use</div>
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
          print form_hidden('total_mc_check', $total_mc_check);
          print form_hidden('total_regn_check', $total_regn_check);
          print form_hidden('total_mc_cash', $total_mc_cash);
          print form_hidden('total_regn_cash', $total_regn_cash);
          print form_hidden('cash', $cash);
          ?>

          <table class="table" style="margin:0">
            <thead>
              <tr>
                <th><p></p></th>
                <th><p>Check #</p></th>
                <th><p>Check Date</p></th>
                <th><p>Amount</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $check)
              {
                print "<tr>";
                print '<td><input type="checkbox" name="check[]" value="'.$check->cid.'"></td>';
                print "<td>".$check->check_no."</td>";
                print "<td>".substr($check->check_date, 0, 10)."</td>";
                print "<td>".number_format($check->amount, 2, '.', ',')."</td>";
                print "</tr>";
              }

              if (empty($table))
              {
                print '<tr>
                  <td>No result.</td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>';
              }
              ?>
            </tbody>
          </table>
          <hr>

          <div class="span1"></div>
          <div class="span3">
            <div class="control-group">
              <label class="control-label">Total Check Fund</label>
              <div class="controls text tot-check">0.00</div>
            </div>
            <div class="control-group">
              <label class="control-label">Total Check Expense</label>
              <div class="controls text tot-check-exp"><?php print number_format($total_regn_check, 2, '.', ','); ?></div>
            </div>
            <div class="control-group">
              <label class="control-label">Computed Check Fund</label>
              <div class="controls text comp-check">0.00</div>
            </div>
          </div>

          <div class="span1"></div>
          <div class="span3">
            <div class="control-group">
              <label class="control-label">Total Cash Fund</label>
              <div class="controls text tot-cash"><?php print number_format($cash, 2, '.', ',')  ; ?></div>
            </div>
            <div class="control-group">
              <label class="control-label">Total Cash Expense</label>
              <div class="controls text tot-cash-exp"><?php print number_format($total_regn_cash, 2, '.', ','); ?></div>
            </div>
            <div class="control-group">
              <label class="control-label">Computed Cash Fund</label>
              <div class="controls text comp-cash">0.00</div>
            </div>
          </div>
          
          <div class="form-actions span12">
            <b><p class="form-msg m1 hide" style="color:red">Check fund is not enough to accomodate total expense.</p></b>
            <b><p class="form-msg m2 hide" style="color:red">Cash fund is not enough to accomodate total expense.</p></b>
            <b><p class="form-msg m3 hide">Please make sure all information are correct before proceeding.</p></b>

            <input type="submit" name="submit" value="Submit" class="btn btn-success submit disabled" disabled>
            <input type="submit" name="back[1]" value="Back" class="btn btn-success">
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){
  function compute(){
    var tot_check = toFloat($(".tot-check").text());
    var tot_check_exp = toFloat($(".tot-check-exp").text());
    var tot_cash = toFloat($(".tot-cash").text());
    var tot_cash_exp = toFloat($(".tot-cash-exp").text());

    var comp_check = tot_check - tot_check_exp;
    var comp_cash = tot_cash - tot_cash_exp;

    $(".tot-check").text(commafy(tot_check));
    $(".tot-check-exp").text(commafy(tot_check_exp));
    $(".comp-check").text(commafy(comp_check));
    $(".tot-cash").text(commafy(tot_cash));
    $(".tot-cash-exp").text(commafy(tot_cash_exp));
    $(".comp-cash").text(commafy(comp_cash));

    $("table input").attr('disabled', 'disabled');
    $(".form-msg").addClass('hide');
    $(".submit").addClass('disabled').attr('disabled', '');

    if (comp_check < 0) {
      $(".form-msg.m1").removeClass('hide');
      $("table input").removeAttr('disabled');
    }
    if (comp_cash < 0) {
      $(".form-msg.m2").removeClass('hide');
    }
    if (comp_check >= 0 && comp_cash >= 0) {
      $("table input:checked").removeAttr('disabled');
      $(".form-msg.m3").removeClass('hide');
      $(".submit").removeClass('disabled').removeAttr('disabled');
    }
  }

  $(document).ready(function(){
    $('table input').click(function(){
      var tot = 0;
      $("table input:checked").each(function(){
        tot += toFloat($(this).closest('tr').find('td:last-child').text());
      });
      $(".tot-check").text(commafy(tot));
      compute();
    });
    compute();
  });
});
</script>
