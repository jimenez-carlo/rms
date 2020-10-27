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
   //alert(JSON.stringify(checkbox_value));
   $.ajax({
    url:"<?php// echo base_url(); ?>plate/approve_all",
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
      var hays = $('input').val(plateid);
      //alert(plateid);
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
   formGroup.append('<div class="controls"><input name="plateid" class="form-control" id="md_plateid" value="'+<?php //  if ($_SESSION['pid']=='108'){ ?>columnValues[1]<?php }//else{?> columnValues[0] <?php } ?>+'" style="visibility: hidden;"/></div>'); 
   formGroup.append('<div class="control-label">Plate #</div>');
   formGroup.append('<div class="controls"><input name="plateno" class="form-control" id="md_plateno" value="'+<?php //  if ($_SESSION['pid']=='108'){ ?>columnValues[6]<?php }//else{?> columnValues[5] <?php } ?>+'" /></div>'); 
   modalForm.append(formGroup);
   modalBody.append(modalForm);
   $('.modal-body').html(modalBody);
});
function receivePlateno(plateid) {
    var foo = $('#form_plate').append(
       $('<input>').attr({
         name: 'plateid',
        value: plateid
        }),
     $('<input>').attr({
         name: 'plateid',
        value: plateid
        }),  
    );
    
    foo.submit();
}

function receivePlatenoDate(plateid) {
    var test = document.getElementById("date-received").value;
    if (test == ''){
      alert('Please input date.');
    }
    else{
    var foo = $('#form_plate').append(
       $('<input>').attr({
         name: 'plateid',
        value: plateid
        }),
     $('<input>').attr({
         name: 'test',
        value: test
        }),  
    );
    
    foo.submit();
 }
}

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