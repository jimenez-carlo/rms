var filenames = new Array(), paths = new Array(), attachments = '';

function upload()
{
  $.ajax({
    url : "../upload",
    type: "POST",
    data: new FormData($('#form')[0]),
    dataType: "JSON",
    contentType: false,
    processData: false,
    success: function(data)
    {
      if(data.status)
      {
      	filenames.push(data.filename);
      	paths.push(data.path);

      	// sort
      	filenames.sort();
      	paths.sort();

      	attachments = '';

      	for (var i = 0; i < filenames.length; i++) {
      		attachments += '<div class="attachment temp" style="position:relative"><input type="hidden" name="files[]" value="' + filenames[i] + '">';
      		attachments += '<img src="' + paths[i] + '" style="margin:5px; border:solid"><a style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a></div>';
      	}

      	$(".attachments").html("");
      	$(".attachments").html(attachments);

	      $(".error").html("");
	      $(".alert-error").addClass("hide");
	      check_attachments();
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

function unlink(filename)
{
  $.ajax({
    url : "../unlink/",
    type: "POST",
    data: {"filename": filename},
    dataType: "JSON",
  });
}

function check_attachments()
{
	if ($('.attachment').length == 1)
		$('.attachment.empty').removeClass('hide');
	else
		$('.attachment.empty').addClass('hide');
}
check_attachments();

$(function(){
	$(".uniform_on").uniform();
	
	$(document).on('click', '.attachment a', function(){
		if ($(this).closest(".attachment").hasClass("temp"))
		{
			var filename = $(this).closest(".attachment").find("input").val();
			unlink(filename);
			$(this).closest(".attachment").remove();
			check_attachments();
		}
		else
		{
			$(this).closest(".attachment").html("<span style=color:red>This attachment will be remove upon save.</span>");
		}
	});
	
	$("#print").on("click", function() {
		 return confirm('Are you sure you want to save and print?');
	});

	$("input.misc").keypress(function(){
		$(".icon-refresh").removeClass("hide");
		$("input[name=print]").attr("disabled", "disabled");
	});
	$(".icon-refresh").click(function(){
		$(this).addClass("hide");

		var misc = 0;
		$("input.misc").each(function(){
			if (toFloat($(this).val()))
			{
				misc += toFloat($(this).val());
			}
			else $(this).val("0.00").change();
		});

		var exp = 0;
		$(".exp").each(function(){
			if (toFloat($(this).text()))
			{
				exp += toFloat($(this).text());
			}
			else $(this).text(commafy(0));
		});

		var exp = misc + exp;
		var bal = toFloat($(".total-amt").text()) - exp;

		$(".total-misc").text(commafy(misc));
		$(".total-exp").text(commafy(exp));
		$(".total-bal").text(commafy(bal));
		compute_fund();

		if (bal < 0)
		{
			alert("Balance must not be negative");
		}
		else
		{
			$("input[name=print]").removeAttr("disabled");
		}
	}).click();

	function compute_fund()
	{
		var others = toFloat($(".others").text()) + toFloat($("input[name=others]").val());
		if (others > 0) $(".others-specify").removeClass("hide");
		else
		{
			$(".others-specify").addClass("hide");
			$(".others-specify input").val("");
		}

		var amount = ( toFloat($(".meal").text())
			+ toFloat($(".photocopy").text())
			+ toFloat($(".transportation").text())
			+ toFloat($(".others").text()) )
			-
			( toFloat($("input[name=meal]").val())
			+ toFloat($("input[name=photocopy]").val())
			+ toFloat($("input[name=transportation]").val())
			+ toFloat($("input[name=others]").val()) );

		var cash = commafy( toFloat( $(".cash").text() ));
		$(".cash").removeAttr('style');
		
		if (amount < 0)
		{
			cash = cash + " (" + commafy(amount) + ")";
			$(".cash").css('color', 'red');
		}
		else if (amount > 0)
		{
			cash = cash + " (+ " + commafy(amount) + ")";
		}

		$(".cash").text(cash);
	}
});