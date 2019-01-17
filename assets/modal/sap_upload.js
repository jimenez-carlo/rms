var bid;

function save(_bid)
{
  bid = _bid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form')[0].reset(); // reset form on modals
  $('#modal_form').modal('show'); // show bootstrap modal
}

function save_doc()
{
  $.ajax({
    url : "sap_upload/liquidate/" + bid,
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

function set_id(index)
{
  // disable all
  $(".table tbody tr .doc_no").prop('disabled', true);
  $(".table tbody tr .save").prop('disabled', true);

  // enable specific
  $(".table tbody tr:eq("+index+") .doc_no").prop('disabled', false);
  $(".table tbody tr:eq("+index+") .save").prop('disabled', false);
}