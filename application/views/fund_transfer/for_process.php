<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Process Transfer</div>
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
                <!-- <th><p>Company</p></th>
                <th><p></p></th> -->
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                print '<tr>';
                print '<td>'.form_checkbox('vid[]', $row->vid, in_array($row->vid, set_value('vid', array()))).'</td>';
                print '<td>'.$row->reference.'</td>';
                print '<td>'.$row->voucher_no.'</td>';
                print '<td>'.$row->date.'</td>';
                print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
                print '<td>'.$row->region.'</td>';
                print '<td>'.form_input('dm_no['.$row->vid.']', set_value('dm_no['.$row->vid.']'), array('disabled' => '')).'</td>';
                print '<td>'.form_input('process_date['.$row->vid.']', set_value('process_date['.$row->vid.']'), array('class' => 'datepicker', 'disabled' => '', 'autocomplete' => 'off')).'</td>';
                // print '<td>'.$row->company.'</td>';
                // print '<td style="text-align:center"><button class="btn btn-success" onclick="process_transfer('.$row->vid.')">Process Transfer</button></td>';
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
var vid;

function process_transfer(_vid)
{
  vid = _vid;

  $.ajax({
    url : "fund_transfer/process_transfer/" + vid,
    type: "POST",
    dataType: "JSON",
    success: function(data)
    {
      $(".error").html("");
      $(".alert-error").addClass("hide");
      $('.form-body').html(data); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function save_process()
{
  if (confirm('Please make sure that all information are correct before proceeding. Continue?'))
  {
    $.ajax({
      url : "fund_transfer/save_process/" + vid,
      type: "POST",
      data: $('#form').serialize(),
      dataType: "JSON",
      success: function(data)
      {
        if(data.status)
        {
          $('#modal_form').modal('hide');
          location.href='fund_transfer';
        }
        else
        {
          $(".alert-error").removeClass("hide");
          $(".error").html("");
          $(".error").append(data.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error get data from ajax');
      }
    });
  }
}

function total()
{
  var total = 0;

  $('#form .amount:checked').each(function(){
    total += parseFloat($(this).val());
  });

  total = parseFloat(total).toFixed(2);
  $('#form input[name=amount]').val(total);

  total = total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  $('#form #total-projected').text(total);
}

function get_offline()
{
  if($('input[name=offline]').is(':checked')){
    $('.control-group.date').removeClass('hide');
  }
  else {
    $('.control-group.date').addClass('hide');

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;
    var yyyy = today.getFullYear();

    if (dd < 10) dd = "0"+dd;
    if (mm < 10) mm = "0"+mm;
    $('input[name=date]').val(yyyy + '-' + mm + '-' + dd);
  }
}

$(function(){
  $(document).ready(function(){
    // $(".table").dataTable({
    //   "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
    //   "sPaginationType": "bootstrap",
    //   "oLanguage": {
    //     "sLengthMenu": "_MENU_ records per page"
    //   },
    //   "bSort": false,
    //   "iDisplayLength": 5,
    //   "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    // });

    // $('.view').click(function(){
    //   $('form').attr('action', 'sales/view');
    // });

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
