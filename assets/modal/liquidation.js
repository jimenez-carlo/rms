function details(_ftid)
{
  $.ajax({
    url : "liquidation/details/" + _ftid,
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