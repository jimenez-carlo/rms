<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="block">
  <div class="navbar navbar-inner block-header">
    <div class="pull-left">RIC Monitoring</div>
  </div>
</div>
<form class="form-horizontal" method="post">
  <fieldset>
    <div class="control-group span6">
      <div class="control-label">Status</div>
      <div class="controls">
        <?php print form_dropdown('status', array_merge(array('any' => '- Any -'), $status), set_value('status')); ?>
      </div>
    </div>
    <div class="control-group span6">
      <div class="control-label">Region</div>
      <div class="controls">
      <?php
        $region_opts = array();
        $default = 'any';
        if($_SESSION['dept_name'] === 'Regional Registration' && in_array($_SESSION['position_name'], ['RRT General Clerk', 'RRT Branch Secretary'])){
          $region_opts['readonly'] = 'true';
          $default = $_SESSION['region_id'];
        }
        print form_dropdown('region', $region, set_value('region', $default), $region_opts);
      ?>
      </div>
    </div>
    <div class="control-group span6">
      <div class="control-label">RIC Reference</div>
      <div class="controls">
        <?php print form_input('reference', set_value('reference', '')); ?>
      </div>
    </div>
    <div class="control-group span6">
      <div class="control-label">Company</div>
      <div class="controls">
        <?php print form_dropdown('company', $company, set_value('company', 'any')); ?>
      </div>
    </div>
    <div class="form-actions span5">
      <input type="submit" class="btn btn-success" value="Search" name="search">
    </div>
  </fieldset>
</form>
<table class="table">
  <thead>
    <tr>
      <th><center>Download</center></th>
      <th>RIC Reference #</th>
      <th>Total Amount</th>
      <th>Document #</th>
      <th>Debit Memo</th>
      <th>Date Deposited</th>
      <th>Region</th>
      <th>Company</th>
      <?php if($showCheckBtn): ?>
      <th>Action</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php if(!empty($batches)): ?>
      <?php foreach($batches AS $key => $batch): ?>
      <?php
        switch ($_SESSION['position_name']) {
          case 'Accounts Payable Clerk':
            $ap_id = ['id'=>$batch['ric_id']];
            $tr_id = [];
            $disDN = (empty($batch['doc_num'])) ? [] : ['disabled'=>""];
            $disDM = ['disabled'=>""];
            $disDD = ['disabled'=>""];
            break;
          case 'Treasury Assistant':
            $ap_id = [];
            $tr_id = ['id'=>$batch['ric_id']];
            $disDN = ['disabled'=>""];
            $disDM = (empty($batch['doc_num']) || !empty($batch['debit_memo']))     ? ['disabled'=>""] : [];
            $disDD = (empty($batch['doc_num']) || !empty($batch['date_deposited'])) ? ['disabled'=>""] : [];
            break;
          default:
            $ap_id = [];
            $tr_id = [];
            $disDN = ['disabled'=>""];
            $disDM = ['disabled'=>""];
            $disDD = ['disabled'=>""];
        }
      ?>
      <tr>
        <td>
          <center>
            <a id="download" href="<?php echo base_url("ric/download/{$batch['ric_id']}"); ?>">
              <span class="icon icon-download" style="font-size:125%;"></span>
            </a>
          </center>
        </td>
        <td><?php echo '<a class="view-ric-customer" href="#" data-value="'.$batch['ric_id'].'">'.$batch['reference_num'].'</a>'; ?></td>
        <td><?php echo $batch['amount']; ?></td>
        <td><?php echo form_input($ap_id + ['name'=>'doc_num', 'value'=>$batch['doc_num']] + $disDN); ?></td>
        <td><?php echo form_input($tr_id + ['name'=>'debit_memo', 'value'=>$batch['debit_memo']] + $disDM); ?></td>
        <td><?php echo form_input(['id'=>'date-deposited-'.$batch['ric_id'], 'class'=>'datepicker', 'name'=>'date_deposited', 'value'=>$batch['date_deposited'], 'autocomplete'=>'off'] + $disDD); ?></td>
        <td><?php echo $batch['region_name']; ?></td>
        <td><?php echo $batch['company']; ?></td>
        <?php if($showCheckBtn): ?>
        <td><?php
          echo form_button([
            'id'=>'ck-btn-'.$batch['ric_id'], 'class'=>'button-save btn btn-success',
            'style'=>'display: inline-block;', 'content'=>'Save',
            'value'=> $batch['ric_id'], 'disabled'=>""
          ]);
        ?></td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td></td>
        <td>No Result Found.</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </td>
    <?php endif; ?>
  </tbody>
</table>

<div id="ric-list" class="modal modal-center hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="modal-title"></h3>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Close</a>
  </div>
</div>

<script>
$('input').on('keyup', function(){
  var id = $(this).attr('id');
  if ($(this).val().length > 4) {
    $('#ck-btn-'+id).removeAttr('disabled');
  } else {
    $('#ck-btn-'+id).prop('disabled', true);
  }
});

$('.button-save').on('click', function(e){
  e.preventDefault();
  var thisBtn = $(this);
  var isConfirm = confirm('Are you sure?');
  if (isConfirm) {
    var id = thisBtn.val();
    var input = $('#'+id);
    var dataToSend = { "ric_id": id }
    <?php
      switch ($_SESSION['position_name']) {
        case 'Treasury Assistant':
          echo "dataToSend.date_deposited = $('input[name=date_deposited]').val();\n";
          echo "dataToSend.debit_memo = input.val();";
          break;
        case 'Accounts Payable Clerk':
          echo 'dataToSend.doc_num = input.val();';
          break;
      }
    ?>

    $.ajax({
      url: "<?php echo base_url('ric/update'); ?>",
      data: dataToSend,
      type: "post",
      beforeSend: function() {
        thisBtn.button('loading');
      },
      success: function(data) {
        var response = JSON.parse(data);
        if (response.success) {
          thisBtn.parent().empty().append('<p class="text-success"><b>Success!!</b></p>');
          if (response.date_deposited) {
           $('#date-deposited-'+id).prop('disabled', true).val(response.date_deposited);
          }
        } else {
          thisBtn.parent().empty().append('<p class="text-error"><b>Error!!</b></p>');
        }
        input.prop('disabled', true);
      }
    });
  }
});

$('.view-ric-customer').on('click', function(e) {
  e.preventDefault();
  var ric_id = $(this).data('value');
  $.ajax({
    url: "<?php echo base_url('ric/list'); ?>",
    data: {"ric_id":ric_id},
    type: "post",
    dataType: "json",
    success: function(data) {
      $('#modal-title').empty().append(data.title);
      $('.modal-body').empty().append(data.table);
      $('#ric-list').modal('show');
    }
  });
});
</script>
