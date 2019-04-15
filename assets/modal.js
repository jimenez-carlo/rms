$(function(){
  $('.btn-modal').click(function(){
    var id = this.id.split('\\W+');
    var modal_id = '#modal_'+$id[0];
    var key_id = str[1];

    $(modal_id+' .modal-form')[0].reset(); // reset form on modal
    $(modal_id+' .modal-key').val(key_id);
    $(modal_id).modal('show'); // show bootstrap modal
  });

  $('.modal-form .submit').click(function(){
    var form = $(this).closest('.modal-form');
    var modal = $(this).closest('.modal');

    $.ajax({
      url : form.attr('action'),
      type: "POST",
      data: form.serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          modal.modal('hide');
          location.reload();
        }
        else  {
          modal.find(".alert-error").removeClass("hide");
          modal.find(".error").html(data.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  });
});