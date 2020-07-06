<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">LTO Pending</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0">
          <table class="table" style="margin:0">
            <thead>
              <tr>
                <th><p>Transmittal #</p></th>
                <th><p>Transmittal Date</p></th>
                <th><p>Company</p></th>
                <th><p># of Units</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $company = array(1 => 'MNC', 6 => 'MTI', 3 => 'HPTI', 8 => 'MDI');
              foreach ($table as $row)
              {
                print "<tr>";
                print "<td>".$row->code."</td>";
                print "<td>".$row->date."</td>";
                print "<td>".$company[$row->company]."</td>";
                print "<td>".$row->sales."</td>";
                print "<td>".form_submit('view['.$row->ltid.']', 'Update', array('class' => 'btn btn-success'))."</td>";
                print "</tr>";
              }

              if (empty($table))
              {
                print '<tr>
                  <td>No result.</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>';
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
