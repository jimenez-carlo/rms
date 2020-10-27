<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid"> 
   <div class="row-fluid">
      <div class="block">
                <div class="navbar navbar-inner block-header">
            <div class="pull-left">Plate</div>
         </div>
         <div class="block-content collapse in">

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
                     $branches = array('_any' => '- Any -') + $branches;
                     echo '<div class="control-group span5">';
                     echo form_label('Branch', 'branch', array('class' => 'control-label', 'id'=>'bsource'));
                     echo '<div class="controls">';
                     $js = 'id="shirts" onChange="some_function();"';
                     echo form_dropdown('branch', $branches, set_value('branch'), $js);
                     echo '</div></div>';

                     $status = array(0 => '- Any -', 1 => 'For HO Validation', 2 => 'In-Transit', 3 => 'Branch Received', 4=>'Received by Customer');
                     echo '<div class="control-group span5">';
                     echo form_label('Status', 'status', array('class' => 'control-label'));
                     echo '<div class="controls">';
                     echo form_dropdown('status', $status, set_value('status'));
                     echo '</div></div>';

                     // $print_status = array(
                     //    '_any' => '- Any -',
                     //    0 => 'For printing',
                     //    1 => 'Printed',
                     // );
                     // echo '<div class="control-group span4">';
                     // echo form_label('Print Status', 'print', array('class' => 'control-label'));
                     // echo '<div class="controls">';
                     // echo form_dropdown('print', $print_status, set_value('print', 0));
                     // echo '</div></div>';
                  ?>

            <div class="form-actions span12">
               <input type="submit" name="search" value="Search" class="btn btn-success">
            </form>
            </div>



               </fieldset>
        
               <hr>
               <table class="table" style="margin:0">
                  <div style="float:left">
                   <?php  if ($_SESSION['pid']=='108'){ ?>
                   
            <button type="button" name="delete_all" id="delete_all" class="btn btn-primary btn-sm">Approve Checked</button>
          
             <?php } ?>
             <!--span style="color:white;">---</span-->
          </div>
             <?php if ($_SESSION['position'] == 108){ ?>
              
               <form  method="post" action="<?= base_url()?>plate/plate_transmittal">
                <div style="float:right">
                 <?php
                     $branches = array('_any' => '- Any -') + $branches;
                     $js = 'id="shirts" style="width: 240px" onChange="some_function();"';
                     echo form_dropdown('btopsheet', $branches, set_value('branch'), $js);
                    
                     ?>
               <span style="color:white;">---</span><input type="submit" name="search" value="Generate Transmittal" class="btn btn-success">
               </div>
            </form>
                       
         <?php } ?>
              <br><br>
                 <form method="post" class="form-horizontal" action="" target="">
               <?php print form_hidden('plate_id', 0); ?>
                  <thead>
                     <tr>
                        <?php  if ($_SESSION['pid']=='108'){ ?>
                        <th width = "1%"></th>
                    <?php } ?>
                        <th hidden>
                           <p>pid</p>
                        </th>
                        <th>
                           <p>Branch</p>
                        </th>
                        <th>
                           <p>Customer Name</p>
                        </th>
                        <th>
                           <p>Engine #</p>
                        </th>
                        <th>
                           <p>Plate #</p>
                        </th>
                        <th>
                           <p>Status</p>
                        </th>
                        <th>
                           <p>Date Created</p>
                        </th>
                        <th>
                           <p>Received By Branch</p>
                        </th>
                        <th>
                           <p>Received By Customer</p>
                        </th>
                                                <?php if ($_SESSION['pid']=='108'){ ?>
                       <th><p>Action</p></th>
                     <?php   }
                        ?>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                if(!empty($table)){

                     foreach ($table as $key => $rerfo)
                     {
                     /*   $key = '['.$rerfo->plate_id.']';
                        $log = (!empty($rerfo->print_date))
                           ? 'Printed on '.$rerfo->print_date : '<i>For printing</i>';  */

                        print '<tr>';
                                                $key 
                        = '['.$rerfo->plate_id.']';
                        print '<tr>';
                                                if ($_SESSION['pid']=='108'){
                           print '<td hidden></td>';
                           if ($rerfo->status=="For HO Validation"){
                        print '<td><input type="checkbox" class="delete_checkbox" value="'.$rerfo->plate_id.'" /></td>';

                     }
                     else{
                       print '<td></td>';
                     }
                  }
                        print '<td class="td_bname" hidden>'.$rerfo->plate_id.'</td>';
                        print '<td class="td_bname">'.$rerfo->branchname.'</td>';
                        print '<td class="td_name">'.$rerfo->name.'</td>';
                        print '<td class="td_engineno">'.$rerfo->engine_no.'</td>';
                        print '<td class="td_plateno">'.$rerfo->plate_number.'</td>';
                        print '<td class="td_status">'.$rerfo->status.'</td>';
                        print '<td class="td_status">'.$rerfo->date_encoded.'</td>';
                        print '<td class="td_receivedt">'.$rerfo->received_dt.'</td>';
                        print '<td class="td_receivedt">'.$rerfo->received_cust.'</td>';

                       
                                                if ($_SESSION['pid']=='108'){
                           if ($rerfo->status=="For HO Validation"){
                              print '<td>
                                       <a id="pid" class="btn btn-success" data-toggle="modal" data-target="#myModal">Edit</a>
                                       <a name="sid" class="btn btn-success" onclick="approvePlateno('.$rerfo->plate_id.')">Approve</a>
                                    </td>';
                           }
                           else{
                              print '<td>
                                       <a class="btn btn-success" disabled>Edit</a>
                                       <a class="btn btn-success" disabled>Approve</a>
                                    </td>';
                           }
                        }
                        else{
                            print '<td></td>';
                        }
                /* $print_date = (!empty($print_date)) ? substr($rerfo->print_date, 0, 10) : date('Y-m-d');
                if ($rerfo->print == 0 || $print_date == date('Y-m-d')) {
                    print '<input type="submit" name="print'.$key.'" value="Print" class="btn btn-success print">';
                  }
                  else {
                    print '<input type="submit" name="request'.$key.'" value="Request Reprinting" class="btn btn-success request">';
                  }*/
                       
                        print '</tr>';
                     }
                  }

                     if (empty($table))
                     {
                        print '
                           <tr>
                              <td>No result.</td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              ';
                     if ($_SESSION['pid']=='108'){ 
                       print '<td></td><td></td></tr>';
                    }
                    else{
                         print  '</tr>';
                        }
                     }
                     ?>
                  </tbody>
               </table>
            </form>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
         <div class="modal-body"></div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button id="save" type="button" class="btn btn-primary">Save changes</button>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">

   function some_function(){
     // alert($('#shirts').val());
      $('#btopsheet').val($('#shirts').val());
   }

    $('.delete_checkbox').click(function(){
  if($(this).is(':checked'))
  {
   $(this).closest('tr').addClass('removeRow');
  }
  else
  {
   $(this).closest('tr').removeClass('removeRow');
  }
 });

 $('#delete_all').click(function(){
  var checkbox = $('.delete_checkbox:checked');
  if(checkbox.length > 0)
  {
   var checkbox_value = [];
   $(checkbox).each(function(){
    checkbox_value.push($(this).val());
   });
   alert(JSON.stringify(checkbox_value));
   $.ajax({
    url:"<?php echo base_url(); ?>plate/approve_all",
    method:"POST",
    data:{checkbox_value:checkbox_value},
    success:function()
    {
     //$('.removeRow').fadeOut(1500);
     location.reload();
    }
   })
  }
  else
  {
   alert('Select atleast one records');
  }
 });

