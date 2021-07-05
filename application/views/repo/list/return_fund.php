<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$title = (in_array($this->session->position, array(72,73,83))) ? 'Edit Return Fund': 'View Return Fund';
$btn   = (in_array($this->session->position, array(72,73,83))) ? 'Edit': 'View';
 ?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Return Fund</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <div class="row">

            <div class="control-group span5">
              <div class="control-label">Date</div>
              <div class="controls">
                <input class="datepicker" type="text" name="date_from" value="<?php echo $date_from; ?>" autocomplete="off">
              </div>
            </div>

            <div class="control-group span5" <?php echo (in_array($this->session->position, array(72,73,83))) ? 'style="display:none"': '';?>>
              <div class="control-label">Status</div>
              <div class="controls">
                <?php echo form_dropdown('status', array(0 => '- Any -') + $statuses, set_value('status', 0)); ?>
              </div>
            </div>
            
            
          </div>
          <div class="row">
            <div class="control-group span5">
              <div class="control-label"> To</div>
              <div class="controls">
                <input class="datepicker" type="text" name="date_to" value="<?php echo $date_to; ?>" autocomplete="off">
              </div>
            </div>
            <div class="control-group span5" <?php echo (in_array($this->session->position, array(72,73,83))) ? 'style="display:none"': '';?>>
              <div class="control-label">Company</div>
              <div class="controls">
                <?php echo form_dropdown('company', array(0 => '- Any -') + $companies, set_value('company', 0)); ?>
              </div>
            </div>

           
          </div>

          <div class="row">
            <div class="control-group span5">
              <div class="control-label">Reference #</div>
              <div class="controls">
                <?php print form_input('reference', set_value('reference')); ?>
              </div>
            </div>
            
            <div class="control-group span5" <?php echo (in_array($this->session->position, array(72,73,83))) ? 'style="display:none"': '';?>>
              <div class="control-label">Region</div>
              <div class="controls">
                <?php
                  $config = [ 'class' => 'form_dropdown'];
                  $val = '0';
                  if ($_SESSION['dept_name'] === 'Regional Registration') {
                    $config['disabled'] = 'true';
                    $val = $_SESSION['region_id'];
                  }
                  print form_dropdown('region', array_merge(array(0 => '- Any -'), $region), set_value('region', $val), $config);
                ?>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="form-actions span5">
                <input type="submit" class="btn btn-success" value="Search" name="search">
            </div>
          </div>
          <hr>
          <table id="return-fund-table" class="table">
            <thead>
              <tr>
                <th>Date Entry</th>
                <th>Batch #</th>
                <th>Company</th>
                <th>Region</th>
                <th>Amount</th>
                <th>Attachment</th>
                <th>Status</th>
                <th>Date Liquidated</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr id="tr_id_'.$row->return_fund_id.'">';
              print '<td>'.$row->created.'</td>';
              print '<td>'.$row->reference.'</td>';
              print '<td>'.$row->companyname.'</td>';
              print '<td>'.$row->region.'</td>';
              print '<td style="text-align: right">'.number_format($row->amount,2).'</td>';
              if (!empty($row->image_path)) {
                print '<td><a href="'.base_url().$row->image_path.'" target="_blank">'.$row->reference.'</a></td>';
              }else{
                print '<td></td>';
              }
              print '<td>'.strtoupper($row->status).'</td>';
              print (empty($row->liq_date)) ? '<td>-</td>' : '<td>'.$row->liq_date.'</td>';
              print '<td><button type="button" class="btn btn-success btn-edit-repo-fund" value="'.$row->return_fund_id.'" data-title="'.$title.'">'.$btn.'</button></td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr>';
              print '<td></td>';
              print '<td></td>';
              print '<td></td>';
              print '<td></td>';
              print '<td>No result.</td>';
              print '<td></td>';
              print '<td></td>';
              print '<td></td>';
              print '<td></td>';
              print '</tr>';
            }
            ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
