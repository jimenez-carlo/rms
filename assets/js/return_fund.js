var return_fund_table = $("#return-fund-table").dataTable({
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

$('#rf-disapprove').on('click', function(){
  $(this).attr('disabled', true);
  $('.return_fund_disapprove').show();
});


$('#return-fund-save-disapprove').on('click', function(e){
  e.preventDefault();
  var confirm = ('This action cannot be undone: Disapprove. Continue?');
  if (confirm) {
    $('#return-disapprove-form').submit();
  }
});

$('#input-amount').on('keyup', function(e) {
  $("input[name='amount']").val($(this).val())
});

$('#save-correct-amount').on('click', function(e){
  e.preventDefault();
  $("#form-correct-amount").submit();
});
