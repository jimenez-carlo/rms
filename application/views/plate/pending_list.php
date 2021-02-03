<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
        <div class="row-fluid">
                <div class="block">
                        <div class="navbar navbar-inner block-header">
                                <div class="pull-left">Pending List</div>
                        </div>
                        <div class="block-content collapse in">

                                <form class="form-horizontal" method="post">
                                        <?php print form_hidden('sid', 0); ?>

                                        <fieldset>
                                                <?php
                                                        if (isset($branches))
                                                        {
                                                                $branches = array('0' => '- Any -') + $branches;
                                                                echo '<div class="control-group span5">';
                                                                echo form_label('Branch', 'branch', array('class' => 'control-label'));
                                                                echo '<div class="controls">';
                                                                echo form_dropdown('branch', $branches, set_value('branch', $branch_def));
                                                                echo '</div></div>';

                                                                echo '<div class="control-group span5">';
                                                                echo form_label('Customer Name', 'name', array('class' => 'control-label'));
                                                                echo '<div class="controls">';
                                                                echo form_input('name', set_value('name'));
                                                                echo '</div></div>';

                                                                echo '<div class="control-group span5">';
                                                                echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
                                                                echo '<div class="controls">';
                                                                echo form_input('engine_no', set_value('engine_no'));
                                                                echo '</div></div>';

                                                        }
                                                        else
                                                        {
                                                                echo '<div class="control-group span5">';
                                                                echo form_label('Customer Name', 'name', array('class' => 'control-label'));
                                                                echo '<div class="controls">';
                                                                echo form_input('name', set_value('name'));
                                                                echo '</div></div>';

                                                                echo '<div class="control-group span5">';
                                                                echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
                                                                echo '<div class="controls">';
                                                                echo form_input('engine_no', set_value('engine_no'));
                                                                echo '</div></div>';

                                                        }
                                                ?>
                                                <div class="form-actions span12">
                                                        <input type="submit" class="btn btn-success" value="Search" name="submit">
                                                </div>
                                        </fieldset>

                                        <hr>
                                        <table class="table" style="margin:0">
                                                <thead>
                                                        <tr>
                                                                <th hidden></th>
                                                                <th>Branch</th>
                                                                <th>Engine #</th>
                                                                <th>Customer Name</th>
                                                                <th>MV File</th>
                                                                <th>Status</th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <?php
                                                        foreach ($table as $sales)
                                                        {

                                print '<tr>';
                                                                print '<td hidden>'.$sales->ssid.'</td>';
                                                                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                                                                print '<td>'.$sales->engine_no.'</td>';
                                                                print '<td>'.$sales->customer_name.'</td>';
                                                                print '<td>'.$sales->mvff_no.'</td>';
                                                                print '<td>'.$sales->status_name.'</td>';
                                                                print '</tr>';
                                                        }

                                                        if (empty($table))
                                                        {
                                                                print '<tr>';
                                                                print '<td>No result.</td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
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
<!-- Modal -->
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


        });
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
   formGroup.append('<div class="controls"><input name="plateid" class="form-control" id="md_sid" value="'+columnValues[0]+'" style="visibility: hidden;"/></div>');
   formGroup.append('<div align="center"><b>Branch</b>: '+columnValues[1]+'</div><br>');
   formGroup.append('<div align="center"><b>Engine #</b>: '+columnValues[2]+'</div><br>');
   formGroup.append('<div align="center"><b>Customer Name</b>: '+columnValues[3]+'</div><br>');
   formGroup.append('<div class="control-label"><b>Plate #</b></div>');
   formGroup.append('<div class="controls"><input name="plateno" class="form-control" id="md_plateno" value="" /></div>');
   modalForm.append(formGroup);
   modalBody.append(modalForm);
   $('.modal-body').html(modalBody);
});

$("#save").on("click", function(e) {
      e.preventDefault();
      var action=confirm('Are you sure you want to save?');
      if(action){
      $('#form_plateno').submit();
   }
});
</script>