function approvePlateno(plateid) {
      $('input').val(plateid);
      $('form').submit();
}

$("#save").on("click", function(e) {
      e.preventDefault();
      var action=confirm('Are you sure you want to save?');
      if(action){
      $('#form_plateno').submit();
   }
});

//Edit
$(".btn[data-target='#myModal']").click(function() {
   var columnHeadings = $("thead th").map(function() {
      return $(this).text();
   }).get();
   columnHeadings.pop();
   var columnValues = $(this).parent().siblings().map(function() {
      return $(this).text();
   }).get();
   var modalBody = $('<div id="modalContent"></div>');
   var modalForm = $('<form id="form_plateno" role="form" name="modalForm" class="form-horizontal" method="post"></form>');
   var formGroup = $('<div class="control-group"></div>');
   formGroup.append('<div class="controls"><input name="plateid" class="form-control" id="md_plateid" value="'+columnValues[2]+'" style="visibility: hidden;"/></div>'); 
   formGroup.append('<div class="control-label">Plate #</div>');
   formGroup.append('<div class="controls"><input name="plateno" class="form-control" id="md_plateno" value="'+columnValues[6]+'" /></div>'); 
   modalForm.append(formGroup);
   modalBody.append(modalForm);
   $('.modal-body').html(modalBody);
});

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
         "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
      });

      $('.table').on('click', '.view', function(){
         $('form').attr('action', 'rerfo/view').removeAttr('target');
      });
      $('.table').on('click', '.print', function(){
         $('form').attr('action', 'rerfo/sprint').attr('target', '_blank');
      });
      $('.table').on('click', '.request', function(){
         $('form').attr('action', 'rerfo/request').removeAttr('target');
         return confirm('The following action cannot be undone: Request reprinting of rerfo. Continue?');
      });
   });
});

$(function(){
   $(document).ready(function(){
      $("#tblPlate").dataTable({
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
