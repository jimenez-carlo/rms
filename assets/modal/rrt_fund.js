var fid, cid, type;

function modal(_fid, _cid, _type)
{
  fid = _fid;
  cid = _cid;
  type = _type;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form')[0].reset(); // reset form on modals
  $('#modal_form').modal('show'); // show bootstrap modal

  switch (type)
  {
    case 1:
      $(".modal-title").text('Cash Withdrawal');
      $(".check_no").addClass('hide');
      $("input[name=check_no]").val('N/A');
      $("#btnSubmit").text('Withdraw');
      break;
    case 2:
      $(".modal-title").text('Check Withdrawal');
      $(".check_no").removeClass('hide');
      $("input[name=check_no]").val('');
      $("#btnSubmit").text('Withdraw');
      break;
    case 3:
      $(".modal-title").text('Cash Deposit');
      $(".check_no").addClass('hide');
      $("input[name=check_no]").val('N/A');
      $("#btnSubmit").text('Deposit');
      break;
  }
}

function submit()
{
  $("#form input.numeric").each(function(){
    var val = toFloat($(this).val());
    $(this).val(val);
  });

  $.ajax({
    url : "fund/transaction/" + fid + '/' + cid + '/' + type,
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