<div class="block">
  <div class="navbar navbar-inner block-header">
    <div class="pull-left">SI Transmittal</div>
  </div>
</div>

<div class="container">
  <form class="form-horizontal" method="POST">
    <div class="control-group">
      <label class="control-label">Transmittal Date:</label>
      <div class="controls">
        From <input class="datepicker" type="text" name="date_from" value="<?php echo set_value('date_from', ''); ?>">
        To <input class="datepicker" type="text" name="date_to" value="<?php echo set_value('date_to', ''); ?>">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Branch Code:</label>
      <div class="controls">
        <input type="text" name="bcode" value="<?php echo set_value('bcode', ''); ?>">
        <input type="submit" value="Search" name="search" class="btn btn-success">
      </div>
    </div>
  </form>
</div>

<div class="container">
  <?php echo $table; ?>
</div>

<script type="text/javascript">
$(".table").dataTable();
</script>
