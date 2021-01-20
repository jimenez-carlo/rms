<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Update CA Reference</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <table id="tbl_exp" class="table">
            <thead>
              <tr>
                <th><p>Reference # (SI/OR)</p></th>
                <th><p>OR Date</p></th>
                <th><p>Amount</p></th>
                <th><p>Type</p></th>
                <th><p>Status</p></th>
                <th><p>CA Reference</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $misc)
              {
                $misc->type = $type[$misc->type];
                $misc->type = ($misc->type == 'Others') ? '(Others) '.$misc->other : $misc->type;
                $misc->status = ($misc->status == 'Rejected') ? 'Rejected due to:<br>'.$misc->da_reason : $misc->status;

                print '<tr>';
                print '<td>'.$misc->or_no.'</td>';
                print '<td>'.substr($misc->or_date, 0, 10).'</td>';
                print '<td>'.$misc->amount.'</td>';
                print '<td>'.$misc->type.'</td>';
                print '<td>'.$misc->status.'</td>';
                print (!empty($misc->ca_ref)) ? '<td>'.$misc->ca_ref.'</td>' : '<td>'.form_submit('update['.$misc->mid.']', 'Update', array('class' => 'btn btn-success')).'</td>';
                print '</tr>';
              }

              if (empty($table))
              {
                print '<tr>
                  <td>No result.</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  </tr>';
              }
              ?>
            </tbody>
          </table>

<!--
          <div class="form-actions">
            <input type="submit" name="save" value="Save Changes" class="btn btn-success">
          </div>
-->
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){
        $('input[name=save]').click(function(){
                if (!confirm('Please make sure all information are correct before proceeding. Continue?')) return false;

                $('input[type=textbox]').each(function(){
                        if (!$(this).val()) $(this).attr('disabled', 'disabled');
                });
        });
});
</script>
