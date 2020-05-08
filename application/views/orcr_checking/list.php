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
    <div class="block span2">
      <div class="navbar navbar-inner block-header" style="width:79%; position:relative; z-index:1;">
        <div class="pull-left">Reference Number</div>
      </div>
      <div class="block-content collapse in" style="margin:0; height:690px; overflow-y:auto;">
        <form class="form-horizontal list-form" method="post" style="margin:0;">
          <?php //print form_hidden('tid', 0); ?>
          <?php //print form_hidden('vid', 0); ?>
          <input id="for_check_id" type="hidden">
          <table class="table" style="margin:0;">
            <tbody>
              <?php
                if (empty($ca_refs)) {
                  print '<tr><td>No result.</td></tr>';
                } else {

                  foreach ($ca_refs as $ca_reference) {
                    $style = ($reference_selected === $ca_reference['reference']) ? 'style="background:mediumaquamarine;"' : '';
                    echo '<tr id="'.$ca_reference['reference'].'" '.$style.'>';
                    echo   '<td>
                      <a class="btn btn-success btn-mini" onclick="get_sales('.$ca_reference['id'].', \''.$ca_reference['budget_type'].'\', \''.$ca_reference['reference'].'\')">
                      <i class="icon-edit"></i>
                      </a> '.$ca_reference['reference'].
                      '</td>';
                    echo '</tr>';
                  }
                }

                // foreach ($table as $row)
                // {
                //   if (isset($topsheet) && $topsheet->tid == $row->tid) print '<tr class="info">';
                //   else if ($row->alert > 0) print '<tr class="warning">';
                //   else print '<tr>';
                //   print '<td>
                //           <a class="btn btn-success btn-mini" onclick="tid('.$row->tid.')">
                //             <i class="icon-edit"></i>
                //           </a> '.$row->trans_no.'</td>';
                //   print '</tr>';
                // }

                // if (empty($table))
                // {
                //   print '<tr><td>No result.</td></tr>';
                // }
              ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>

    <!-- block -->
    <div class="block span10">
      <div class="navbar navbar-inner block-header" style="position:relative;">
        <div class="pull-left">Transaction <?php echo (isset($ca_ref)) ? '# '.$ca_ref['reference'] : ''; ?></div>
      </div>
      <div class="block-content collapse in">
        <?php
        if(isset($view)) {
          print $view;
        }
        else {
          print '<p><span class="icon icon-chevron-left"></span> Select a CA reference to check OR CR attachment and details.</p>';
        }
        ?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
setTimeout(function(){
  window.scrollTo(0, 0);
}, 1000);

function tid(_tid) {
  $('.list-form input').val(_tid);
  $('.list-form').submit();
}

function get_sales(id, budget_type, reference) {
  $('#for_check_id').prop({'name': budget_type, 'value': id});
  $('.list-form').attr('action', 'orcr_checking#'+reference);
  $('.list-form').submit();
}
</script>
