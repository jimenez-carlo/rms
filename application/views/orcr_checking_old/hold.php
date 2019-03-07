<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">

    <!-- Sales -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">For Checking</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0px;">
          <table class="table" style="margin:0px;">
            <thead>
              <tr>
                <th><p>Topsheet</p></th>
                <th><p>Branch</p></th>
                <th><p>Date Sold</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Customer Code</p></th>
                <th><p>Engine #</p></th>
                <th><p>AR #</p></th>
                <th><p>Reason</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($sales as $row)
              {
                $key = '['.$row->sid.']';

                print '<tr>';
                print '<td>'.$row->trans_no.'</td>';
                print '<td>'.$row->branch->b_code.' '.$row->branch->name.'</td>';
                print '<td>'.$row->date_sold.'</td>';
                print '<td>'.$row->customer->first_name.' '.$row->customer->middle_name.' '.$row->customer->last_name.'</td>';
                print '<td>'.$row->customer->cust_code.'</td>';
                print '<td>'.$row->engine->engine_no.'</td>';
                print '<td>'.$row->ar_no.'</td>';

                print '<td>';
                foreach ($row->reason as $reason)
                {
                  print '<div style="float: left; font-style: italic; border: 1px solid red; padding: 2px 5px; margin: 2px;">';
                  switch ($reason->reason)
                  {
                    case 1: print 'Wrong Attachments'; break;
                    case 2: print 'Wrong Registration'; break;
                    case 3: print 'Wrong Tip'; break;
                    case 4: print 'Wrong CR #'; break;
                    case 5: print 'Wrong MVF #'; break;
                    case 6: print 'Wrong Plate #'; break;
                    case 7: print 'Others'; break;
                  }
                  print '</div> ';
                }
                print'</td>';

                print '<td><a href="hold_view/'.$row->sid.'" class="btn btn-success">View</a></td>';
                //print '<td><a class="btn btn-success" onclick="remarks('.$row->tsid.', 1)">View remarks</a> <input type="submit" value="Unhold" name="unhold['.$row->sid.']" class="btn btn-success"></td>';
                print '</tr>';
              }

              if (empty($row))
              {
                print '<tr><td colspan=20>No result.</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </form>
			</div>
		</div>

    <!-- Misc 
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Miscellaneous</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th><p>Transaction #</p></th>
              <th><p>Meal</p></th>
              <th><p>Photocopy</p></th>
              <th><p>Transportation</p></th>
              <th><p>Others</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach($misc as $row)
            {
              print '<tr>';
              print '<td>'.$row->trans_no.'</td>';
              print '<td>'.$row->misc->meal.'</td>';
              print '<td>'.$row->misc->photocopy.'</td>';
              print '<td>'.$row->misc->transportation.'</td>';
              print '<td>'.$row->misc->others.'</td>';
              print '<td><button class="btn btn-success" onclick="remarks('.$row->tid.', 2)">View remarks</button></td>';
              print '</tr>';
            }

            if (empty($misc))
            {
              print '<tr><td colspan=20>No result.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
    -->

  </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">Remarks</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>

      <?php
      foreach($sales as $rec)
      {
        print '<div class="sales-remarks tsid-'.$rec->tsid.' hide">';
        foreach ($rec->remarks as $row)
        {
          print '
          <div>
            <p>'.$row->remarks.'</p>
            <p><i>by '.$row->remarks_name.' ('.$row->remarks_user.') on '.$row->remarks_date.'</i></p>
          </div>';
        }
        print '</div>';
      }

      foreach($misc as $rec)
      {
        print '<div class="misc-remarks tid-'.$rec->tid.' hide">';
        foreach ($rec->misc->remarks as $row)
        {
          print '
          <div>
            <p>'.$row->remarks.'</p>
            <p><i>by '.$row->remarks_name.' ('.$row->remarks_user.') on '.$row->remarks_date.'</i></p>
          </div>';
        }
        print '</div>';
      }
      ?>
      <hr>
      <form action="#" id="form" class="form-horizontal" method="post">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">New Remarks</label>
            <div class="col-md-9">
              <textarea name="remarks" class="form-control" style="width:300px;height:120px;"></textarea>
            </div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save_remarks()" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->