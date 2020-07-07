<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<form class="form-horizontal" method="post" style="margin:0;">
  <?php
    if (isset($CA)) {
      print form_hidden('CA', $batch_ref['vid']);
    }
    if (isset($EPP)) {
      print form_hidden('EPP', $batch_ref['lpid']);
    }
    print form_hidden('summary', 1);
  ?>

  <fieldset>
    <div class="control-group span4">
      <div class="control-label" style="padding-top:0">Date</div>
      <div class="controls"><?php print $batch_ref['date']; ?></div>
    </div>
    <div class="control-group span4">
      <div class="control-label" style="padding-top:0">Region</div>
      <div class="controls"><?php print $batch_ref['region']; ?></div>
    </div>
    <div class="control-group span4">
      <div class="control-label" style="padding-top:0">Company</div>
      <div class="controls"><?php print $batch_ref['company']; ?></div>
    </div>
  </fieldset>

  <hr>
  <table class="table tbl-sales table-condensed" style="display:block; margin:0;">
    <thead>
      <tr>
        <th><p>#</p></th>
        <th><p>Branch</p></th>
        <th width=75><p>Date Sold</p></th>
        <th width=125><p>Engine #</p></th>
        <th><p>Sales Type</p></th>
        <th><p>Registration Type</p></th>
        <th><p>Reference #</p></th>
        <th><p class="text-right">Amount Given</p></th>
        <th><p class="text-right">LTO Registration</p></th>
        <th><p class="text-right">Total Expense</p></th>
        <th><p class="text-right">Balance</p></th>
        <th><p>Status</p></th>
        <th><p>Acctg. Status</p></th>
      </tr>
    </thead>
    <tbody style="<!-- display:block; --> max-height:690px; overflow:auto;">
      <?php
      $count = 1;
      $total_amt = 0;
      $total_exp = 0;

      foreach (json_decode($batch_ref['sales']) as $sales)
      {
        $sales->amount = ($sales->registration_type == 'Free Registration' || stripos($sales->registration_type, 'subsidy') !== false) ? 1500 : $sales->amount;
        $clickable = ($sales->selectable) ? 'onclick="attachment('.$sales->sid.', 1)"' : '';
        if ($sales->disapprove === null) {
          $disapprove = '<td class="text-success">Ok</td>';
        } elseif (in_array($sales->disapprove, array('Resolved', 'For Sap Uploading', 'Done'))) {
          $disapprove = '<td class="text-success">'.$sales->disapprove.'</td>';
        } else {
          $disapprove = '<td class="text-error">'.$sales->disapprove.'</td>';
        }

        print '<tr class="sales-'.$sales->sid.'" '.$clickable.'>';
        print '<td>'.$count.'</td>';
        $count++;
        print '<td>';
        print '<input type="hidden" name="sid[]" value="'.$sales->sid.'" disabled>';
        print $sales->bcode.' '.$sales->bname;
        print '</td>';
        print '<td>'.$sales->date_sold.'</td>';
        print '<td>'.$sales->engine_no.'</td>';
        print '<td>'.$sales->sales_type.'</td>';
        print '<td>'.$sales->registration_type.'</td>';
        print '<td>'.$sales->ar_no.'</td>';
        print '<td><p class="text-right sales-amt">'.number_format($sales->amount, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right">'.number_format($sales->registration, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right sales-exp">'.number_format($sales->registration, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right">'.number_format($sales->amount - $sales->registration, 2, ".", ",").'</p></td>';
        print '<td>'.$sales->status.'</td>';
        print $disapprove;
        print '</tr>';
        if ($sales->status === "Registered") {
          $total_amt += $sales->amount;
          $total_exp += $sales->registration;
        }
      }

    print '</tbody>';
    print '<tbody>';
      // Miscellaneous
      print '<tr style="border-top: double">';
      print '<th colspan="2"><p>#</p></th>';
      print '<th colspan="2"><p>OR #</p></th>';
      print '<th colspan="2"><p>OR Date</p></th>';
      print '<th colspan="2"><p class="text-right">Type</p></th>';
      print '<th colspan="2"><p class="text-right">Expense</p></th>';
      print '<th colspan="2"><p class="text-right">Status</p></th>';
      print '</tr>';

      $misc_expenses = json_decode($misc_expense);
      if ($misc_expenses !== NULL) {
        $exp_count = 1;
        foreach ($misc_expenses as $misc)
        {
            $text_color = (in_array($misc->status, array('Approved', 'Resolved', 'For Liquidation', 'Liquidated'))) ? 'text-success' : 'text-error';
            print '<tr class="misc-'.$misc->mid.'" onclick="attachment('.$misc->mid.', 2)">';
            print '<td colspan="2">'.$exp_count.'</td>';
            print '<td colspan="2">';
            print '<input type="hidden" name="mid[]" value="'.$misc->mid.'" disabled>';
            print $misc->or_no;
            print '</td>';
            print '<td colspan="2">'.$misc->or_date.'</td>';
            print '<td colspan="2"><p class="text-right">'.$misc->type.'</p></td>';
            print '<td colspan="2"><p class="text-right misc-exp">'.$misc->amount.'</p></td>';
            print '<td colspan="2"><p class="text-right '.$text_color.'">'.$misc->status.'</p></td>';
            print '</tr>';

            if (array('Approved', 'Resolved')) {
              $total_exp += $misc->amount;
            }
        }
      } else {
        print '<tr>';
        print '<td colspan="3"><p style="color:red"><b>No included miscellaneous expense.</b></p></td>';
        print '<td colspan="2"></td>';
        print '<td colspan="2"></td>';
        print '<td colspan="3"></td>';
        print '</tr>';
      }

      ?>
    </tbody>
    <tfoot style="border-top: dotted gray; font-size: 16px">
      <tr>
        <th colspan="10"></th>
        <th><p>For Upload</p></th>
        <th><p>Total</p></th>
      </tr>
      <tr>
        <th colspan="9"></th>
        <th><p class="text-right">Amount</p></th>
        <th><p class="text-right fup-amt" style="color:red">&#x20b1 0.00</p></th>
        <th><p class="text-right tot-amt">&#x20b1 <?php print number_format($total_amt, 2, ".", ","); ?></p></th>
      </tr>
      <tr>
        <th colspan="9">
          <p class="form-msg msg1" style="color:red">Balance for upload must not be zero.</p>
          <p class="form-msg msg2" style="color:red">Balance for upload must not be negative.</p>
          <p class="form-msg msg3" style="color:red">Remaining amount will not be able to accomodate remaining expenses.</p>
          <p class="form-msg msg4">Please make sure all information are correct before proceeding.</p>
        </th>
        <th><p class="text-right">Expense</p></th>
        <th><p class="text-right fup-exp" style="color:red">&#x20b1 0.00</p></th>
        <th><p class="text-right tot-exp">&#x20b1 <?php print number_format($total_exp, 2, ".", ","); ?></p></th>
      </tr>
      <tr>
        <th colspan="9">
          <input type="submit" name="submit" value="Preview Summary" class="btn btn-success">
        </th>
        <th><p class="text-right">Balance</p></th>
        <th><p class="text-right fup-bal" style="color:red">&#x20b1 0.00</p></th>
        <th><p class="text-right tot-bal">&#x20b1 <?php print number_format($total_amt - $total_exp, 2, ".", ","); ?></p></th>
      </tr>
    </tfoot>
  </table>
</form>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog" style="width: 85%; left: 30%;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">View attachment</h3>
      </div>
      <div class="modal-body form" style="max-height: 430px;">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <div class="form-body" style="height: 430px;">
          <!-- see attachment.php -->
        </div>
      </div>
      <div class="modal-footer">
        <button id="include_for_upload" type="button" onclick="for_upload(1)" class="btn btn-success include">Include For Upload</button>
        <button type="button" onclick="for_upload(-1)" class="btn btn-success exclude">Exclude For Upload</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
var id;
var type;
var selector;

function attachment(_id, _type) {
  id = _id;
  type = _type;

  switch(type) {
    case 1: selector = '.sales-'+id; break;
    case 2: selector = '.misc-'+id; break;
  }

  $.ajax({
    url : "orcr_checking/attachment",
    type: "POST",
    data: {'id': id, 'type': type},
    dataType: "JSON",
    success: function(data)
    {
      $('.form-body').html(data.page); // reset form on modals
      $("#include_for_upload").prop("disabled", data.disable);
      $('#modal_form').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });

  if ($(selector).hasClass('info')) {
    $('.include').addClass('hide');
    $('.exclude').removeClass('hide');
  }
  else {
    $('.include').removeClass('hide');
    $('.exclude').addClass('hide');
  }
}

function for_upload(_x) {
  var amt = 0;
  var exp = 0;

  switch (type) {
    case 1:
      amt = toFloat($(selector).find('.sales-amt').text())*_x;
      exp = toFloat($(selector).find('.sales-exp').text())*_x;
      break;
    case 2: exp = toFloat($(selector).find('.misc-exp').text())*_x; break;
  }

  var fup_amt = toFloat($('.fup-amt').text()) + amt;
  var fup_exp = toFloat($('.fup-exp').text()) + exp;
  var fup_bal = fup_amt - fup_exp;

  $('.fup-amt').text(commafy(fup_amt));
  $('.fup-exp').text(commafy(fup_exp));
  $('.fup-bal').text(commafy(fup_bal));

  if (_x > 0) {
    $(selector).find('input').removeAttr('disabled');
    $(selector).addClass('info');
  }
  else {
    $(selector).find('input').attr('disabled', '');
    $(selector).removeClass('info');
  }

  compute();
  $('#modal_form').modal('hide');
}

function compute() {
  $('.form-msg').addClass('hide');
  $('input[name=submit]').addClass('disabled').attr('disabled', '');

  var fup_amt = toFloat($('.fup-amt').text());
  var fup_exp = toFloat($('.fup-exp').text());
  var fup_bal = toFloat($('.fup-bal').text());
  var tot_amt = toFloat($('.tot-amt').text());
  var tot_exp = toFloat($('.tot-exp').text());
  var tot_bal = toFloat($('.tot-bal').text());

  var rem_amt = tot_amt - fup_amt;
  var rem_exp = tot_exp - fup_exp;

  if (fup_bal == 0) {
    $('.form-msg.msg1').removeClass('hide');
  }
  else if (fup_bal < 0) {
    $('.form-msg.msg2').removeClass('hide');
  }
  else if (rem_amt < rem_exp) {
    $('.form-msg.msg3').removeClass('hide');
  }
  else {
    $('.form-msg.msg4').removeClass('hide');
    $('input[name=submit]').removeClass('disabled').removeAttr('disabled');
  }
}

$(function(){
  $(document).ready(function(){
    compute();
  });
});
</script>
