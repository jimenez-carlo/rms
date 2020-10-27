<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Batch</div>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>Reference#</th>
          <th>Amount</th>
          <th>Document#</th>
          <th>Debit Memo</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($batches AS $batch): ?>
        <tr>
          <td><?php echo $batch['reference']; ?></td>
          <td><?php echo number_format($batch['amount'], 2, '.', ','); ?></td>
          <td><?php echo $batch['doc_no']; ?></td>
          <td><?php echo $batch['debit_memo']; ?></td>
          <td><?php echo $batch['status']; ?></td>
          <td>
            <?php
              echo '<a class="btn btn-primary" href="'.base_url('repo/batch_view/'.$batch['repo_batch_id']).'">View</a>  ';
              echo '<a class="btn btn-success" href="'.base_url('repo/batch_print/'.$batch['repo_batch_id']).'">Print</a>';
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- <pre id="result"></pre> -->
  </div>
</div>
