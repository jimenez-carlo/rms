<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="navbar navbar-inner block-header">
      <div class="pull-left">Create CA</div>
    </div>
    <br>
    <div class="container">
      <?php echo form_open("",["onsubmit"=>"return confirm('Are you sure?')"]); ?>
        <?php echo $table_sales; ?>
        <br>
        <button id="create-ca" class="btn btn-success" disabled>Create CA</button>
      </form>
    </div>
  </div>
</div>

<script>


$('input[type="checkbox"]').on('click', function(){
  var bool = $('input[type="checkbox"]:checkbox:checked').length;
  $('input[type="checkbox"]').prop('required', !bool);
  $('#create-ca').prop('disabled', !bool);
});
</script>
