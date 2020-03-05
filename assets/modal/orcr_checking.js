var sid;

function hold(_sid)
{
  sid = _sid;
  $(".error").html("");
  $(".alert-error").addClass("hide");

  if (sid == 0) {
    $("#form .reason").hide();
    $("#form .reason0").show();
  }
  else {
    $("#form .reason").show();
    $("#form .reason0").hide();
  }

  $("#form select").select2("val", "");
  $("#form select").change();

  $('#form')[0].reset(); // reset form on modals
  $('#modal_form').modal('show'); // show bootstrap modal
}

function save_hold()
{
  $.ajax({
    url : "./../../orcr_checking/hold/" + sid,
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
  if ($(".tbl-sales tbody tr").length == 0) $(".tbl-sales").addClass("hide");

  $(".tbl-sales tbody tr.sales").click(function(){
    var index = $(this).index()+1;
    $(".tbl-sales tbody tr:eq("+index+")").removeClass("hide");
  });

  $(".tbl-sales tbody tr.attachments .close").click(function(){
    $(this).closest("tr").addClass("hide");
  });

  $(".misc-hold").click(function(){
    $(this).addClass("hide");
    $(".misc-cancel").removeClass("hide");
    $(".new-remarks").removeClass("hide");
    $("input[name=misc_save]").removeClass("hide");
  });

  $(".misc-cancel").click(function(){
    $(".misc-hold").removeClass("hide");
    $(this).addClass("hide");
    $(".new-remarks").addClass("hide");
    $("input[name=misc_save]").addClass("hide");
  });

  $("select").change(function(){
    var val = ($(this).val() != null) ? $(this).val() : "";

    if (val.indexOf("0") != -1)
      $(".remarks").removeClass("hide");
    else
    {
      $(".remarks").addClass("hide");
      $("textarea[name=remarks]").val("");
    }
  });
});

