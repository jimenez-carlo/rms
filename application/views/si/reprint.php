<div class="block">
  <div class="navbar navbar-inner block-header">
    <div class="pull-left">SI Reprint</div>
  </div>
</div>

<div class="container">
  <form name="for-print" method="POST" action="<?php echo base_url('si/print_now'); ?>">
    <div class="row">
      <div class="span12 form-inline">
        <label for="prepared-by">Prepared By :</label>
        <input id="prepared-by" type="text" name="prepared_by" value="" required>
        <label for="approved-by">Approved By :</label>
        <input id="approved-by" type="text" name="approved_by" value="" required>
        <input class="btn btn-primary" type="submit" name="print" value="Print  " onsubmit="return confirm('Are you sure?');" >
      </div>
    </div>
    <br>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>#</th>
          <th>Transmittal#</th>
          <th>Branch Code</th>
          <th>Engine No.</th>
          <th>Customer Name</th>
          <th>Date Sold</th>
        </tr>
      </thead>
      <tbody>
        <?php if (isset ($data) && count($data) > 0): ?>
          <?php $x = 1; foreach($data as $datas) : ?>
            <tr>
              <td><input class="cb-selectall" type="checkbox" name="bobj_sales_ids[<?php echo $datas['bobj_sales_id']; ?>]"></td>
              <td><?php echo $x++; ?></td>
              <td><?php echo $datas['code']; ?></td>
              <td><?php echo $datas['bcode']; ?></td>
              <td><?php echo $datas['si_engin_no']; ?></td>
              <td><?php echo $datas['si_custname']; ?></td>
              <td><?php echo $datas['si_dsold']; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
            <tr>
              <td>No results found.</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>
<script>
    $('#selectAll').click(function(){
      $('.cb-selectall').prop('checked', $(this).is(':checked'));
    })
</script>

