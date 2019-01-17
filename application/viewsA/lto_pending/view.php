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
        <form method="post" class="form-horizontal" onsubmit="return confirm('Items tagged as Pending at LTO will proceed to the next process. Continue?');">
          <fieldset>
            <div class="control-group span5">
              <div class="control-label">Transmittal Date</div>
              <div class="controls" style="padding-top:5px;"><?php print $transmittal->date; ?></div>
            </div>
            <div class="control-group span5">
              <div class="control-label">Branch</div>
              <div class="controls" style="padding-top:5px;"><?php print $transmittal->branch->b_code.' '.$transmittal->branch->name; ?></div>
            </div>
            <div class="control-group span5">
              <div class="control-label">Customer Type</div>
              <div class="controls" style="padding-top:5px;"><?php print $transmittal->cust_type; ?></div>
            </div>
            <div class="control-group span5">
              <div class="control-label"><input type="submit" name="submit" class="btn btn-success" value="Submit"></div>
            </div>
          </fieldset>

          <table class="table">
            <thead>
              <tr>
                <th><p>Date Sold</p></th>
                <th><p>Engine #</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Registration Type</p></th>
                <th><p>Status</p></th>
                <th style="width: 20%"><p>Reason</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($transmittal->sales as $row)
              {
                $key = '['.$row->sid.']';
                $row->status = 2;

                print '<tr>';
                print '<td>'.$row->date_sold.'</td>';
                print '<td>'.$row->engine_no.'</td>';
                print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
                print '<td>'.$row->registration_type.'</td>';
                ?>

                <td>
                  Tag as
                  <span style="display: inline-block; margin-left: 1em">
                    <?php
                      print form_radio('status'.$key, 2, 
                      (set_value('status'.$key, $row->status) == 2));
                    ?> Pending at LTO
                  </span>
                  <span style="display: inline-block; margin-left: 1em">
                    <?php
                      print form_radio('status'.$key, 1, 
                      (set_value('status'.$key, $row->status) == 1));
                    ?> Rejected
                    </span>
                </td>

                <?php
                print '<td>'.form_dropdown('lto_reason'.$key, $reasons, set_value('lto_reason'.$key, $row->lto_reason)).'</td>';
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
