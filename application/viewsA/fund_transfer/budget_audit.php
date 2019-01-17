<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<table class="table">
  <thead>
    <tr>
      <th><p>Date</p></th>
      <th><p>Fund Given</p></th>
      <th><p>Projected Cost (Total)</p></th>
      <th><p>Breakdown</p></th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($table as $row)
    {
      print '<tr>';
      print '<td>'.$row->date.'</td>';
      print '<td>'.$row->amount.'</td>';
      print '<td>'.$row->total.'</td>';

      print '<td>';
      foreach ($row->audit as $audit)
      {
      	print $audit->amount.' ('.$audit->date.')<br>';
      }
      print '</td>';
      print '</tr>';
    }

    if (empty($table))
    {
      print '<tr><td colspan=20>No result.</td></tr>';
    }
    ?>
  </tbody>
</table>