<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Inventory</div>
    </div>
    <table class="table">
      <thead>
        <tr>
          <td>Rerfo#</td>
          <td>Status</td>
          <td></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rerfos AS $rerfo): ?>
        <tr>
          <td><?php echo $rerfo['rerfo_number']; ?></td>
          <td><?php echo $rerfo['status']; ?></td>
          <td>
            <?php
              echo '<a class="btn btn-primary" href="'.base_url('repo/rerfo_view/'.$rerfo['repo_rerfo_id']).'">View</a>';
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- <pre id="result"></pre> -->
  </div>
</div>
