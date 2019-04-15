<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Topsheet</div>
            </div>
            <div class="block-content collapse in">

                <!-- Search Form -->
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <div class="row-fluid">
                            <div class="control-group">
                                <?php
                                    echo form_label('Transaction #', 'trans_no', array('class' => 'control-label'));
                                    echo '<div class="controls">';
                                    echo form_input('trans_no', set_value('trans_no'));
                                    echo '</div>';
                                ?>
                            </div>
                        </div>
                        <div class="form-actions span12">
                            <input type="submit" value="Search" class="btn btn-success" name="search">
                        </div>
                    </fieldset>
                </form>

			</div>
		</div>

	</div>
 </div>