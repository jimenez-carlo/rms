var repo_in = new Array();

$('#engine_no').on('keypress', function(e){
  if (e.keyCode === 13) {
    e.preventDefault();
    $('form').submit();
  }
});

$('form').on('submit', function(e){
  e.preventDefault();
  var data = $(this).serializeArray();
  $.ajax({
    url: 'repo/get_sales',
    type: "POST",
    data: data,
    dataType: 'json',
    complete: function() {
      $('ajax-loader').hide();
      removeEngine();
    },
    success: function(data) {
      if (data !== null) {
        repo_in.push(data);
        console.log(repo_in);
        $('#result').empty().append(JSON.stringify(data));
        $('#tbl_content').prepend('<tr><td>'+data.engine_no+'</td><td>'+data.mvf_no+'</td><td>'+data.customer_name+'</td><td><button class="btn btn-small btn-danger"><i class="icon-trash"></i></button></td><tr>');
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      $('ajax-loader').hide();
      alert('Error get data from ajax');
    }
  });
});

function removeEngine() {
  $('.btn-danger').on('click', function(e){
    e.preventDefault();
    $(this).closest('tr').remove();
  });
}
