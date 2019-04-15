<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Attachment</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" enctype="multipart/form-data" class="form-horizontal" 
          <?php if (isset($sales)) print '"'; ?> >

          <!-- Sales Form -->
          <div class="span5 sales-form">
            <?php
            if (isset($sales))
            {
              print form_hidden('sid', $sales->sid);

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
              print '<div class="control-label">Tip</div>';
              print '<div class="controls">'.form_input('tip', set_value('tip', $sales->tip)).'</div>';
              print '</div>';

              print '<div class="control-group">';
              print '<div class="control-label">Registration</div>';
              print '<div class="controls">'.form_input('registration', set_value('registration', $sales->registration)).'</div>';
              print '</div>';

              print '<div class="control-group">';
              print '<div class="control-label">OR Date</div>';
              print '<div class="controls">'.form_input('cr_date', set_value('cr_date', substr($sales->cr_date, 0, 10)), array('class' => 'datepicker')).'</div>';
              print '</div>';

              print '<div class="control-group">';
              print '<div class="control-label">CR #</div>';
              print '<div class="controls">'.form_input('cr_no', set_value('cr_no', $sales->cr_no)).'</div>';
              print '</div>';

              print '<div class="control-group">';
              print '<div class="control-label">MV File #</div>';
              print '<div class="controls">'.form_input('mvf_no', set_value('mvf_no', $sales->mvf_no)).'</div>';
              print '</div>';

              print '<div class="control-group">';
              print '<div class="control-label">Plate #</div>';
              print '<div class="controls">'.form_input('plate_no', $sales->plate_no).'</div>';
              print '</div>';

              print '<div class="form-actions">';
              print form_submit('submit', 'Save', array('class' => 'btn btn-success', 'onclick' => "return confirm('Please make sure all information are correct before proceeding. Continue?')"));
              print '</div>';
            }
            else
            {
              print '<div class="control-group">';
              print '<div class="control-label">Engine #</div>';
              print '<div class="controls">';
              print form_input('engine_no', set_value('engine_no'));
              print form_submit('search', 'Search', array('class' => 'btn btn-success'));
              print '</div>';
              print '</div>';
            }
            ?>
          </div>

          <!-- Attachments -->
          <div class="span7 upload-form">
            <div class="attachments">
              <?php
              if (!empty($sales->files))
              {
                foreach ($sales->files as $file)
                {
                  $path = '/rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;
                  print form_hidden('files[]', $file);
                  print '<div class="attachment" style="position:relative">';
                  print '<img src="'.$path.'" style="margin:1em; border:solid">';
                  print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 1em; right: 0">X</a>';
                  print '</div>';
                }
              }

              $temp = set_value('temp', $temp);
              if (!empty($temp))
              {
                foreach ($temp as $file)
                {
                  print '<div class="attachment temp" style="position:relative">';
                  print form_hidden('temp[]', $file);

                  $path = '/rms_dir/temp/'.$file;
                  print '<img src="'.$path.'" style="margin:1em; border:solid">';

                  print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 1em; right: 0">X</a>';
                  print '</div>';
                }
              }
              ?>
            </div>

            <!-- Upload Form -->
            <div class="control-group" style="margin-top: 10px;">
              <div class="control-label">
                Upload File
              </div>
              <div class="controls">
                <input type="file" name="scanFiles[]" class="input-file uniform_on" id="scanFiles" multiple>
                <br>
                <b>Required file format: jpeg, jpg</b>
                <br><b>You can only upload upto 1MB</b>
              </div>
            </div>
            <div class="form-actions">
              <input type="submit" name="upload" value="Upload" class="btn btn-success">
              <!-- <a class="btn btn-success" onclick="upload()">Upload</a> -->
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
var filenames = new Array(), paths = new Array(), attachments = '';

function upload()
{
  $(".messages").remove();
  
  $.ajax({
    url : "../registration/upload",
    type: "POST",
    data: new FormData($('form')[0]),
    dataType: "JSON",
    contentType: false,
    processData: false,
    success: function(data)
    {
      if(data.status)
      {
        $(".attachments").html("");
        $(".attachments").html(data.content);

        $(".attachment-block").addClass("span9");
        $(".sales-block").removeClass("hide");
      }
      else 
      {
        $("body").prepend(data.message);
      }    
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function unlink(filename)
{
  $.ajax({
    url : "../registration/delete/",
    type: "POST",
    data: {"filename": filename},
    dataType: "JSON",
  });
}

$(function(){
  $(".uniform_on").uniform();

  // show on post
  if ($(".attachment").length > 0)
  {
    $(".attachment-block").addClass("span9");
    $(".sales-block").removeClass("hide");
  }
  
  $("#save").on("click", function() {
     return confirm('Are you sure you want to save?');
  });

  $(document).on('click', '.attachment a', function(){
    var filename = $(this).closest(".attachment").find("input").val();
    $(this).closest(".attachment").html('<span style="color:red">This file will be deleted upon saving. Reload the page to undo.</span>');
  });

  $(document).on('click', '.attachment.temp a', function(){
    var filename = $(this).closest(".attachment").find("input").val();
    unlink(filename);
    $(this).closest(".attachment").remove();

    if ($(".attachment").length == 0)
    {
      $(".attachment-block").removeClass("span9");
      $(".sales-block").addClass("hide");
    }
  });

  var offset = 90;
  $(document).on("scroll", function(){
    if ($(this).scrollTop() > offset) {
      $(".sales-block").attr("style", "position:fixed; top:4.5%; right:0");
    }
    else {
      $(".sales-block").removeAttr("style");
    }
  }).scroll();
});
</script>