<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">

  <?php if (isset($summary)) { ?>
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="pull-left">Update</div>
        </div>
        <div class="block-content collapse in">

        <!-- Summary Form -->
        
        <form method="post" class="form-horizontal">
          <table class="table">
            <thead>
              <tr>
                <th style="width:10%">Engine #</th>
                <th>Branch</th>
                <th>Customer Name</th>
                <th>Date Sold</th>
                <th style="width:15%">Result</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($summary->sales as $row)
              {
                print "<tr>";
                print "<td>".$row->engine->engine_no."</td>";

                if (isset($row->sid))
                {
                  print '<td>'.$row->branch.'</td>';
                  print '<td>'.$row->customer->first_name
                    .' '.$row->customer->middle_name
                    .' '.$row->customer->last_name.'</td>';
                  print '<td>'.$row->date_sold.'</td>';

                  if ($row->status == "New")
                  {
                    ?>
                    <td>
                      <input type="hidden" name="sid[]" value="<?php print $row->sid; ?>">
                      <label>Tag as</label>
                      <div>
                        <label><input type="radio" name="is_rejected[<?php print $row->sid; ?>]" value="0"
                          <?php if (set_value('is_rejected['.$row->sid.']', 0) == 0) print 'checked'; ?> > Pending at LTO</label>
                      </div>
                      <div>
                        <label><input type="radio" name="is_rejected[<?php print $row->sid; ?>]" value="1"
                          <?php if (set_value('is_rejected['.$row->sid.']', 0) == 1) print 'checked'; ?> > Rejected</label>
                      </div>
                      <div class="reason hide">
                        <select name="reason[<?php print $row->sid; ?>]">
                          <option value="Closed Item">Closed Item</option>
                          <option value="COC Does Not Exist">COC Does Not Exist</option>
                          <option value="COC Failed">COC Failed</option>
                          <option value="Expired Insurance">Expired Insurance</option>
                          <option value="Unreadable SI">Unreadable SI</option>
                          <option value="No Date on SI">No Date on SI</option>
                          <option value="Affidavit of Change Body Type">Affidavit of Change Body Type</option>
                          <option value="No TIN #">No TIN #</option>
                          <option value="Wrong CSR Attached">Wrong CSR Attached</option>
                          <option value="No Sales Report">No Sales Report</option>
                          <option value="Expired Accre">Expired Accre</option>
                          <option value="Need Affidavit of Lost Docs">Need Affidavit of Lost Docs</option>
                          <option value="Lost Docs">Lost Docs</option>
                          <option value="DIY Reject">DIY Reject</option>
                        </select>
                      </div>
                    </td>
                    <!--td>
                      <textarea name="reason[<?php print $row->sid; ?>]" readonly><?php print $row->remarks; ?></textarea>
                    </td-->
                    <?php
                  }
                  else
                  {
                    if ($row->status == "LTO Pending")
                    {
                      print '<td><p style="color: green">'.$row->status.'</p></td>';
                      print '<td></td>';
                    }
                    else
                    {
                      print '<td><p style="color: red">'.$row->status.'</p></td>';
                      print '<td></td>';
                    }
                  }
                }
                else
                {
                  print '<td colspan=5>No Match</td>';
                }

                print "</tr>";
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total number of records</th>
                <th><?php print $summary->count; ?></th>
                <th colspan=4></th>
              </tr>
              <tr>
                <th>Total number of records without match</th>
                <th><?php print $summary->no_match; ?></th>
                <th colspan=4></th>
              </tr>
              <tr>
                <th>Total number of records to be updated</th>
                <th><?php print $summary->update; ?></th>
                <th colspan=4></th>
              </tr>
            </tfoot>
          </table>

          <?php if ($summary->update > 0) { ?>
          <div class="form-actions">
            <input type="submit" name="submit" class="btn btn-success" value="Submit">
          </div>
          <?php } ?>
        </form>
      </div>
    </div>
        <?php } ?>



    <!-- block -->
    <div class="block hide">
        <div class="navbar navbar-inner block-header">
            <div class="pull-left">Update</div>
        </div>
        <div class="block-content collapse in">

        <!-- Upload Form -->
        <form method="post" enctype="multipart/form-data" class="form-horizontal" action="rrt/upload">
            <div class="control-group">
                <div class="control-label">
                  Upload DIY
                </div>
                <div class="controls">
                  <input type="file" name="upfile" class="input-file uniform_on">
                </div>
            </div>
            <div class="form-actions">
              <input type="submit" name="submit" class="btn btn-success" value="Upload">
            </div>
        </form>

        <!-- Summary Form -->
        <?php if (isset($summary)) { ?>
        <form method="post" class="form-horizontal">
          <table class="table">
            <thead>
              <tr>
                <th style="width:10%">Engine #</th>
                <th>Branch</th>
                <th>Customer Name</th>
                <th>Date Sold</th>
                <th style="width:15%">Result</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($summary->sales as $row)
              {
                print "<tr>";
                print "<td>".$row->engine->engine_no."</td>";

                if (isset($row->sid))
                {
                  print '<td>'.$row->branch->b_code.' '.$row->branch->name.'</td>';
                  print '<td>'.$row->customer->first_name
                    .' '.$row->customer->middle_name
                    .' '.$row->customer->last_name.'</td>';
                  print '<td>'.$row->date_sold.'</td>';

                  if ($row->status == "New")
                  {
                    ?>
                    <td>
                      <input type="hidden" name="sid[]" value="<?php print $row->sid; ?>">
                      <label>Tag as</label>
                      <div>
                        <label><input type="radio" name="is_rejected[<?php print $row->sid; ?>]" value="0"
                          <?php if (set_value('is_rejected['.$row->sid.']', 0) == 0) print 'checked'; ?> > Pending at LTO</label>
                      </div>
                      <div>
                        <label><input type="radio" name="is_rejected[<?php print $row->sid; ?>]" value="1"
                          <?php if (set_value('is_rejected['.$row->sid.']', 0) == 1) print 'checked'; ?> > Rejected</label>
                      </div>
                      <div class="reason hide">
                        <select name="reason[<?php print $row->sid; ?>]">
                          <option value="Closed Item">Closed Item</option>
                          <option value="COC Does Not Exist">COC Does Not Exist</option>
                          <option value="COC Failed">COC Failed</option>
                          <option value="Expired Insurance">Expired Insurance</option>
                          <option value="Unreadable SI">Unreadable SI</option>
                          <option value="No Date on SI">No Date on SI</option>
                          <option value="Affidavit of Change Body Type">Affidavit of Change Body Type</option>
                          <option value="No TIN #">No TIN #</option>
                          <option value="Wrong CSR Attached">Wrong CSR Attached</option>
                          <option value="No Sales Report">No Sales Report</option>
                          <option value="Expired Accre">Expired Accre</option>
                          <option value="Need Affidavit of Lost Docs">Need Affidavit of Lost Docs</option>
                          <option value="Lost Docs">Lost Docs</option>
                          <option value="DIY Reject">DIY Reject</option>
                        </select>
                      </div>
                    </td>
                    <!--td>
                      <textarea name="reason[<?php print $row->sid; ?>]" readonly><?php print $row->remarks; ?></textarea>
                    </td-->
                    <?php
                  }
  								else
  								{
  									if ($row->status == "LTO Pending")
                    {
                      print '<td><p style="color: green">'.$row->status.'</p></td>';
                      print '<td></td>';
                    }
                    else
                    {
                      print '<td><p style="color: red">'.$row->status.'</p></td>';
                      print '<td></td>';
                    }
  								}
                }
                else
                {
                  print '<td colspan=5>No Match</td>';
                }

                print "</tr>";
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total number of records</th>
                <th><?php print $summary->count; ?></th>
                <th colspan=4></th>
              </tr>
              <tr>
                <th>Total number of records without match</th>
                <th><?php print $summary->no_match; ?></th>
                <th colspan=4></th>
              </tr>
              <tr>
                <th>Total number of records to be updated</th>
                <th><?php print $summary->update; ?></th>
                <th colspan=4></th>
              </tr>
            </tfoot>
          </table>

          <?php if ($summary->update > 0) { ?>
          <div class="form-actions">
            <input type="submit" name="submit" class="btn btn-success" value="Submit">
          </div>
          <?php } ?>
        </form>
        <?php } ?>
      </div>
    </div>

    <!-- Diy List -->
    <?php if (isset($rejected)) { ?>
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">LTO</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0">
          <table class="table tbl-diy" style="margin:0">
            <thead>
              <tr>
                <th>Branch</th>
                <th>Date</th>
                <th>Customer Type</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($diy as $row)
              {
                print "<tr>";
                print "<td>".$row->branch."</td>";
                print "<td>".$row->tr_date."</td>";
                print "<td>".$row->cust_type."</td>";
                print "<td><a href='rrt/lto/".$row->bid."/".$row->tr_date."/".$row->type."' class='btn btn-success'>UPDATE</a></td>";
                print "</tr>";
              }

              if (empty($diy))
              {
                print '<tr><td>No rejected.</td><td></td><td></td></tr>';
              }
              ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>
    <?php } ?>

    <!-- Reject List -->
    <?php if (isset($rejected)) { ?>
    <div class="block hide">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Rejected</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0">
          <table class="table tbl-reject" style="margin:0">
            <thead>
              <tr>
                <th style="width:10%">Engine #</th>
                <th>Branch</th>
                <th>Customer Name</th>
                <th>Date Sold</th>
                <th>Reason for Rejection</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($rejected as $row)
              {
                print "<tr>";
                print "<td>".$row->engine->engine_no."</td>";
                print '<td>'.$row->branch->b_code.' '.$row->branch->name.'</td>';
                print '<td>'.$row->customer->first_name
                  .' '.$row->customer->middle_name
                  .' '.$row->customer->last_name.'</td>';
                print '<td>'.$row->date_sold.'</td>';
                print '<td>'.$row->remarks.'</td>';
                print '<td>'.form_submit('pending['.$row->sid.']', 'Pending at LTO', array('class' => 'btn btn-success')).'</td>';
                print "</tr>";
              }

              if (empty($rejected))
              {
                print '<tr><td>No rejected.</td><td></td><td></td><td></td><td></td><td></td></tr>';
              }
              ?>
            </tbody>
          </table>
        </form>
			</div>
		</div>
    <?php } ?>

	</div>
 </div>
