<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Plate Transmittal</div>

      </div>
         <div class="block-content collapse in">
<?php echo ($this->session->flashdata('alert')) ? $this->session->flashdata('alert') : ''; ?>
<br/>
            <form method="post" class="form-horizontal" action="" target="">
               <?php print form_hidden('plate_id', 0); ?>

            <fieldset>
            <div class="control-group span5">
              <div class="control-label">Registration Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-3 days'))), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <?php
            if ($_SESSION['position']=='108' || $_SESSION['position']=='109' || $_SESSION['position']=='156'){
                     $branches = array('_any' => '- Any -') + $branches;
                     echo '<div class="control-group span5">';
                     echo form_label('Branch', 'branch', array('class' => 'control-label', 'id'=>'bsource'));
                     echo '<div class="controls">';
                     $js = 'id="shirts" onChange="some_function();"';
                     echo form_dropdown('branch', $branches, set_value('branch'), $js);
                     echo '</div></div>';
            }
                     $status = array(0 => '- Any -', 1 => 'For HO Validation', 2 => 'In-Transit', 3 => 'Branch Received', 4=>'Received by Customer');
                     echo '<div class="control-group span5">';
                     echo form_label('Status', 'status', array('class' => 'control-label'));
                     echo '<div class="controls">';
                     echo form_dropdown('status', $status, set_value('status'));
                     echo '</div></div>';
            ?>

            <div class="form-actions span12">
               <input type="submit" name="search" value="Search" class="btn btn-success">

            </form>

            <br><br><br>

          </div>

        <form class="form-horizontal" method="post" action="view">

          <table class="table">

            <thead>
              <th><p>Plate Transaction #</p></th>
              <th><p>Branches #</p></th>
              <th><p># of Units</p></th>
              <th><p># of for Approval</p></th>
              <th><p># of In Transit</p></th>
              <th><p># of Received by Branch</p></th>
              <th><p># of Received by Customer</p></th>
              <th hidden></th>
              <th hidden></th>
              <th><p>Action</p></th>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                // $branch = $row->branchname;
                print '<tr>';
                if ($_SESSION['position']=='108' || $_SESSION['position']=='109' || $_SESSION['position']=='156'){
                print '<td>'.$row->plate_trans_no.'</td>';
                print '<td>'.$row->branchname.'</td>';
                print '<td>'.$row->total.'</td>';
                print '<td>'.$row->forApproval.'</td>';
                print '<td>'.$row->pending.'</td>';
                print '<td>'.$row->received.'</td>';
                print '<td>'.$row->receivedcust.'</td>';
                print '<td hidden>'.form_input('vstatus', set_value('status')).'</td>';
                print '<td hidden>'.form_input('vdate['.$row->plate_id.']', $row->date_encoded).'</td>';
                print '<td>'.form_submit('view_tr['.$row->plate_id.']', 'View', array('class' => 'btn btn-success')).''.form_submit('view_te['.$row->plate_id.']', 'Print', array('class' => 'btn btn-success')).'</td>';
                }
                else if ($_SESSION['branch_id'] == $row->bid){
                   print '<td>P-'.$row->bcode.'-'.date("ymd", strtotime($row->date_encoded)).'</td>';
                print '<td hidden></td>';
                print '<td>'.$row->branchname.'</td>';
                print '<td>'.$row->total.'</td>';
                print '<td>'.$row->forApproval.'</td>';
                print '<td>'.$row->pending.'</td>';
                print '<td>'.$row->received.'</td>';
                print '<td>'.$row->receivedcust.'</td>';
                 print '<td hidden>'.form_input('vstatus', set_value('status')).'</td>';
                print '<td hidden>'.form_input('vdate['.$row->plate_id.']', $row->date_encoded).'</td>';
                print '<td>'.form_submit('view_tr['.$row->plate_id.']', 'View', array('class' => 'btn btn-success')).''.form_submit('view_te['.$row->plate_id.']', 'Print', array('class' => 'btn btn-success')).'</td>';
              }
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
    $.fn.dataTableExt.sErrMode = 'throw';

      $("table").dataTable({
         "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
         "sPaginationType": "bootstrap",
         "oLanguage": {
         "sLengthMenu": "_MENU_ records per page"
      },
      "iDisplayLength": 5,
      "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
      });
   });
});
</script>
