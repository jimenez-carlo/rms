<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Dashboard</div>
      </div>
      <div class="block-content collapse in">
        <div class="span4">
          <div id="ca_chart" style="height: 250px;"></div>
        </div>

        <div class="span4">
          <div id="ts_chart" style="height: 250px;"></div>
        </div>

        <div class="span4">
          <div id="sap_chart" style="height: 250px;"></div>
        </div>

        <table id="tbl_chart" class="table">
          <thead>
            <tr>
              <th><p></p></th>
              <th><p>Total</p></th>
              <th><p>Pending</p></th>
              <th><p>Done</p></th>
              <th><p>Rate</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row) {
              //OLD CODE
              //$rate = round(($row->done / $row->total) * 100, 2);

              $rate = ($row->done != 0 || $row->total != 0 )
                ? round(($row->done / $row->total) * 100, 2)
                : 0;

              print '<tr>';
              print '<td>'.$row->label.'</td>';
              print '<td>'.$row->total.'</td>';
              print '<td>'.$row->pending.'</td>';
              print '<td>'.$row->done.'</td>';
              print '<td>'.$rate.'%</td>';
              print '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="vendors/raphael-min.js"></script>
<script src="vendors/morris/morris.min.js"></script>

<script type="text/javascript">
  $(function() {
    var ca_chart = [];
    var ca_color = [];
    var ca_total = parseFloat($('#tbl_chart tbody tr:eq(0) td:eq(1)').text().replace(/,/g, ''));
    var ca_pending = parseFloat($('#tbl_chart tbody tr:eq(0) td:eq(2)').text().replace(/,/g, ''));
    var ca_done = parseFloat($('#tbl_chart tbody tr:eq(0) td:eq(3)').text().replace(/,/g, ''));

    if (ca_pending > 0) {
      ca_chart.push({label: 'Pending', value: ca_pending});
      ca_color.push('#A11717');
    }
    if (ca_done > 0) {
      ca_chart.push({label: 'Done', value: ca_done});
      ca_color.push('#3DA117');
    }

    Morris.Donut({
      element: 'ca_chart',
      data: ca_chart,
      colors: ca_color,
      formatter: function (value) { return value + ' (' + (value / ca_total * 100).toFixed(2) + '%)'; },
    });


    var ts_chart = [];
    var ts_color = [];
    var ts_total = parseFloat($('#tbl_chart tbody tr:eq(1) td:eq(1)').text().replace(/,/g, ''));
    var ts_pending = parseFloat($('#tbl_chart tbody tr:eq(1) td:eq(2)').text().replace(/,/g, ''));
    var ts_done = parseFloat($('#tbl_chart tbody tr:eq(1) td:eq(3)').text().replace(/,/g, ''));

    if (ts_pending > 0) {
      ts_chart.push({label: 'Pending', value: ts_pending});
      ts_color.push('#A11717');
    }
    if (ts_done > 0) {
      ts_chart.push({label: 'Done', value: ts_done});
      ts_color.push('#3DA117');
    }

    Morris.Donut({
      element: 'ts_chart',
      data: ts_chart,
      colors: ts_color,
      formatter: function (value) { return value + ' (' + (value / ts_total * 100).toFixed(2) + '%)'; },
    });


    var sap_chart = [];
    var sap_color = [];
    var sap_total = parseFloat($('#tbl_chart tbody tr:eq(2) td:eq(1)').text().replace(/,/g, ''));
    var sap_pending = parseFloat($('#tbl_chart tbody tr:eq(2) td:eq(2)').text().replace(/,/g, ''));
    var sap_done = parseFloat($('#tbl_chart tbody tr:eq(2) td:eq(3)').text().replace(/,/g, ''));

    if (sap_pending > 0) {
      sap_chart.push({label: 'Pending', value: sap_pending});
      sap_color.push('#A11717');
    }
    if (sap_done > 0) {
      sap_chart.push({label: 'Done', value: sap_done});
      sap_color.push('#3DA117');
    }

    Morris.Donut({
      element: 'sap_chart',
      data: sap_chart,
      colors: sap_color,
      formatter: function (value) { return value + ' (' + (value / sap_total * 100).toFixed(2) + '%)'; },
    });
  });
</script>
