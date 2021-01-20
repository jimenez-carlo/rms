<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
  .table input {
    width: 100px;
  }
  .table input.numeric {
    width: 75px;
  }
</style>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Registration</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" action="<?php echo base_url(); ?>registration" target="_blank">
          <?php print form_hidden('sid', 0); ?>

          <table class="table">
            <thead>
              <tr>
                <th><p>Branch</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Engine #</p></th>
                <th><p>Sales Type</p></th>
                <th><p>Payment Method</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $sales)
              {
                $key = '['.$sales->sid.']';
                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->payment_method.'</td>';
                print '<td>'.$sales->sales_type.'</td>';
                print '<td><a name="update" class="btn btn-success" onclick="update('.$sales->sid.')">Update</a></td>';

                print '</tr>';
              }
              ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function update(sid) {
  $('input[name=sid]').val(sid);
  $('form').submit();
}

$(function(){
  $(document).ready(function(){
    $(".table").dataTable({
      "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
      "sPaginationType": "bootstrap",
      "oLanguage": {
        "sLengthMenu": "_MENU_ records per page"
      },
      "bSort": false,
      "iDisplayLength": 5,
      "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });
  });
});
</script>
