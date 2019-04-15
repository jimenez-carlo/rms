<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Topsheet # <?php print $topsheet->trans_no; ?></div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <?php print form_hidden('tid', $topsheet->tid); ?>

      		<table class="table">
    				<thead>
    					<tr>
                <th><p>Transmittal #</p></th>
                <th><p>Destination</p></th>
                <th><p>Origin</p></th>
                <th><p>Included</p></th>
    					</tr>
    				</thead>
    				<tbody>
    					<?php
              foreach ($topsheet->sales as $sales)
              {
                $date = date('mdy', strtotime($topsheet->date));

                print '<tr>';
                print '<td>'.$sales->bcode.$sales->sales_type.'0'.$date.'</td>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$topsheet->region.' RRT</td>';
                print '<td>'.$sales->names.'</td>';
                print '</tr>';

                if ($sales->sales_type == 1) {
                  print '<tr>';
                  print '<td>'.$sales->bcode.'11'.$date.'</td>';
                  print '<td>BMI '.$topsheet->region.'</td>';
                  print '<td>'.$topsheet->region.' RRT</td>';
                  print '<td>'.$sales->names.'</td>';
                  print '</tr>';                  
                }
              }
              ?>
    				</tbody>
      		</table>
        </form>
  	  </div>
  	</div>
  </div>
</div>