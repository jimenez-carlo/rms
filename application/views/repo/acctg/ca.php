<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div id="status"></div>
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo CA</div>
      </div>
      <br>
      <div class="container">
          <div class="row">
            <?php echo form_open('repo/ca_template', ["class"=>"span6", "target"=>"_blank"]); ?>
              <div class="control-group">
                <div class="control-label">CA Template</div>
                <div class="controls">
                  <?php echo form_button(["class" => "btn btn-warning", "name"=>"download", "value"=>"true", "content"=>"Download", "type"=>"submit"]); ?>
                </div>
                <div class="help-block" style="margin-top:10px; width:70%; position:relative;">
                  <span class="label label-info">Download CA Template:</span> <br>
                  Template will only generate REPOCA batch without Document #.
                </div>
              </div>
            </form>
            <?php echo form_open('repo/print_ca_topsheet', ["class"=>"span6", "target"=>"_blank"]); ?>
              <div class="control-group">
                <div class="control-label">Date Doc#</div>
                <div class="controls">
                  <?php echo form_input(["class"=>"datepicker", "name"=>"date_doc_no_encoded", "value"=> date('Y-m-d',strtotime( "yesterday" ))]); ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Region</div>
                <div class="controls">
                  <?php echo $input_region; ?>
                </div>
                <div class="controls">
                  <?php echo form_button(["class" => "btn btn-primary", "name"=>"print", "value"=>"true", "content"=>"Print", "type"=>"submit"]); ?>
                </div>
                <div class="help-block" style="margin-top:10px; width:70%; position:relative;">
                  <span class="label label-info">Print Topsheet:</span> <br>
                  You can print only based on the Doc# date encoded.
                </div>
              </div>
            </form>
          </div>
      </div>
      <div class="block-content collapse in">
        <?php echo form_open(); ?>
          <?php echo $for_ca; ?>
      </div>
    </div>
  </div>
</div>

<script>
$('.print').on('click', function(e){
  e.preventDefault();
  var form = $(this).closest('form');
  form.attr('target', '_blank');
  form.attr('action', '<?php echo base_url('repo/print_ca'); ?>');
  form.submit();
});

$('.save').on('click', function(e){
  var confirmed = confirm('Are you sure?');
  if (!confirmed) {
    return false;
  }
  var form = $(this).closest('form');
  form.removeAttr('target');
  form.attr('action', '<?php echo base_url('repo/ca'); ?>');
});

$('.doc-no').on('keyup', function() {
  var id = $(this).attr('id');
  var bool = true;
  if($(this).val().length > 3) {
    bool = false;
  }
  $('#save-'+id).prop('disabled', bool);
});

$('button[name="save"]').on('click', function() {
  var id = $(this).val();
  var doc_no = $('input#'+id).val();
  $('input#'+id).parent().children('span').remove();

  $.ajax({
    url: '<?php echo base_url('repo/ca'); ?>',
    type: 'POST',
    dataType: 'json',
    data: { "save_doc_no": true, "repo_batch_id": id, "doc_no": doc_no },
    complete: function (jqXHR, textStatus) {
    },
    success: function (data, textStatus, jqXHR) {
      $('input#'+id).parent().removeClass('error').addClass('success').append('<span class="help-inline">✔</span>');
      $('#status').empty().append('<div class="alert alert-success"><p>'+data.message+'</p></div>');
    },
    error: function (jqXHR, textStatus, errorThrown) {
      if (jqXHR.status === 400) {
        $('input#'+id).parent().removeClass('success').addClass('error').append('<span class="help-inline">✘</span>');
        $('#status').empty().append('<div class="alert alert-error">'+jqXHR.responseText+'</div>');
      }
    }
  });
})
</script>
