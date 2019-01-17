function upload()
{
  $.ajax({
    url : "upload",
    type: "POST",
    data: new FormData($('#form')[0]),
    dataType: "JSON",
    contentType: false,
    processData: false,
    success: function(data)
    {
      if(data.status)
      {
      	attachment = '<div class="attachment temp" style="position:relative"><input type="hidden" name="temp[]" value="' + data.file.filename + '"><img src="./../' + data.file.path + '" style="margin:5px; border:solid"><a style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a></div>';

      	$(".attachments").html("");
      	$(".attachments").html(attachment);

				$(".attachment-block").addClass("span8");
	      $(".sales-block").removeClass("hide");
      }
      else 
      {
        $(".alert-error").removeClass("hide");
        $("body").prepend(data.message);
      }    
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function unlink(filename)
{
  $.ajax({
    url : "registration/unlink/",
    type: "POST",
    data: {"filename": filename},
    dataType: "JSON",
  });
}

function get_offline()
{
  if($('input[name=offline]').is(':checked')){
    $('.control-group.date').removeClass('hide');
    document.getElementById("or_date").disabled = false;
  }
  else {
    $('.control-group.date').addClass('hide');
    document.getElementById("or_date").disabled = true;

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
	$(".uniform_on").uniform();

	// show on post
	if ($(".attachment").length > 0)
	{
		$(".attachment-block").addClass("span8");
		$(".sales-block").removeClass("hide");
	}
	
	$("#save").on("click", function() {
		 return confirm('Are you sure you want to save?');
	});

	$(document).on('click', '.attachment a', function(){
		var filename = $(this).closest(".attachment").find("input").val();
		unlink(filename);
  	$(this).closest(".attachment").remove();

		if ($(".attachment").length == 0)
		{
			$(".attachment-block").removeClass("span8");
			$(".sales-block").addClass("hide");
		}
	});

	var offset = 90;
	$(document).on("scroll", function(){
		if ($(this).scrollTop() > offset) {
			$(".sales-block").attr("style", "position:fixed; top:4.5%; right:0");
		}
		else {
			$(".sales-block").removeAttr("style");
		}
	}).scroll();

	$("input[name=registration], input[name=tip]").keypress(function(){
		$(".calculate").removeClass("hide");
		$("input[name=submit]").addClass("hide");
	});

	$(".calculate").click(function(){
		var amount = ( toFloat($(".registration").text())
			+ toFloat($(".tip").text()) )
			-
			( toFloat($("input[name=registration]").val())
			+ toFloat($("input[name=tip]").val()) );

		var cash = commafy( toFloat( $(".cash").text() ));

		if (amount < 0)
		{
			cash = cash + " (" + commafy(amount) + ")";
		}
		else if (amount > 0)
		{
			cash = cash + " (+ " + commafy(amount) + ")";
		}

		$(".cash-on-hand").text(cash);
		$(".calculate").addClass("hide");
		$("input[name=submit]").removeClass("hide");
	}).click();
});