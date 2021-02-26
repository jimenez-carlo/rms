<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Add New Batch</div>
      </div>
      <div class="block-content collapse in">

        <form class="form-horizontal" method="post" enctype="multipart/form-data" id='batchForm'>
          <fieldset class="span5">
            <div class="control-group">
              <div class="control-label">Region</div>
              <div class="controls">
                <?php print $region; ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Company</div>
              <div class="controls">
                <?php print form_dropdown('company', $company, set_value('company'),array('id' => 'company')); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Payment Reference #</div>
              <div class="controls">
                <?php print form_input('reference', set_value('reference')); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Date</div>
              <div class="controls">
                <?php print form_input('ref_date', set_value('ref_date'), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Amount</div>
              <div class="controls">
                <?php print form_input('amount', set_value('amount')); ?>
              </div>
            </div>

            <div class="form-actions">
              <input type="submit" name="save" id="save" value="Save" class="btn btn-success"  onclick="return validation(this);">
              <label id="LabelSave" style="visibility:hidden;">Please wait...</label>
              <!-- <img src="img/loader.gif"> -->
              <!-- <button onclick="move()">Click Me</button>  -->
              <!-- <div id="myProgress" style="visibility:hidden;">
  <div id="myBar"></div>
</div> -->

            </div>
          </fieldset>

          <div class="span6">
            <!-- <div class="control-group">
              <div class="control-label">Screenshot</div>
              <div class="controls">
                <input type="file" name="screenshot" class="input-file uniform_on">
                <br><b>Required file format: PDF</b>
                <br><b>File must not exceed 1MB</b>
              </div>
            </div> -->

            <hr>

            <div class="control-group">
              <div class="control-label">Uploaded Batch</div>
              <div class="controls">
                <input id="upload" type="file" name="batch" class="input-file uniform_on" class="input-file uniform_on">
                <br><b>Required file format: CSV</b>
              </div>
            </div>
          </div>
        </form>

			</div>
		</div>
	</div>
</div>
<script>

function validation(a){
	var messages = "";
	job = confirm('Please make sure all information are correct before proceeding. Continue?');
	if(job != true){
	return false;
	}
	//var checkfile = (document.getElementById('upload').length == undefined);
	//alert(document.getElementById('upload').innerHTML);
	//if(checkfile = true){
	//messages = messages + ("\nPlease upload a file");
	//}
	if(document.getElementById('company').value == 0){
	messages = messages + ("Please specify the company");
	}
	if(messages != ""){
	alert(messages);
	return false;
	}
  a.style['display']='none';
  document.getElementById("LabelSave").style['visibility'] = 'visible';
  // document.getElementById("myProgress").style['visibility'] = 'visible';
  // move();

}
function move() {
  var elem = document.getElementById("myBar");   
  var width = 1;
  var id = setInterval(frame, 10);
  function frame() {
    if (width >= 100) {
      clearInterval(id);
    } else {
      width++; 
      elem.style.width = width + '%'; 
    }
  }
}

</script> 
<style>
#myProgress {
  width: 100%;
  background-color: #ddd;
}

#myBar {
  width: 1%;
  height: 30px;
  background-color: #4CAF50;
}
</style>