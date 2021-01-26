<div class="block">
  <div class="navbar navbar-inner block-header">
    <div class="pull-left">Self Registration</div>
  </div>
</div>

<?php echo form_open('',['class'=>'form-horizontal']); ?>
  <div class="container">
    <div class="row">
      <div class="offset9 span3">
        <div class="control-group">
          <label class="control-label">Out PNP:</label>
          <div class="controls">
              <button id="pnp-out" class="btn btn-success" type="button" disabled>Confirm</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
  <div class="container">
    <?php echo $table; ?>
  </div>
</form>

<script type="text/javascript">
  var btnConfirm = $("#pnp-out");
  var btnConfirmDisable = function() {
    btnConfirm.prop('disabled', true)
  };

  $("input[type=checkbox]").on("change", function(e) {
    if ($("input[type=checkbox]:checked").length) {
      btnConfirm.prop('disabled', false);
    } else {
      btnConfirmDisable();
    }
  });

  btnConfirm.on("click", function(e) {
    e.preventDefault();
    var confirmed = confirm("Are you sure?");
    if (confirmed) {
      $("form").submit();
    }
  });

  $(".table").dataTable();
</script>
