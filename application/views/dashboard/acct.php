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
        <div class="row-fluid">
          <div class="span4">
            <label>Cash Advance</label>
            <div id="ca_chart" style="height: 250px;"></div>
          </div>

          <div class="span4">
            <label>EPAT</label>
            <div id="epat_chart" style="height: 250px;"></div>
          </div>

          <div class="span4">
            <label>For Checking</label>
            <div id="ts_chart" style="height: 250px;"></div>
          </div>
        </div>

        <div class="row-fluid">
          <div class="span4">
            <label>SAP Uploading</label>
            <div id="sap_chart" style="height: 250px;"></div>
          </div>

          <div class="span4">
            <label>Return Fund</label>
            <div id="rf_chart" style="height: 250px;"></div>
          </div>

          <div class="span4">
            <label>Misc Expenses</label>
            <div id="misc_chart" style="height: 250px;"></div>
          </div>
        </div>
        <?php if(isset($table)): ?>
          <?php echo $table; ?>
        <?php endif; ?>
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
      formatter: function (value) { return value.toLocaleString() + ' (' + (value / ca_total * 100).toFixed(2) + '%)'; },
    });

    var epat_chart = [];
    var epat_color = [];
    var epat_total = parseFloat($('#tbl_chart tbody tr:eq(1) td:eq(1)').text().replace(/,/g, ''));
    var epat_pending = parseFloat($('#tbl_chart tbody tr:eq(1) td:eq(2)').text().replace(/,/g, ''));
    var epat_done = parseFloat($('#tbl_chart tbody tr:eq(1) td:eq(3)').text().replace(/,/g, ''));

    if (epat_pending > 0) {
      epat_chart.push({label: 'Pending', value: epat_pending});
      epat_color.push('#A11717');
    }
    if (epat_done > 0) {
      epat_chart.push({label: 'Done', value: epat_done});
      epat_color.push('#3DA117');
    }

    Morris.Donut({
      element: 'epat_chart',
      data: epat_chart,
      colors: epat_color,
      formatter: function (value) { return value.toLocaleString() + ' (' + (value / epat_total * 100).toFixed(2) + '%)'; },
    });

    var ts_chart = [];
    var ts_color = [];
    var ts_total = parseFloat($('#tbl_chart tbody tr:eq(2) td:eq(1)').text().replace(/,/g, ''));
    var ts_pending = parseFloat($('#tbl_chart tbody tr:eq(2) td:eq(2)').text().replace(/,/g, ''));
    var ts_done = parseFloat($('#tbl_chart tbody tr:eq(2) td:eq(3)').text().replace(/,/g, ''));

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
      formatter: function (value) { return value.toLocaleString() + ' (' + (value / ts_total * 100).toFixed(2) + '%)'; },
    });


    var sap_chart = [];
    var sap_color = [];
    var sap_total = parseFloat($('#tbl_chart tbody tr:eq(3) td:eq(1)').text().replace(/,/g, ''));
    var sap_pending = parseFloat($('#tbl_chart tbody tr:eq(3) td:eq(2)').text().replace(/,/g, ''));
    var sap_done = parseFloat($('#tbl_chart tbody tr:eq(3) td:eq(3)').text().replace(/,/g, ''));

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
      formatter: function (value) { return value.toLocaleString() + ' (' + (value / sap_total * 100).toFixed(2) + '%)'; },
    });

    var rf_chart = [];
    var rf_color = [];
    var rf_total = parseFloat($('#tbl_chart tbody tr:eq(4) td:eq(1)').text().replace(/,/g, ''));
    var rf_pending = parseFloat($('#tbl_chart tbody tr:eq(4) td:eq(2)').text().replace(/,/g, ''));
    var rf_done = parseFloat($('#tbl_chart tbody tr:eq(4) td:eq(3)').text().replace(/,/g, ''));

    if (rf_pending > 0) {
      rf_chart.push({label: 'Pending', value: rf_pending});
      rf_color.push('#A11717');
    }
    if (rf_done > 0) {
      rf_chart.push({label: 'Done', value: rf_done});
      rf_color.push('#3DA117');
    }

    Morris.Donut({
      element: 'rf_chart',
      data: rf_chart,
      colors: rf_color,
      formatter: function (value) { return value.toLocaleString() + ' (' + (value / rf_total * 100).toFixed(2) + '%)'; },
    });

    var misc_chart = [];
    var misc_color = [];
    var misc_total = parseFloat($('#tbl_chart tbody tr:eq(5) td:eq(1)').text().replace(/,/g, ''));
    var misc_pending = parseFloat($('#tbl_chart tbody tr:eq(5) td:eq(2)').text().replace(/,/g, ''));
    var misc_done = parseFloat($('#tbl_chart tbody tr:eq(5) td:eq(3)').text().replace(/,/g, ''));

    if (misc_pending > 0) {
      misc_chart.push({label: 'Pending', value: misc_pending});
      misc_color.push('#A11717');
    }
    if (misc_done > 0) {
      misc_chart.push({label: 'Done', value: misc_done});
      misc_color.push('#3DA117');
    }

    Morris.Donut({
      element: 'misc_chart',
      data: misc_chart,
      colors: misc_color,
      formatter: function (value) { return value.toLocaleString() + ' (' + (value / misc_total * 100).toFixed(2) + '%)'; },
    });
  });
</script>
