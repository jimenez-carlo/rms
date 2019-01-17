<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

            <footer>
                <p class="pull-left footer">Registration Monitoring System (Rev. 1.2)</p>
                <p class="pull-right footer">&copy; CMC 2017 </p>
            </footer>
        </div>

        <div class="ajax-loader hide" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; ">
            <div style="background: linear-gradient(#000, #FFF, #FFF, #FFF, #FFF, #FFF, #FFF, #FFF, #000); opacity: .5; position: absolute; width: 100%; height: 100%;"></div>
            <img src="<?php if(isset($dir)) echo $dir; ?>images/loader.gif" style="position: absolute; top: 35%; left: 45%;">
        </div>

        <!--/.fluid-container-->
        <script src="<?php if(isset($dir)) echo $dir; ?>vendors/jquery-1.9.1.min.js"></script>
        <script src="<?php if(isset($dir)) echo $dir; ?>bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php if(isset($dir)) echo $dir; ?>vendors/select2.min.js"></script> 
        <script src="<?php if(isset($dir)) echo $dir; ?>vendors/bootstrap-datepicker.js"></script>
        <script type="text/javascript">
        function commafy(val, prefix) {
            prefix = prefix || "\u20b1";

            val = val.toFixed(2);
            val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

            if (prefix != "") val = "\u20b1 "+val;
            return val;
        }
        function toFloat(val) {
            val = parseFloat(val.toString().replace(/[^\d\.]/g, ""));
            if (!val) val = 0;
            return val;
        }

        $(document).ready(function(){
            $('select').select2();
            $(".datepicker").datepicker({
                  format: 'yyyy-mm-dd'
                });

            $(document).ajaxStart(function(){
                $('.ajax-loader').removeClass('hide');
            }).ajaxComplete(function(){
                $('.ajax-loader').addClass('hide');
            });

            $('form').submit(function(){
                $(this).find('.numeric').each(function(){
                    $(this).val( toFloat( $(this).val() ) );
                });
            });
        });
        </script>
        <script src="<?php if(isset($dir)) echo $dir; ?>assets/autocomma.js"></script>
        <?php if(isset($script)) echo $script; ?>
    </body>

</html>