<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$ctr=0;
foreach ($table as $row)
{
  if($ctr>0) print '<td></td><td></td><td></td>';

  print '<td>'.$row->region->name.'</td>';
  print '<td>'.$row->company->code.'</td>';
  print '<td>'.$row->date.'</td>';
  print '<td style="text-align:right;">'.number_format($row->amount,2,'.',',').'</td>';
  
  print '<td>';
  if($ctr==0) print '<a class="btn btn-success" href="fund_transfer/sprint/'.$row->ftid.'">Print</a>';
  print '</td>';

  print '</tr>';
  $ctr++;
}
?>