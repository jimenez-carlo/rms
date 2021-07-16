<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style>
  #barcodevideo,
  #barcodecanvas,
  #barcodecanvasg {
    height: 400px;
  }

  #barcodecanvasg {
    position: absolute;
    top: 0px;
    left: 0px;
  }

  #result {
    font-family: verdana;
    font-size: 1.5em;
  }

  #barcode {
    position: relative;
  }

  #barcodecanvas {
    display: none;
  }

  .modal {
    position: fixed;
    width: 60%;
    top: 10% !important;
    left: 20%;
    margin-top: auto;
    /* Negative half of height. */
    margin-left: auto;
    /* Negative half of width. */
  }

  .tab-pane {
    border: 1px solid;
    border-color: #ddd #ddd #ddd #ddd;
    padding: 20px;
  }

  .tabs-right>.nav-tabs {
    float: right;
    margin-left: 0px;
  }

  img {
    width: auto;
    height: 250px;
  }

  .block-content {
    margin: 2em;
  }

  .block {
    border: unset;
    /* border-top: 1px solid #f5f5f5; */
  }
</style>


<div class="navbar navbar-inner block-header" style="margin-top: 15px; background:#fff">
  <div class="pull-left">Plate List</div>
</div>

<!-- block -->
<br>

<form id="da_form" method="post" class="form-horizontal"  style="background-color: #fff;">
  <div class="control-group span8" style="padding:20px;">
    <div class="controls">
      <input type="text" id="test_input" name="test">
      <!-- <input type="checkbox" name="">Open window upon scan -->
      <button type="button" class="btn btn-success" id="btn-test">Search</button>
      <!-- <button type="button" class="btn btn-success btn-edit-branch-tip" data-title="Edit Branch Code - ">QR</button>
      <button type="button" class="btn btn-success btn-edit-branch-tip" data-title="Edit Branch Code - ">Bar Code</button> -->
    </div>
  </div>
  <!-- <div id="barcode">
    <video id="barcodevideo" autoplay></video>
    <canvas id="barcodecanvasg"></canvas>
  </div>
  <canvas id="barcodecanvas"></canvas>
  <div id="result"></div> -->
  <table id="data-table" class="table">
    <thead>
      <tr>
        <th>
          <p>Plate Trans. No.</p>
        </th>
        <th>
          <p>Plate No.</p>
        </th>
        <th>
          <p>Branch</p>
        </th>
        <th>
          <p>Company</p>
        </th>
        <th>
          <p>Status</p>
        </th>
        <th>
          <p>Received Date</p>
        </th>
        <th>
          <p>Customer Claimed Date</p>
        </th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($table as $res) {
        print '<tr">';
        print '<td>' . $res['plate_trans_no'] . '</td>';
        print '<td>' . $res['plate_number'] . '</td>';
        print '<td>' . $res['branch'] . '</td>';
        print '<td>' . $res['company_name'] . '</td>';
        print '<td>' . strtoupper($res['status_name']) . '</td>';
        print '<td>' . $res['received_dt'] . '</td>';
        print '<td>' . $res['received_cust'] . '</td>';
        print '<td><button value="' . $res['plate_id'] . '" type="button" class="btn btn-success btn-edit-branch-tip" data-title="Edit Branch Code - ' . $res['plate_id'] . '">Edit</button></td>';
        // print '<td>'.$res['name'].'</td>';
        print '</tr>';
      }

      ?>
    </tbody>
  </table>
</form>
<script>
  //   var sound = new Audio("<?php echo BASE_URL ?>assets/js/barcode.wav");

  // $(document).ready(function() {

  // barcode.config.start = 0.1;
  // barcode.config.end = 0.9;
  // barcode.config.video = '#barcodevideo';
  // barcode.config.canvas = '#barcodecanvas';
  // barcode.config.canvasg = '#barcodecanvasg';
  // barcode.setHandler(function(barcode) {
  //   $('#result').html(barcode);
  // });
  // barcode.init();

  // $('#result').bind('DOMSubtreeModified', function(e) {
  //   sound.play();	
  // });

  // });

  document.getElementById("btn-test").addEventListener("click", function() {
    var btn = this;
    this.disabled = true;
    this.innerText = 'Searching ...';
    var plate_no = document.getElementById('test_input').value;
    if (plate_no == '') {
      error("Plate Number Is Empty!");
      btn.innerText = 'Search';
      btn.disabled = false;
      return false;
    }
    doca('.modal-title').innerText = "Plate No# " +plate_no;
    var params = "action=view_plate&plate_no="+plate_no;
    var view_plate = new XMLHttpRequest();
    view_plate.onload = function() {
      btn.disabled = false;
      btn.innerText = 'Search';
      if (view_plate.response.trim() != '') {
        docid('modal_body').innerHTML = view_plate.response;
        $('#modal-container').modal('toggle');
      }else{
        error("Plate Number <b>"+plate_no.toUpperCase()+"</b> Not Found!");
        return false;
      }
    }
    view_plate.open("POST", BASE_URL + "Request", true);
    view_plate.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    view_plate.send(params);
  });
</script>