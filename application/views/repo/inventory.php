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
          <td>Status</td>
          <td>Date Sold</td>
          <td>Date Registered</td>
          <td>Expiration</td>
          <td></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach($repo_inventory AS $repo): ?>
        <tr>
          <td><?php  echo $repo['last_name'].', '.$repo['first_name']; ?></td>
          <td><?php echo $repo['engine_no']; ?></td>
          <td><?php echo $repo['mvf_no']; ?></td>
          <td><?php echo $repo['repo_status']; ?></td>
          <td><?php echo $repo['date_sold']; ?></td>
          <td><?php echo $repo['date_registered']; ?></td>
          <td><?php echo '<p class="text-'.$repo['status'].'">'.$repo['message'].'</p>'; ?></td>
          <td>
            <?php
              $view_url = '#';
              $view_target = '';
              $view_disabled = 'disabled="disabled"';
              if (!isset($repo['view_disabled'])) {
                $view_url = base_url('repo/view/'.$repo['repo_inventory_id']);
                $view_target='target="_blank"';
                $view_disabled = '';
              }
              echo '<a class="btn btn-primary" href="'.$view_url.'" '.$view_target.'>View</a>';

              $sale_url = '#';
              $sale_disabled = 'disabled="disabled"';
              if (!isset($repo['sale_disabled'])) {
                $sale_url = base_url('repo/sale/'.$repo['repo_inventory_id']);
                $sale_disabled = '';
              }
              echo '<a class="btn btn-warning" style="margin: 0 3px 0 3px;" href="'.$sale_url.'" '.$sale_disabled.'>Sales</a>';

              $register_url = '#';
              $register_disabled = 'disabled="disabled"';
              if (!isset($repo['regn_disabled'])) {
                $register_url = base_url('repo/registration/'.$repo['repo_inventory_id']);
                $register_disabled = '';
              }
              echo '<a class="btn btn-success" href="'.$register_url.'" '.$register_disabled.'>Register</a>';
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- <pre id="result"></pre> -->
  </div>
</div>
