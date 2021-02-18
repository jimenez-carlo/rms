<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<form action="#" id="form" class="form-horizontal" style="margin:0px!important;">
  <?php echo $table; ?>

  <hr style="margin:10px!important;">

  <div class="row-fluid">
    <div class="span1"></div>
    <div class="span5">
      <p>Please enter document number and click save.</p>
    </div>
    <div class="span5">
    </div>
  </div>
</form>

<?php if(isset($javascript)): ?>
<script>
  <?php print $javascript; ?>
</script>
<?php endif; ?>
