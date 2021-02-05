<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">LTO Pending</div>
      </div>
      <div class="block-content collapse in">
        <div class="row">
          <div class="span6 offset1">
            <input id="save" type="button" name="submit" class="btn btn-success" value="Submit">
          </div>
          <div class="span5">
            <fieldset style="padding:4.9px 10.5px 8.75px 10.5px;border:solid 1px black;">
              <legend style="width:auto;font-size:14px;margin:0;line-height:normal;border:0;">Apply To All</legend>
              <?php echo form_radio(['id' =>'no-si-all', 'class'=>'apply-all', 'name'=>'apply-all', 'value'=>'.no-si']); ?>
              <label class="radio" for="no-si-all">
                Unsubmitted SI
              </label>
              <?php echo form_radio(['id' =>'epp-all', 'class'=>'apply-all', 'name'=>'apply-all', 'value'=>'.epp']); ?>
              <label class="radio" for="epp-all">
                Pending at LTO EPP
              </label>
              <?php echo form_radio(['id' =>'cash-all', 'class'=>'apply-all', 'name'=>'apply-all', 'value'=>'.cash']); ?>
              <label class="radio" for="cash-all">
                Pending at LTO CASH
              </label>
              <?php echo form_radio(['id' =>'reject-all', 'class'=>'apply-all', 'name'=>'apply-all', 'value'=>'.reject']); ?>
              <label class="radio" for="reject-all">
                Rejected
              </label>
            </fieldset>
          </div>
        </div>
        <form method="post" class="form-horizontal" onsubmit="return confirm('Items tagged as Pending at LTO will proceed to the next process. Continue?');">
          <?php print form_hidden('ltid', $transmittal->ltid); ?>
          <table class="table">
            <thead>
              <tr>
                <th><p>#</p></th>
                <th><p>Branch</p></th>
                <th style="width: 8%"><p>Date Sold</p></th>
                <th><p>Engine #</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Registration Type</p></th>
                <th><p>Transmittal Date</p></th>
                <th style="width: 12%"><p>Status</p></th>
                <th style="width: 20%"><p>Reason for rejection</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 1;
              foreach ($transmittal->sales AS $key => $sales)
              {
                $default_no_si  = ($sales->lto_reason == "0") ? true : false;
                $default_reject = ($sales->lto_reason != "0") ? true : false;
                $sales->status = ($sales->status == 1) ? 1 : 2;
                $error = (form_error('sales['.$key.'][lto_reason]')) ? ' class="warning"' : '';
                print '<tr'.$error.'>';
                print '<td>'.$count.'</td>';
                $count++;
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->date_sold.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->customer_name.'</td>';
                print '<td>'.$sales->registration_type.'</td>';
                print '<td>'.$sales->transmittal_date.'</td>';
              ?>
                <td>
                  <?php echo form_hidden('sales['.$key.'][sid]', $sales->sid); ?>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'NO_SI',
                        set_radio('sales['.$key.'][action]', 'NO_SI', $default_no_si),
                        ['class'=>'no-si']
                      ); ?> Unsubmitted SI
                  </label>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'EPP',
                         set_radio('sales['.$key.'][action]', 'EPP', false),
                        ['class'=>'epp']
                      );
                    ?> Pending at LTO EPP
                  </label><br>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'CASH',
                        set_radio('sales['.$key.'][action]', 'CASH', false),
                        ['class'=>'cash']
                      );
                    ?> Pending at LTO CASH
                  </label><br>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'REJECT',
                        set_radio('sales['.$key.'][action]', 'REJECT', $default_reject),
                        ['class'=>'reject']
                      );
                    ?> Rejected
                  </label>
                </td>
              <?php
                print '<td>'.form_dropdown('sales['.$key.'][lto_reason]', $reasons, set_value('sales['.$key.'][lto_reason]', $sales->lto_reason)).'</td>';
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
$(function(){
  $(document).ready(function(){
    $("input[type=radio]").change(function(){
      if ($(this).val() == 'REJECT') {
        $(this).closest("tr").find("select").select2("enable", true);
      }
      else {
        $(this).closest("tr").find("select").select2("disable", true);
        $(this).closest("tr").find("select").select2("val", "0");
      }
    });
    $(":checked").change();
  });
});

$(".apply-all").on("click", function() {
  var radioClass = $(this).val();
  $(radioClass).prop("checked", true);
  $("select").select2("val", "0");
  reasonEnDis = (radioClass == '.reject') ? "enable" : "disable";
  $("select").select2(reasonEnDis, true);
});

$("#save").on("click", function() {
  $("form").submit();
});
</script>
