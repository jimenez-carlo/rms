$(function(){
	$("input[type=radio]").change(function(){
		if ($(this).val() == 1)
		{
			$(this).closest("tr").find(".select2-container").removeClass("hide");
		}
		else
		{
      $(this).closest("tr").find(".select2-container").addClass("hide");
      $(this).closest("tr").find("select").select2("val", "0");
		}
	});
	$(":checked").change();
});