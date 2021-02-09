<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="span4 details">
  <?php
  print '<div class="control-group">';
  print '<div class="control-label" style="padding:0">CA Ref#</div>';
  print '<div class="controls">'.$misc->reference.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label" style="padding:0">OR #</div>';
  print '<div class="controls">'.$misc->or_no.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label" style="padding:0">OR Date</div>';
  print '<div class="controls">'.$misc->or_date.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label" style="padding:0">Type</div>';
  print '<div class="controls" style="padding:0">'.$misc->type.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label" style="padding:0">Amount</div>';
  print '<div class="controls">'.$misc->amount.'</div>';
  print '</div>';

  if ($misc->status !== "5") {
    print '<div class="control-group">';
    print '<div class="control-label" style="padding:0">Remarks</div>';
    print '<div class="controls">'.$misc->remarks.'</div>';
    print '</div>';
  }

  print '<div class="control-group misc-da-remarks hide">';
  print '<div class="control-label">Reason</div>';
  print '<div class="controls">
          <select id="misc_da_reason" name="da_reason" required>
            <option>ADVANCE DATE OF EXPENSE VS CA EXPENSE</option>
            <option>DUPLICATE ATTACHMENT</option>
            <option>EXCEED APPROVED EXPENSE</option>
            <option>EXPIRED OR</option>
            <option>NO COMPANY INFO</option>
            <option>NO OR ATTACHED</option>
            <option>NO VOUCHER FOR ITINERARY</option>
            <option>NOT FOR REGISTRATION EXPENSE</option>
            <option>WRONG AMOUNT</option>
            <option>WRONG ATTACHED OR</option>
            <option>WRONG COMPANY INFO</option>
            <option>WRONG OR#</option>
          </select>
        </div>';
  print '</div>';
  print '<div class="control-group misc-da-remarks hide">';
  print '<div class="control-label">Remarks</div>';
  print '<div class="controls">
          <textarea name="remarks"></textarea>
        </div>';
  print '</div>';

  switch ($misc->status) {
    case '3':
    case '5':
      print '<div class="control-group">';
      print '<div class="control-label" style="padding:0">DA Reason</div>';
      print '<div class="controls">'.$misc->da_reason.'</div>';
      print '</div>';

      print '<div class="control-group">';
      print '<div class="control-label" style="padding:0">Remarks</div>';
      print '<div class="controls">'.$misc->remarks.'</div>';
      print '</div>';
      break;

    case '4':
      break;

    default:
      $da_reason = array(5 => 'Disapproved');
      print '<div id="da_group" class="control-group">';
      print '<div class="control-label"></div>';
      print '<div class="controls">';
      print '<a id="da_misc" class="btn btn-warning trigger">Disapprove</a>';
      print form_dropdown('da_reason', $da_reason, 0, array('data-sid' => $misc->mid, 'class' => 'hide'));
      print '<a class="btn btn-success save hide">Save</a>';
      print '</div></div>';
      break;
  }
  ?>
</div>

<div class="span1"></div>

<div class="span7 attachments">
  <?php
  if (!empty($misc->files)) {
    foreach ($misc->files as $file)
    {
      $path = base_url('rms_dir/misc/'.$misc->mid.'/'.$file);
      print '<img src="'.$path.'" style="margin:1em; border:solid; float:right; width:88%;">';
    }
  }
  else {
      print "No attachments.";
  }
  ?>
</div>

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
    $('.modal-body').on("scroll", function(){
      if ($(this).scrollTop() > 50) {
        $(".details").attr("style", "position:fixed; top:20%;");
        $(".attachments").attr("style", "margin-left:30%");
      }
      else {
        $(".details").removeAttr("style");
        $(".attachments").removeAttr("style");
      }
    });
  });
});

$('#da_misc').on('click', function(){
  $('.misc-da-remarks, .save').removeClass('hide');
  $(this).addClass('hide');
});

$('.save').on('click', function(){
  var misc_da_reason = $('#misc_da_reason').val();
  var remarks = $('textarea[name="remarks"]').val();
  var remarks_length = misc_da_reason.length;
  var mid = '<?php echo $misc->mid; ?>';

  switch (remarks_length) {
    case 0:
      $('.misc-da-remarks').addClass('error');
      $('.required').remove();
      $('<span class="help-inline required">This field is required.</span>').insertAfter('#misc_da_remarks');
      break;

    default:
      $.ajax({
        url : "<?php echo base_url(); ?>disapprove/misc_expense",
        type: "POST",
        data: {
          "mid": mid,
          "da_reason":  misc_da_reason,
          "remarks": remarks
        },
        dataType: "JSON",
        success: function(data)
        {
          // update info
          $('#da_group .control-label').text('Disapproved');
          $("#include_for_upload").prop("disabled", true);
          $("#exclude_for_upload").prop("disabled", true);
        },
      });
      break;
  }
});
</script>
