<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Repo In</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-search">
          <?php echo form_input(['id' => 'engine_no', 'type' => 'text', 'name' => 'engine_no', 'placeholder' => 'Input Engine#']); ?>
          <button class="btn btn-success" type="submit"> Search </button>
        </form>
        <pre id="result"></pre>
        <table class="table">
          <thead>
            <tr>
              <th>Engine Number</th>
              <th>MV File</th>
              <th>Customer Name</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="tbl_content"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
