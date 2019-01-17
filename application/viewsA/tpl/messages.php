<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!empty($_SESSION["messages"]) 
    || !empty($_SESSION["warning"]) 
    || validation_errors() != '')
{
?>

<div class="container-fluid messages">
    <div class="row-fluid">
    	<div class="span12">
            <!-- Information -->
            <?php if (!empty($_SESSION["messages"])) { ?>
                <div class="alert alert-success">
                    <button class="close" data-dismiss="alert-success">x</button>
                    <ul>
                    <?php
                    foreach ($_SESSION["messages"] as $message) {
                        echo "<li>".$message."</li>";
                    }
                    unset($_SESSION["messages"]);
                    ?>
                    </ul>
                </div>
            <?php } ?>

            <!-- Warning -->
            <?php if (!empty($_SESSION["warning"])) { ?>
                <div class="alert alert-warning">
                    <button class="close" data-dismiss="alert-warning">x</button>
                    <ul>
                    <?php
                    foreach ($_SESSION["warning"] as $message) {
                        echo "<li>".$message."</li>";
                    }
                    unset($_SESSION["warning"]);
                    ?>
                    </ul>
                </div>
            <?php } ?>

            <!-- Form Validation -->
            <?php if (validation_errors() != '') { ?>
                <div class="alert alert-danger">
                    <button class="close" data-dismiss="alert-danger">x</button>
                    <ul>
                    <?php echo validation_errors('<li>', '</li>'); ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php } ?>