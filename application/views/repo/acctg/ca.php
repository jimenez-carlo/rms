<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo CA</div>
      </div>
      <div class="block-content collapse in">
        <?php foreach($for_cas AS $ca): ?>
          <?php echo form_open(); ?>
          <table class="table">
            <thead>
              <tr>
                <th>
                <?php
                  echo $ca['rrt_region'].' '.$ca['company_code'];
                  echo form_hidden('region_company', $ca['rrt_region'].' '.$ca['company_code']);
                ?>
                </th>
                <th>Rerfo</th>
                <th>Branch Code</th>
                <th>Branch Name</th>
                <th># of Unit</th>
                <th></th>
                <th>Amount</th>
                <th>Document Number</th>
              </tr>
            </thead>
            <tbody>
              <?php $rerfos = json_decode($ca['rerfos'],true); ?>
              <?php foreach($rerfos AS $rerfo): ?>
              <td>
                <td><?php echo $rerfo['rerfo']; ?></td>
                <td><?php echo $rerfo['bcode']; ?></td>
                <td><?php echo $rerfo['bname']; ?></td>
                <td><?php echo $rerfo['no_of_rerfo_unit']; ?></td>
                <td></td>
                <td><?php echo number_format($rerfo['rerfo_amount'], 2, '.', ','); ?></td>
                <td>
                  <input
                    type="text"
                    placeholder="Input Document Number"
                    name="<?php echo 'rerfos'.'['.$rerfo['repo_rerfo_id'].']'; ?>"
                    value="<?php echo set_value('rerfos'.'['.$rerfo['repo_rerfo_id'].']',''); ?>"
                    required="true"
                  >
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:right"><b>Total # of Unit</b></td>
                <td><b><?php echo $ca['total_no_of_unit']; ?></b></td>
                <td style="text-align:right"><b>Total Amount</b></td>
                <td><b><?php echo $ca['total_amount']; ?></b></td>
                <td>
                  <button class="print btn btn-success">Print</button>
                  <button class="save btn btn-warning" name="save" value="true">Save</button>
                </td>
              </tr>
            </tfoot>
          </table>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script>
$('.print').on('click', function(e){
  e.preventDefault();
  var form = $(this).closest('form');
  form.attr('target', '_blank');
  form.attr('action', '<?php echo base_url('repo/print_ca'); ?>');
  form.submit();
});

$('.save').on('click', function(e){
  var confirmed = confirm('Are you sure?');
  if (!confirmed) {
    return false;
  }
  var form = $(this).closest('form');
  form.removeAttr('target');
  form.attr('action', '<?php echo base_url('repo/ca'); ?>');
});
</script>
