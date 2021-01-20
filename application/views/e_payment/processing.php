<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Processing E-Payment</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <div class="form-actions">
            <input type="submit" class="btn btn-success" value="Save" name="save" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
          </div>
          <table class="table">
            <thead>
              <tr>
                <th><p>Date</p></th>
                <th><p>Payment Reference #</p></th>
                <th><p>Region</p></th>
                <th><p>Amount</p></th>
                <th><p>Addt'l Amount</p></th>
                <th><p>Document #</p></th>
                <th><p>Debit Memo #</p></th>
                <th><p>Update Amount</p></th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach ($table as $row)
            {
                print '<tr>';
                print '<td>'.$row->ref_date.'</td>';
                print '<td><a id="ref-'.$row->epid.'" href="'.base_url().'electronic_payment/view/'.$row->epid.'" target="_blank">'.$row->reference.'</a></td>';
                print '<td>'.$region[$row->region].' '.$company[$row->company].'</td>';
                print '<td>'.$row->amount.'</td>';
                print '<td>'.$row->addtl_amt.'</td>';
                print '<td>'.$row->doc_no.'</td>';
                print '<td>'.form_input('dm_no['.$row->epid.']', set_value('dm_no['.$row->epid.']')).'</td>';
                print '<td>'.form_button([
                  'name'=>'update_amount', 'value'=>$row->epid,
                  'type'=>'button','class'=>'update-amount btn btn-primary',
                  'content' => 'Update'
                ]).'</td>';
                print '</tr>';
            }

            if (empty($table))
            {
                print '<tr>';
                print '<td>No result.</td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
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

<div id="update-amount" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="modal-title"></h3>
  </div>
  <div class="modal-body">
    <?php echo form_open(base_url('electronic_payment/add_amt'), ['class'=>'form-inline']); ?>
    <div class="control-group row">
      <div class="control-label offset1">
        <?php echo form_label('Input additional payment:','add-amt'); ?>
      </div>
      <div class="controls offset1">
        <?php echo form_input(['id'=>'add-amt', 'type'=>'text', 'value'=>'']); ?>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button id="save-add-amt" class="btn btn-primary" type="submit">Save</button>
  </div>
</div>

<script>
  $('.update-amount').on('click', function(e) {
    e.preventDefault();
    var epid = $(this).val();
    $('#modal-title').empty().append('Reference# '+$('#ref-'+epid).text());
    $('#add-amt').attr('name', 'add_amt['+epid+']').val('');
    $('#update-amount').modal('show');
  });

  $('#save-add-amt').on('click', function(e) {
    var confirmation = confirm('Are you sure?');
    if (confirmation ) {
      $(this).submit();
    } else {
      e.preventDefault();
    }
  });
</script>
