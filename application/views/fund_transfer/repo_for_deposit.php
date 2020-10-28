<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Process Transfer</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <table class="table">
            <thead>
              <tr>
                <th><p></p></th>
                <th><p>Reference #</p></th>
                <th><p>Document #</p></th>
                <th><p>Entry Date</p></th>
                <th style="text-align:right;padding-right:10px;"><p>Amount</p></th>
                <th><p>Region</p></th>
                <th><p>Debit Memo #</p></th>
                <th><p>Date Processed</p></th>
                <th><p>Date Deposited</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $i => $row)
              {
                print '<tr>';
                print '<td>'.form_checkbox('deposit_funds['.$i.'][repo_batch_id]', $row->repo_batch_id).'</td>';
                print '<td>'.$row->reference.'</td>';
                print '<td>'.$row->doc_no.'</td>';
                print '<td>'.$row->date_created.'</td>';
                print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
                print '<td>'.$row->region.'</td>';
                print '<td>'.form_input('deposit_funds['.$i.'][debit_memo]', set_value('deposit_funds['.$i.'][debit_memo]'), array('disabled' => '')).'</td>';
                print '<td>'.form_input('deposit_funds['.$i.'][date_processed]', set_value('deposit_funds['.$i.'][date_processed]'), array('class' => 'datepicker', 'disabled' => '', 'autocomplete' => 'off')).'</td>';
                print '<td>'.form_input('deposit_funds['.$i.'][date_deposited]', set_value('deposit_funds['.$i.'][date_deposited]'), array('class' => 'datepicker', 'disabled' => '', 'autocomplete' => 'off')).'</td>';
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
                  </tr>';
              }
              ?>
            </tbody>
          </table>

          <div class="form-actions">
            <?php print form_submit('submit', 'Save changes', array('class' => 'btn btn-success', 'onclick' => 'return confirm("Please make sure all information are correct before proceeding. Continue?")', 'disabled' => '')); ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Transfer Fund</h3>
      </div>
      <div class="modal-body form">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <div class="form-body">
          <!-- see process_transfer.php -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save_process()" class="btn btn-success">Save Transfer</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
    function enable_save() {
      if ($(':checked').length) {
        $('input[type=submit]').removeAttr('disabled');
      }
      else {
        $('input[type=submit]').attr('disabled', '');
      }
    }

    $('input[type=checkbox]').click(function(){
      if ($(this).is(':checked')) {
        $(this).closest('tr').find('input[type=text]').removeAttr('disabled');
      }
      else {
        $(this).closest('tr').find('input[type=text]').val('').attr('disabled', '');
      }
      enable_save();
    }).each(function(){
      if ($(this).is(':checked')) {
        $(this).closest('tr').find('input[type=text]').removeAttr('disabled');
      }
      else {
        $(this).closest('tr').find('input[type=text]').val('').attr('disabled', '');
      }
    });
    enable_save();
  });
});
</script>
