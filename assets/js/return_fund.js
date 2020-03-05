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

$('input[type=checkbox]').on('click', function(){
  $.ajax({
    url: BASE_URL+'return_fund'
  });
});

