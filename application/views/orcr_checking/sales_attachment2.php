<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

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
  print '<div class="control-label">AR #</div>';
  print '<div class="controls">'.$sales->ar_no.'</div>';
  print '</div>';

  print '<div class="control-group">';
  print '<div class="control-label">Registration Type</div>';
  print '<div class="controls">'.$sales->registration_type.'</div>';
  print '</div>';

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