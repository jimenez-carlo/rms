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
              foreach ($transmittal->sales as $sales)
              {
                $key = '['.$sales->sid.']';
                $sales->status = ($sales->status == 1) ? 1 : 2;

                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->date_sold.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.$sales->registration_type.'</td>';
                print '<td>'.$sales->transmittal_date.'</td>';
                ?>

                <td>
                  <span>
                    <?php
                      print form_radio('status'.$key, 2, 
                      (set_value('status'.$key, $sales->status) == 2));
                    ?> Pending at LTO
                  </span><br>
                  <span>
                    <?php
                      print form_radio('status'.$key, 1, 
                      (set_value('status'.$key, $sales->status) == 1));
                    ?> Rejected
                  </span>
                </td>

                <?php
                print '<td>'.form_dropdown('lto_reason'.$key, $reasons, set_value('lto_reason'.$key, $sales->lto_reason)).'</td>';
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
      if ($(this).val() == 1) {
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