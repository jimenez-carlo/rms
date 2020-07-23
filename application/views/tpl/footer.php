<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

            <footer>
                <p class="pull-left footer">Registration Monitoring System v2.3.5</p>
                <p class="pull-right footer">&copy; CMC <?php print date('Y'); ?> </p>
            </footer>
        </div>

        <div class="ajax-loader hide" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; ">
            <div style="background: linear-gradient(#000, #FFF, #FFF, #FFF, #FFF, #FFF, #FFF, #FFF, #000); opacity: .5; position: absolute; width: 100%; height: 100%;"></div>
            <img src="<?php echo base_url(); ?>images/loader.gif" style="position: absolute; top: 35%; left: 45%;">
        </div>

        <!--/.fluid-container-->
        <script type="text/javascript">
        BASE_URL = "<?php echo base_url(); ?>";
        function commafy(val, prefix) {
            prefix = prefix || "\u20b1";

            val = val.toFixed(2);
            val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

            if (prefix != "") val = "\u20b1 "+val;
            return val;
        }
        function toFloat(val) {
            val = parseFloat(val.toString().replace(/[^-?\d\.]/g, ""));
            if (!val) val = 0;
            return val;
        }

        function removeElement(array, elem) {
          var index = array.indexOf(elem);
          if (index > -1) {
            array.splice(index, 1);
          }
        }

        function find(array, value){
          return array.some(function(arrVal){
            return arrVal === value;
          });
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
        <?php if(isset($script)) echo $script; ?>
        <?php if(isset($return_fund_js)) echo $return_fund_js; ?>
    </body>

</html>
