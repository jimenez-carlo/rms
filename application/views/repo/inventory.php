<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <!-- <strong>Not fund!</strong> --> <p id='error-msg'></p>
    </div>
    <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Inventory</div>
    </div>
    <?php echo $inventory_table; ?>
  </div>
</div>
