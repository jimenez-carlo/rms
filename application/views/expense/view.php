<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h3 class="modal-title">Reference # <?php print $misc->or_no; ?></h3>
</div>
<div class="modal-body">

  <form class="form-horizontal modal-form" method="post">
    <?php print form_hidden('mid', $misc->mid); ?>

  	<fieldset class="span4">
  	<?php
          echo '<div class="control-group">';
          echo form_label('OR Date', '', array('class' => 'control-label'));
          echo '<div class="controls text">';
          echo $misc->or_date;
          echo '</div></div>';

          echo '<div class="control-group">';
          echo form_label('Amount', '', array('class' => 'control-label'));
          echo '<div class="controls text">';
          echo $misc->amount;
          echo '</div></div>';

          echo '<div class="control-group">';
          echo form_label('Type', '', array('class' => 'control-label'));
          echo '<div class="controls text">';
          echo $misc->type;
          echo '</div></div>';

          echo '<div class="control-group">';
          echo form_label('CA Reference', '', array('class' => 'control-label'));
          echo '<div class="controls text">';
          echo $misc->ca_ref;
          echo '</div></div>';

          echo '<div class="control-group">';
          echo form_label('Status', '', array('class' => 'control-label'));
          echo '<div class="controls text">';
          echo $misc->status;
          echo '</div></div>';

          echo '<div class="control-group">';
          echo form_label('Remarks', '', array('class' => 'control-label'));
          echo '<div class="controls text">';
          echo '<textarea disabled>'.$misc->remarks.'</textarea>';
          echo '</div></div>';

          echo '<div class="control-group reason hide">';
          echo form_label('Reason for rejection', 'reason', array('class' => 'control-label'));
          echo '<div class="controls">';
          echo '<textarea name="reason" style="width: 80%"></textarea>';
          echo '</div></div>';
        ?>

          <div class="form-actions">
            <?php
              if ($approval) print ' <button type="submit" name="approve" class="btn btn-success approve">Approve</button>';
              if ($reject) print ' <button type="submit" name="reject" class="btn btn-danger reject">Reject</button>';
            ?>
          </div>
  	</fieldset>
  </form>

  <div class="attachments span5">
  	<?php
  	foreach ($misc->files as $key => $file)
  	{
  	  print '<div class="attachment temp" style="position:relative">';

  	  $path = base_url('rms_dir/misc/'.$misc->mid.'/'.$file);
  	  print '<img src="'.$path.'" style="margin:5px; border:solid">';

  	  print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
  	  print '</div>';
  	}
  	?>
  </div>

</div>
<div class="modal-footer">
  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
$(function(){
  $('.approve').click(function(){
    $('#modal_form form').attr('action', 'expense/approve');
    return confirm('The following action cannot be undone: Approve miscellaneous expense. Continue?');
  });

  $('.reject').click(function(){
    if ($('.reason').hasClass('hide')) {
      $('.reason').removeClass('hide');
    }
    else if (!$('.reason textarea').val()) {
      alert('Reason for rejection field is required.');
    }
    else {
      $('#modal_form form').attr('action', 'expense/reject');
      return confirm('Please make sure that all information are correct before proceeding. Continue?');
    }

    return false;
  });
})
</script>
