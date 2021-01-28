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
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
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
                         set_radio('sales['.$key.'][action]', 'NO_SI', $default_no_si)
                      ); ?> Unsubmitted SI
                  </label>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'EPP',
                         set_radio('sales['.$key.'][action]', 'EPP', false)
                      );
                    ?> Pending at LTO EPP
                  </label><br>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'CASH',
                        set_radio('sales['.$key.'][action]', 'CASH', false)
                      );
                    ?> Pending at LTO CASH
                  </label><br>
                  <label class="radio">
                    <?php
                      print form_radio(
                        'sales['.$key.'][action]',
                        'REJECT',
                        set_radio('sales['.$key.'][action]', 'REJECT', $default_reject)
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

          <fieldset>
            <div class="control-group span5">
              <div class="control-label">
                <input type="submit" name="submit" class="btn btn-success" value="Submit">
              </div>
            </div>
          </fieldset>
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
</script>
