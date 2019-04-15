<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="span4 details">
  <?php
  print '<div class="control-group">';
  print '<div class="control-label">OR #</div>';
  print '<div class="controls">'.$misc->or_no.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">OR Date</div>';
  print '<div class="controls">'.$misc->or_date.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Type</div>';
  print '<div class="controls">'.$misc->type.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Amount</div>';
  print '<div class="controls">'.$misc->amount.'</div>';
  print '</div>';
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
        $(".details").attr("style", "position:fixed; top:20%; left:10%");
        $(".attachments").attr("style", "margin-left:30%");
      }
      else {
        $(".details").removeAttr("style");
        $(".attachments").removeAttr("style");
      }
    });
  });
});
</script>