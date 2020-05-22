$('.edit-actual-docs').on('click', function(e){
  $(this).hide();
  var id = $(this).val();
  $('#td-'+id).append('<input id="input-'+id+'" class="input-medium" name="transmittal_number" type="text" placeholder="Input Transmittal Number" required>');
  $('#save-'+$(this).val()).show();
});

$('.save-actual-docs').on('click', function(){
  var id = $(this).val();
  if ($('#input-'+id).val().length == 0) {
    confirm('Please input transmittal number.');
  } else {
    var confirmed = confirm('This action cannot be undone: Transmittal Number. Continue?');

    if (confirmed) {
      var new_transmittal_number = $('#input-'+id).val();
      $.ajax({
        url : BASE_URL+'actual_docs/save_transmittal_number',
        data: {
          'transmittal_number': new_transmittal_number
        },
        dataType: "json",
        type: "POST",
        beforeSend: function(){
          $('.ajax-loader').show();
          $('.alert').remove();
        },
        complete: function(){
          $('.ajax-loader, .save-actual-docs').hide();
        },
        success: function(data) {
          $('#actual_docs').before(data.message);
          $('#td-'+id).empty().append(new_transmittal_number);
        },
        error: function (jqXHR, textStatus, errorThrown){
        }
      });
    }
  }
});
