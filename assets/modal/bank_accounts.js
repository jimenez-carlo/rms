var fid;

function manage(_fid)
{
  fid = _fid;

  $.ajax({
    url : "bank_accounts/manage/" + fid,
    type: "POST",
    dataType: "JSON",
    success: function(data)
    {
      $(".error").html("");
      $(".alert-error").addClass("hide");
      $('#form .form-body').html(data); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function save()
{
  $.ajax({
    url : "bank_accounts/save/" + fid,
    type: "POST",
    data: $('#form').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form').modal('hide');
        location.reload();
      }
      else 
      {
        $(".alert-error").removeClass("hide");
        $(".error").html("");
        $(".error").append(data.message);
      }
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function total()
{
  var total = 0;

  $('#form .amount:checked').each(function(){
    total += parseFloat($(this).val());
  });

  total = parseFloat(total).toFixed(2);
  $('#form input[name=amount]').val(total);

  total = total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  $('#form #total-projected').text(total);
}

function get_offline()
{
  if($('input[name=offline]').is(':checked')){
    $('.control-group.date').removeClass('hide');
  }
  else {
    $('.control-group.date').addClass('hide');

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;
    var yyyy = today.getFullYear();

    if (dd < 10) dd = "0"+dd;
    if (mm < 10) mm = "0"+mm;
    $('input[name=date]').val(yyyy + '-' + mm + '-' + dd);
  }
}

$(function(){
  $(".table").dataTable({
    "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
    "sPaginationType": "bootstrap",
    "oLanguage": {
      "sLengthMenu": "_MENU_ records per page"
    },
    "bFilter": false,
    "bSort": false,
    "iDisplayLength": 6,
    "aLengthMenu": [[6, 12, -1], [6, 12, "All"]]
  });
});