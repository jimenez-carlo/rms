<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund Audit Log</div>
      </div>
      <div class="block-content collapse in">
        <table class="table" style="margin:0px;">
          <thead>
            <tr>
              <th><p>Transaction Date</p></th>
              <th><p style="text-align:right;">In Amount</p></th>
              <th><p style="text-align:right;">Out Amount</p></th>
              <th><p>Remarks</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row) 
            {
              print '<tr>';
              print '<td>'.$row->date.'</td>';
              switch ($row->type)
              {
                case 1:
                  print '<td><p style="text-align:right;">'.number_format($row->in_amount, 2, ".", ",").'</p></td>';
                  print '<td><p style="text-align:right;">-</p></td>';
                  print '<td>DEPOSIT</td>';
                  break;
                case 2:
                  print '<td><p style="text-align:right;">-</p></td>';
                  print '<td><p style="text-align:right;">'.number_format($row->out_amount, 2, ".", ",").'</p></td>';
                  print '<td>CASH WITHDRAWAL</td>';
                  break;
                case 3:
                  print '<td><p style="text-align:right;">-</p></td>';
                  print '<td><p style="text-align:right;">'.number_format($row->out_amount, 2, ".", ",").'</p></td>';
                  print '<td>CHECK WITHDRAWAL</td>';
                  break;
                case 4:
                  print '<td><p style="text-align:right;">'.number_format($row->in_amount, 2, ".", ",").'</p></td>';
                  print '<td><p style="text-align:right;">-</p></td>';
                  print '<td>DEPOSIT</td>';
                  break;
              }
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td>No transactions.</td><td></td><td></td><td></td><td></td><td></td></tr>';
            }
            ?>
          </tbody>
        </table>
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
      "bFilter": false,
      "bSort": false,
      "iDisplayLength": 5,
      "aLengthMenu": [[5, 10, 25, 100, -1], [5, 10, 25, 100, "All"]]
    });
  });
});
</script>