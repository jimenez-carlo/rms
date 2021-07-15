<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <!-- <strong>Not fund!</strong> --> <p id='error-msg'></p>
    </div>
    <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Inventory <button type="button" id="testing_1">btn1</button> <button type="button" id="testing_2">btn2</button> </div>
    </div>
    <?php echo $inventory_table; ?>
  </div>
</div>
<script>





  event_click(testing_2, testingz);

  // document.getElementById("testing_1").addEventListener("click", function () { req_ajax('Request', "", function() { alert("test"); }) });
  // document.getElementById("testing_2").addEventListener("click", function () { req_ajax('Request', "", testingz) });
  
  
  function testingz(){ alert("success 2"); }

  function event_click(id, func){
    // console.log(func);
    var el = document.getElementById(id);
    if (el) {
      el.addEventListener("click", function () { func(); }); 
    }
  }

  function req_ajax(controller, parameters, run_function) {
    console.log("clicked");
      var xhr = create_xhr();
      xhr.onload = function () {
        run_function();
      }
      xhr.open("POST", 'http://172.0.3.32/dev_site/rms/' + controller, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(parameters);
  }
  function create_xhr() {
  return new XMLHttpRequest();
}
</script>