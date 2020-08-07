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
    <table class="table">
      <thead>
        <tr>
          <td>Customer</td>
          <td>Engine #</td>
          <td>MV File</td>
          <td>Branch</td>
          <td>Expiration Date</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach($repo_sales AS $repo_sale): ?>
        <tr>
          <td><?php echo $repo_sale['first_name']; ?></td>
          <td><?php echo $repo_sale['engine_no']; ?></td>
          <td><?php echo $repo_sale['mvf_no']; ?></td>
          <td><?php echo $repo_sale['bcode'].' '.$repo_sale['bname']; ?></td>
          <td></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- <pre id="result"></pre> -->
  </div>
</div>
