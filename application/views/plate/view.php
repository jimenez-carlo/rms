<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
    <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
      </div>
      <div class="block-content collapse in">
            <form id="form_plate" class="form-horizontal" method="post" action="view">
                        <div style="float:left">
                        <a href="<?php echo base_url('plate/transmittal'); ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-log-out">Back</span></a>

                   <?php  if ($_SESSION['position']=='108'){ ?>

            <!--button type="button" name="delete_all" id="delete_all" class="btn btn-primary btn-sm">Approve Checked</button-->
            <input type="submit"  class="btn btn-primary btn-sm" value="Approve Checked" name="approve">
            <br/><br/>

             <?php } ?>
          <table class="table">

             <!--span style="color:white;">---</span-->
          </div>


            <thead>
              <?php  if ($_SESSION['position']=='108'){ ?>
              <th width = "1%"></th>
              <?php } ?>
              <th hidden><p>pid</p></th>
              <th><p>Plate Transaction #</p></th>
              <th><p>Branch</p></th>
              <th><p>Customer Name</p></th>
              <th><p>Engine #</p></th>
              <th><p>Plate #</p></th>
              <th><p>MV File</p></th>
              <th><p>Status</p></th>
              <th><p>Date Created</p></th>
              <th><p>Received By Branch</p></th>
              <th><p>Received By Customer</p></th>
              <th><p>Action</p></th>
            </thead>
            <tbody>
              <?php
//  echo json_encode($table);
           //   echo $_SESSION['branch'];
              foreach ($table as $plate)
              {
             //   echo $plate->bcode;
                print '<tr>';
                                                                if ($_SESSION['position']=='108'){

                           if ($plate->status=="For Validation"){
                        print '<td><input type="checkbox" name="checkbox['.$plate->plate_id.']" class="delete_checkbox" value="'.$plate->plate_id.'" /></td>';

                     }
                     else{
                       print '<td></td>';
                     }
                  }
                print '<td class="td_bname" hidden>'.$plate->plate_id.'</td>';
                print '<td>'.$plate->plate_trans_no.'</td>';
                print '<td>'.$plate->branchname.'</td>';
                print '<td>'.$plate->name.'</td>';
                print '<td>'.$plate->engine_no.'</td>';
                print '<td>'.$plate->plate_number.'</td>';
                print '<td>'.$plate->mvf_no.'</td>';
                print '<td>'.$plate->status.'</td>';
                print '<td>'.$plate->date_encoded.'</td>';
                print '<td>'.$plate->received_dt.'</td>';
               ?>
                <form id="form_plate" class="form-horizontal" method="post" action="view">
          <?php print form_hidden('plate_id', 0); ?>
               <?php
                if ($_SESSION['position']=='108'){
                  if ($plate->status=="For Validation" &&  $plate->date_encoded >= date('Y-m-d', strtotime('-3 day'))){
                    print '<td>'.$plate->received_cust.'</td>';
                    print '<td>
                      <a id="pid" class="btn btn-success" data-toggle="modal" data-target="#myModal'.$plate->plate_id.'">Edit</a>
                      <input type="hidden" name="plate_id" value="'.$plate->plate_id.'">
                      <input type="submit" class="btn btn-success" value="Approve" name="submit['.$plate->plate_id.']">
                      </td>';
                  }
                  else if($plate->status=="For Validation"){
                    print '<td>'.$plate->received_cust.'</td>';
                    print '<td>
                      <a class="btn btn-success" disabled>Edit</a>
                      <input type="hidden" name="plate_id" value="'.$plate->plate_id.'">
                      <input type="submit" class="btn btn-success" value="Approve" name="submit['.$plate->plate_id.']">
                      </td>';
                  }
                  else{
                    print '<td>'.$plate->received_cust.'</td>';
                    print '<td>
                      <a class="btn btn-success" disabled>Edit</a>
                      <a class="btn btn-success" disabled>Approve</a>
                      </td>';
                  }
                }
                else if($_SESSION['position']=='109' || $_SESSION['position']=='156'){
                  if ($plate->status=="For Validation"  &&  $plate->date_encoded >= date('Y-m-d', strtotime('-3 day'))){
                    print '<td>'.$plate->received_cust.'</td>';
                    print '<td>
                      <a id="pid" class="btn btn-success" data-toggle="modal" data-target="#myModal'.$plate->plate_id.'">Edit</a>
                      </td>';
                  }
                  else if ($plate->status=="For Validation"){
                    print '<td>'.$plate->received_cust.'</td>';
                    print '<td>
                      <a class="btn btn-success" disabled>Edit</a>
                      <input type="hidden" name="plate_id" value="'.$plate->plate_id.'">
                      </td>';
                  }
                  else{
                    print '<td>'.$plate->received_cust.'</td>';
                    print '<td>
                      <a class="btn btn-success" disabled>Edit</a>
                      </td>';
                  }
                }
              else{
                if ($_SESSION['branch_code']==$plate->bcode){
                  if ($plate->status_id==2){
                     print '<td>'.$plate->received_cust.'</td>';

                    /*print '<td><a name="pid" class="btn btn-success" onclick="receivePlateno('.$plate->plate_id.')">Receive</a></td>'; */
                          print             '<td><input type="hidden" name="plateid" value="'.$plate->plate_id.'">
                                        <input type="submit" class="btn btn-success" value="Receive" name="submittt['.$plate->plate_id.']"></td>';
                  }
                  elseif($plate->status_id==3){
                      print '<td><input type="date" name="test" id="date-received" value="'.date('Y-m-d').'" min="'.$plate->received_dt.'" max="'.date('Y-m-d').'"></td>';
                   /*  print '<td><a name="pid" class="btn btn-success" onclick="receivePlatenoDate('.$plate->plate_id.')">Receive</a></td>'; */
                    print             '<td><input type="hidden" name="plateid" value="'.$plate->plate_id.'">
                                        <input type="submit" class="btn btn-success" value="Receive" name="submitt['.$plate->plate_id.']"></td>';
                  }
                  else{
                     print '<td>'.$plate->received_cust.'</td>';
                      print '<td><a class="btn btn-success" disabled>Receive</a></td>';
                  }
                }
                else
                {
                   print '<td>'.$plate->received_cust.'</td>';
                  print '<td><a class="btn btn-success" disabled>Receive</a></td>';
                }
              }
                print '</tr>'; ?>
                <div class="modal fade" id="myModal<?= $plate->plate_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content"></div>
   </div>
   <div class="modal-dialog">
      <div class="modal-content"></div>
   </div>
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Edit Plate Number</h4>
         </div>
         <div class="modal-body">
          <div id="modalContent">
            <div class="control-group"></div>
            <div class="controls"><input name="plateid" class="form-control" id="md_plateid" value="<?= $plate->plate_id; ?>" style="visibility: hidden;"></div>
            <div class="control-label">Plate #</div>
            <div class="controls">
           <input name="plateno<?= $plate->plate_id; ?>" class="form-control" id="md_plateno" value="<?= $plate->plate_number; ?>" maxlength="10">
         </div>
         <br/>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-success" value="Save Change" name="edit[<?= $plate->plate_id ?>]"></td>
         </div>
      </div>
   </div>
</div>
             <?php }

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
      $("table").dataTable({
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
