var tid;
var sid;

function view(_tid, _sid)
{
  tid = _tid;
  sid = _sid;

  $.ajax({
    url : "../view_remarks",
    type: "POST",
    data: {"tid": tid, "sid": sid},
    dataType: "JSON",
    success: function(data)
    {
      $(".error").html("");
      $(".alert-error").addClass("hide");

      if (data.content == '') $('#form .remarks').html("No remarks.<hr>");
      else $('#form .remarks').html(data.content);

      $('#form')[0].reset(); // reset form on modals
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
    url : "../save_remarks",
    type: "POST",
    data: {"tid": tid, "sid": sid, "remarks": $('#form textarea').val()},
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
