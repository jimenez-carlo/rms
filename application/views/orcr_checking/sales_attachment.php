<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.form-horizontal .controls {
    padding-top: 5px;
}
</style>

<div class="span4 details">
  <?php
  print '<div class="control-group">';
  print '<div class="control-label">Branch</div>';
  print '<div class="controls">'.$sales->bcode.' '.$sales->bname.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Customer</div>';
  print '<div class="controls">'.$sales->first_name.' '.$sales->last_name.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Engine #</div>';
  print '<div class="controls">'.$sales->engine_no.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Registration Type</div>';
  print '<div class="controls">'.$sales->registration_type.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Reference #</div>';
  print '<div class="controls">'.$sales->ar_no.'</div>';
  print '</div>';

  if ($sales->registration_type != 'Free Registration' && $sales->amount > 0) {
  print '<div class="control-group">';
  print '<div class="control-label">Amount Given</div>';
  print '<div class="controls">'.$sales->amount.'</div>';
  print '</div>';
  }

  print '<div class="control-group">';
  print '<div class="control-label">Registration</div>';
  print '<div class="controls">'.$sales->registration.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">CR #</div>';
  print '<div class="controls">'.$sales->cr_no.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">MV File #</div>';
  print '<div class="controls">'.$sales->mvf_no.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Plate #</div>';
  print '<div class="controls">'.$sales->plate_no.'</div>';
  print '</div>';

  // disapprove
  // You can check this array in Disapprove_model
  $da_reason = array(
    1  => 'Wrong Amount',
    2  => 'No (AR/SI) reference',
    3  => 'Invalid (AR/SI) reference',
    4  => 'Unreadable attachment',
    5  => 'Missing OR attachment',
    6  => 'Mismatch Customer Name',
    7  => 'Mismatch Engine #',
    8  => 'Mismatch CR #',
    //9  => 'Wrong Tagging',
    10 => 'Wrong Regn Type',
  );
  if ($sales->da_reason > 0 && $sales->da_reason != 11) {
    print '<div class="control-group">';
    print '<div class="control-label">Disapproved</div>';
    print '<div class="controls">Reason: '.$da_reason[$sales->da_reason].'</div>';
    print '</div>';
  }
  else {
    print '<div id="da_group" class="control-group">';
    print '<div class="control-label"></div>';
    print '<div class="controls">';
    print '<a class="btn btn-success trigger">Disapprove</a>';
    print form_dropdown('da_reason', $da_reason, 0, array('data-sid' => $sales->sid, 'class' => 'hide'));
    print '<a class="btn btn-success save hide">Save</a>';
    print '<p class="hide"></p>';
    print '</div></div>';
  }
  ?>
</div>

<div class="span1"></div>

<div class="span7 attachments">
  <?php
  if (!empty($sales->files)) {
    foreach ($sales->files as $file)
    {
      $path = base_url('rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file);
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
        $(".details").attr("style", "position:fixed; top:20%;");
        $(".attachments").attr("style", "margin-left:30%");

    $('.modal-body').on("scroll", function(){
      if ($(this).scrollTop() > 50) {
        $(".details").attr("style", "position:fixed; top:20%;");
        $(".attachments").attr("style", "margin-left:30%");
      }
      else {
        // $(".details").removeAttr("style");
        // $(".attachments").removeAttr("style");
      }
    });

    // disapprove
    $('#da_group a.trigger').click(function(){
      $('#da_group a.trigger').addClass('hide');

      // trigger to disapprove, show select reason
      $('#da_group .control-label').text('Reason for Disapprove');
      $('#da_group select[name=da_reason]').removeClass('hide');
      $('#da_group hr').removeClass('hide');
      $('#da_group a.save').removeClass('hide');
    });

    $('#da_group a.save').click(function(){
      if (!confirm('Please make sure all information are correct before proceeding. Continue?')) return;

      // trigger to save, get data
      var sid = $('#da_group select[name=da_reason]').attr('data-sid');
      var da_reason = $('#da_group select[name=da_reason]').val();

      // saving...
      $('#da_group a.save').addClass('hide');
      $('#da_group p').text('Saving, please wait...').removeClass('hide');

      $.ajax({
        url : "<?php echo base_url(); ?>disapprove/sales",
        type: "POST",
        data: {'sid' : sid, 'da_reason' : da_reason},
        dataType: "JSON",
        success: function(data)
        {
          // saved, hide select reason
          $('#da_group select[name=da_reason]').addClass('hide');
          $('#da_group hr').addClass('hide');

          // update info
          $('#da_group .control-label').text('Disapproved');
          $('#da_group p').text('Reason: '+data);
          $("#include_for_upload").prop("disabled", true);
          $("#exclude_for_upload").prop("disabled", true);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          // failed, enable resave
          $('#da_group a.save').removeClass('hide')
          $('#da_group p').text('Something went wrong.');
        }
      });
    });
  });
});
</script>
