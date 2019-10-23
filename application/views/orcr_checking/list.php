<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.block {
    margin: 1em;
}
</style>

<div class="container-fluid form-horizontal">
	<div class="row-fluid">
    <!-- block -->
    <div class="block span2" style="min-height: 86vh; overflow-y: auto;">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Topsheet</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal list-form" method="post" style="margin:0;">
          <?php print form_hidden('tid', 0); ?>

          <table class="table" style="margin:0;">
            <tbody>
              <?php
              foreach ($table as $row)
              {
                if (isset($topsheet) && $topsheet->tid == $row->tid) print '<tr class="info">';
                else if ($row->alert > 0) print '<tr class="warning">';
                else print '<tr>';
                print '<td>
                  <a class="btn btn-success btn-mini" onclick="tid('.$row->tid.')">
                    <i class="icon-edit"></i>
                  </a> '.$row->trans_no.'</td>';
                print '</tr>';
              }

              if (empty($table))
              {
                print '<tr><td>No result.</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>

    <!-- block -->
    <div class="block span10">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transaction <?php if(isset($topsheet)) print '# '.$topsheet->trans_no; ?></div>
      </div>
      <div class="block-content collapse in">
        <?php
        if(isset($view)) {
          print $view;
        }
        else {
          print '<p><span class="icon icon-chevron-left"></span> Select a topsheet to check OR CR attachment and details.</p>';
        }
        ?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function tid(_tid) {
  $('.list-form input').val(_tid);
  $('.list-form').submit();
}
</script>
