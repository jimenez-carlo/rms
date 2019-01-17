function view_attachment(sid,engine_no)
{
  $.ajax({
    url : "registration2/ajax_view_attachment/" + sid + "/" + engine_no,
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
				$(".attachments").html("");
				for(var i=0; i < data.length; i++)
				{
					$(".attachments").append( "<img src=\'./../rms_dir/scan_docs/" + sid + "_" + engine_no + "/" + data[i] + "\'><br>" );
				}

      $("#modal_form").modal("show");	 
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
        alert("Error get data from ajax");
    }
  });
}