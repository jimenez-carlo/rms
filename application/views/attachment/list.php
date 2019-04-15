<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Pending Attachment</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0" action="../attachment">
          <?php print form_hidden('sid', 0); ?>

          <table class="table" style="margin:0">
            <thead>
              <tr>
                <th><p>Branch</p></th>
                <th><p>Date Sold</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Engine #</p></th>
                <th><p>CR #</p></th>
                <th><p>MV File #</p></th>
                <th><p>Plate #</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $sales)
              {
                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->date_sold.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->cr_no.'</td>';
                print '<td>'.$sales->mvf_no.'</td>';
                print '<td>'.$sales->plate_no.'</td>';
                print '<td><a class="btn btn-success" onclick="upload('.$sales->sid.')">Upload</a></td>';
                print '</tr>';
              }

              if (empty($table))
              {
                print '<tr>';
                print '<td>No result.</td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
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
function upload(sid) {
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