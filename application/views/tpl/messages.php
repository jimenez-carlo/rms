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
                    <button class="close" data-dismiss="alert">x</button>
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
            <?php if (!empty($_SESSION["warning"]) || validation_errors() != '') { ?>
                <div class="alert alert-danger">
                    <button class="close" data-dismiss="alert">x</button>
                    <ul>
                    <?php
                    if (!empty($_SESSION["warning"])) {
                        foreach ($_SESSION["warning"] as $message) {
                            echo "<li>".$message."</li>";
                        }
                        unset($_SESSION["warning"]);
                    }

                    if (validation_errors() != '') print validation_errors('<li>', '</li>');
                    ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php } ?>