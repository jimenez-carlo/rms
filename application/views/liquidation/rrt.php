<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
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
            print '<td>'.$row->date.'</td>';
            print '<td>'.$row->dm_no.'</td>';
            print '<td style="text-align:right">'.number_format($row->amount,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($row->liquidated,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($row->for_liquidation,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($row->lto_pending,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($balance,2,'.',',').'</td>';
            print '<td><a class="btn btn-success" href="sales/'.$row->ftid.'">Sales Details</a></td>';
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