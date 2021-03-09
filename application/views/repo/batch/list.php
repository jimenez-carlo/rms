<div class="row-fluid">
  <!-- block -->
  <div class="block">
<!--
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
-->
    <div class="navbar navbar-inner block-header">
      <div class="pull-left">Repo Batch</div>
    </div>
    <br>
    <div class="container">
      <form class="form-horizontal">
        <div class="row">
          <div class="span4">
            <div class="control-group">
              <label class="control-label" for="date-from">Date From</label>
              <div class="controls">
                <?php echo form_input(["id"=>"date-from", "name"=>"date_from"]); ?>
              </div>
            </div>
          </div>
          <div class="span6">
            <div class="control-group">
              <label class="control-label" for="date-to">Date To</label>
              <div class="controls">
                <?php echo form_input(["id"=>"date-to", "name"=>"date_to"]); ?>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="span6">
            <div class="control-group">
              <label class="control-label" for="reference">Reference#</label>
              <div class="controls">
                <?php echo form_input(["id"=>"reference", "name"=>"reference"]); ?>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="span6">
            <div class="control-group">
              <div class="controls">
                <?php echo form_button("search", "Search", ["class"=>"btn btn-success"]); ?>
                <a class="btn btn-primary" href="<?php echo base_url('repo/create_ca'); ?>" target="_self">Create CA</a>
                <?php // echo form_button("create", "Create CA", ["class"=>"btn btn-primary"]); ?>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php echo $table_batches; ?>
  </div>
</div>
