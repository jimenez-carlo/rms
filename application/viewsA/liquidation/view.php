<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Liquidation</div>
      </div>
      <div class="block-content collapse in">

        <!-- Search Form -->
      <form class="form-horizontal" enctype="multipart/form-data" method="post" style="margin:10px 0px;">
        <fieldset>
          <div class="control-group span4" style="margin-bottom:0;">
            <div class="control-label">
              Region
            </div>
            <div class="controls">
              <?php
              $options = array(
                1 => 'NCR',
                2 => 'Region 1',
                3 => 'Region 2',
                4 => 'Region 3',
                5 => 'Region 4A',
                6 => 'Region 4B',
                7 => 'Region 5',
                8 => 'Region 6',
                9 => 'Region 7',
                10 => 'Region 8',
              );
              print form_dropdown('region', $options, set_value('region'));
              ?>
            </div>
          </div>
          <div class="control-group span4" style="margin-bottom:0;">
            <div class="control-label">
              Date Transferred  
            </div>
            <div class="controls">
              <input type="text" name="date_transferred" class="datepicker" value="<?php if(isset($_POST['date_transferred'])) print $_POST['date_transferred']; ?>">
            </div>
          </div>
          <div class="span4">
            <input type="submit" value="Search" class="btn btn-success" name="search">
          </div>
        </fieldset>
      </form>

      <?php if(isset($table)) { ?>
        <table class="table table-bordered" style="margin-top:20px;margin-bottom:5px;">
          <thead>
            <tr>
              <th><p>Transferred Date</p></th>
              <th><p>Debit Memo #</p></th>
              <th><p>Transferred Amount</p></th>
              <th><p>Liquidated Amount</p></th>
              <th><p>For Liquidation</p></th>
              <th><p>LTO Pending</p></th>
              <th><p>Pending Amount</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
          <?php
          foreach ($table as $row)
          {
            $balance = $row->amount - ($row->liquidated + $row->for_liquidation + $row->lto_pending);
            print '<tr>';
            print '<td>'.substr($row->date, 0,10).'</td>';
            print '<td>'.$row->dm_no.'</td>';
            print '<td style="text-align:right">'.number_format($row->amount,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($row->liquidated,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($row->for_liquidation,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($row->lto_pending,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($balance,2,'.',',').'</td>';
            print '<td><a class="btn btn-success" href="liquidation/sales/'.$row->ftid.'">Sales Details</a></td>';
            print '</tr>';
          }

          if (empty($table))
          {
            print '<tr>
              <td>No result.</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>';
          }
          ?>
          </tbody>
        </table>
      <?php } ?>

      </div>
    </div>
  </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Balance Details</h3>
      </div>
      <div class="modal-body form">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>

        <form action="#" id="form" class="form-horizontal">
          <div class="form-body">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->