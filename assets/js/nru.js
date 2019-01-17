$(function(){
	var datatable = $(".table").dataTable({
		"sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_ records per page"
		},
		"bSort": false,
		"iDisplayLength": 5,
		"aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
	});

	$('.fund_all.default').click();
	$('.fund1').click();

	$("form").submit(function(){
		datatable.$(".registration").each(function(){
			if (!$.contains(document, this) && this.value > 0)
			{
				$("form").append("<input type=hidden name="+this.name+" value="+this.value+">");
				var radio = $(this).closest("tr").find(".fund:checked");
				$("form").append("<input type=hidden name="+$(radio).prop("name")+" value="+$(radio).val()+">");
			}
		});
	});

	$("th a").click(function(){
		var registration = toFloat($(".registration_all").val());
		var fund = $("input[name=fund_all]:checked").val();
		if (!registration) registration = "0.00";
		$(".registration_all").val(registration).change();

		var nodes = datatable.$("tr", {"filter": "applied"});
		nodes.each(function(){
			$(this).find(".registration").val(registration).change();
			$(this).find('.fund').removeAttr('checked');
			$(this).find('.fund'+fund).click();
		});

		$("input[name=submit]").addClass("hide");
		$(".calculate").removeClass("hide");
	});

	datatable.$(".registration, .tip").keypress(function(){
		$("input[name=submit]").addClass("hide");
		$(".calculate").removeClass("hide");
	});

	$(".calculate").click(function(){
		var exp = 0;
		datatable.$(".mnc").each(function(){
			var val = toFloat($(this).val())
			if (val) exp += val;
			else $(this).val("0.00").change();
		});
		$(".mnc-exp").text(commafy(exp));

		exp = 0;
		datatable.$(".mti").each(function(){
			var val = toFloat($(this).val())
			if (val) exp += val;
			else $(this).val("0.00").change();
		});
		$(".mti-exp").text(commafy(exp));

		exp = 0;
		datatable.$(".hpti").each(function(){
			var val = toFloat($(this).val())
			if (val) exp += val;
			else $(this).val("0.00").change();
		});
		$(".hpti-exp").text(commafy(exp));
		
		$("input[name=submit]").removeClass("hide");
		$(".calculate").addClass("hide");
	}).click();
});