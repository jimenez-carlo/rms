var sid;

function remarks(_sid)
{
  sid = _sid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form')[0].reset(); // reset form on modals
  $('#modal_form').modal('show'); // show bootstrap modal
}

function save_remarks()
{
  $.ajax({
    url : "../save_remarks/" + sid,
    type: "POST",
    data: $('#form').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form').modal('hide');
        $("textarea.remarks").val( $("#form textarea").val() );
        $("input[name=submit]").click();
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
  $(".uniform_on").uniform();

  $(".attachment a").click(function(){
    if ($(this).closest(".attachment").hasClass("temp"))
    {
      $(this).closest(".attachment").remove();
    }
    else
    {
      $(this).closest(".attachment").html("<span style=color:red>This attachment will be remove upon save.</span>");
    }

    if ($(".attachment").length == 0)
    {
      $(".search-form").addClass("hide");
      $("input[name=submit]").addClass("hide");
    }
  });

  var offsales = $(".sales-form").offset().top - 50;
  $(document).on("scroll", function(){
    if ($(this).scrollTop() > offsales)
    {
      $(".sales-form").attr("style", "position:fixed; top:7%; right:0");
    }
    else
    {
      $(".sales-form").removeAttr("style");
    }
  });

  $("input[name=registration], input[name=tip]").keypress(function(){
    $(".calculate").removeClass("hide");
    $("input[name=submit]").addClass("hide");
  });

  $(".calculate").click(function(){
    var amount = ( parseFloat($(".registration").text()) + parseFloat($(".tip").text()) ) - ( parseFloat($("input[name=registration]").val()) + parseFloat($("input[name=tip]").val()) );

    var cash = $(".cash").text();
    $("input[name=pos_expense]").val("0");
    $("input[name=neg_expense]").val("0");

    if (amount < 0)
    {
      cash = cash + " (" + amount.toFixed(2) + ")";
      $("input[name=neg_expense]").val(amount.toFixed(2));
      $("input[name=pos_expense]").val("0");
    }
    else if (amount > 0)
    {
      cash = cash + " (+" + amount.toFixed(2) + ")";
      $("input[name=pos_expense]").val(amount.toFixed(2));
      $("input[name=neg_expense]").val("0");
    }

    $(".cash-on-hand").text(cash);
    $(".calculate").addClass("hide");
    $("input[name=submit]").removeClass("hide");
  }).click();
});