var ftid;

function detail(_ftid)
{
  $.ajax({
    url : "audit_detail/" + _ftid,
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

function edit(_ftid)
{
  ftid = _ftid;
  $.ajax({
    url : "edit_details/" + _ftid,
    type: "POST",
    dataType: "JSON",
    success: function(data)
    {
      $(".error").html("");
      $(".alert-error").addClass("hide");
      $('#form_e')[0].reset(); // reset form on modals
      $('#form_e input').val(data);
      $('#modal_form_e').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function save_date()
{
  $.ajax({
    url : "save_date/" + ftid,
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

$(function(){
  $(".table").dataTable({
    "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
    "sPaginationType": "bootstrap",
    "oLanguage": {
      "sLengthMenu": "_MENU_ records per page"
    },
    "bSort": false,
    "bFilter": false,
    "iDisplayLength": 5
  });
});