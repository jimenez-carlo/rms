<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<form class="form-horizontal" enctype="multipart/form-data" method="post" id="form">
    <?php print form_hidden('tid', $topsheet->tid); ?>
    <span class="meal hide"><?php echo $topsheet->meal; ?></span>
    <span class="photocopy hide"><?php echo $topsheet->photocopy; ?></span>
    <span class="transportation hide"><?php echo $topsheet->transportation; ?></span>
    <span class="others hide"><?php echo $topsheet->others; ?></span>

    <!-- Attachments -->
    <div class="span5">
        <div class="attachments">
            <?php
            if (!empty($topsheet->files))
            {
              foreach ($topsheet->files as $key => $file)
              {
                print '<div class="attachment files" style="position:relative">';
                print form_hidden('files[]', $file);

                $path = './../../rms_dir/misc/'.$topsheet->tid.'_'.$topsheet->trans_no.'/'.$file;
                print '<img src="'.$path.'" style="margin:5px; border:solid">';

                print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
                print '</div>';
              }
            }

            $temp = set_value('temp', null);
            if (!empty($temp))
            {
              foreach ($temp as $key => $file)
              {
                print '<div class="attachment temp" style="position:relative">';
                print form_hidden('temp[]', $file);

                $path = './../../rms_dir/temp/'.$file;
                print '<img src="'.$path.'" style="margin:5px; border:solid">';

                print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
                print '</div>';
              }
            }
            ?>
            <div class="attachment empty hide" style="position:relative">No attachments.</div>
        </div>

        <!-- Upload Form -->
        <?php if ($topsheet->print == 0) { ?>
        <hr>
        <div class="control-group">
          <div class="control-label">
            Upload File
          </div>
          <div class="controls">
            <input type="file" name="scanFiles[]" class="input-file uniform_on" id="scanFiles" multiple>
            <br>
            <b>Required file format: jpeg, jpg</b>
            <br><b>You can only upload upto 1MB</b>
          </div>
        </div>
        <div class="form-actions">
            <a class="btn btn-success" onclick="upload()">Upload</a>
        </div>
        <?php } ?>
    </div>

    <!-- Expense -->
    <div class="span6" style="margin-left:5em">
        <table class="table">
            <tbody>

                <!-- Input -->
                <?php if ($topsheet->print == 0) { ?>
                <tr>
                    <td width="60%">Meal:</td>
                    <td><?php echo form_input('meal', set_value('meal', $topsheet->meal), array('class' => 'misc numeric'));?></td>
                </tr>
                <tr>
                    <td>Photocopy:</td>
                    <td><?php echo form_input('photocopy', set_value('photocopy', $topsheet->photocopy), array('class' => 'misc numeric')); ?></td>
                </tr>
                <tr>
                    <td>Transportation:</td>
                    <td><?php echo form_input('transportation', set_value('transportation', $topsheet->transportation), array('class' => 'misc numeric')); ?></td>
                </tr>
                <tr>
                    <td>Others:</td>
                    <td><?php echo form_input('others', set_value('others', $topsheet->others), array('class' => 'misc numeric')); ?></td>
                </tr>
                <tr class="others-specify hide">
                    <td>Please specify:</td>
                    <td><?php echo form_input('others_specify', set_value('others_specify', $topsheet->others_specify)); ?></td>
                </tr>
                <tr>
                    <th>Total Miscellaneous:</th>
                    <th>
                        <span class="total-misc"></span><a><i class="icon-refresh" style="font-size:15px !important; padding-left:15px"> Click to recalculate</i></a>
                    </th>
                </tr>
                <tr>
                    <th>Cash on Hand:</th>
                    <th><p class="cash"><?php print number_format($topsheet->fund,2,'.',','); ?></p></th>
                </tr>

                <!-- Print -->
                <?php } else { ?>
                <tr>
                    <td>Meal:</td>
                    <td><p class="text-right"><?php print $topsheet->meal; ?></p></td>
                </tr>
                <tr>
                    <td>Photocopy:</td>
                    <td><p class="text-right"><?php print $topsheet->photocopy; ?></p></td>
                </tr>
                <tr>
                    <td>Transportation:</td>
                    <td><p class="text-right"><?php print $topsheet->transportation; ?></p></td>
                </tr>
                <tr>
                    <td>Others: <?php if (!empty($topsheet->others_specify)) print '('.$topsheet->others_specify.')'; ?> </td>
                    <td><p class="text-right"><?php print $topsheet->others; ?></p></td>
                </tr>
                <tr>
                    <th>Total Miscellaneous:</th>
                    <th><p class="text-right"><?php print $topsheet->total_misc; ?></p></th>
                </tr>

                <?php } ?>
            </tbody>
        </table>

        <br>
        <table class="table">
            <tbody>
                <tr>
                    <th>TOTAL GIVEN AMOUNT</th>
                    <th><p class="text-right total-amt">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ","); ?></p></th>
                </tr>
                <tr>
                    <th>LESS TOTAL EXPENSE</th>
                    <th><p class="text-right total-exp"><?php print number_format($topsheet->total_expense + $topsheet->total_misc, 2, ".", ","); ?></p></th>
                </tr>
                <tr>
                    <th>BALANCE</th>
                    <th><p class="text-right total-bal"><?php print number_format($topsheet->total_balance, 2, ".", ","); ?></p></th>
                </tr>
            </tbody>
        </table>

        <div class="form-actions span12">
            <?php
            if ($topsheet->print == 0) {
                print '<input type="submit" name="print" value="Print" id="print" class="btn btn-success">';
            }
            else {
                print '<a href="../request/'.$topsheet->tid.'" class="btn btn-success">Request Reprinting</a>';
            }
            ?>
        </div>
    </div>
</form>
