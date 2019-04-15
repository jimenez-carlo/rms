<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<ul class="result">
	<?php
	foreach ($result as $row) {
		print '<li><a><span class="engine_no">'.$row->engine_no.'</span> - '.$row->first_name.' '.$row->last_name.'</a></li>';
	}

	if (empty($result)) {
		print '<li>No result.</li>';
	}
	?>
</ul>
<hr>

<script type="text/javascript">
$(function(){
	$(document).ready(function(){
		$('.result a').click(function(){
			$('#engine_no').val( $(this).find('.engine_no').text() );
		});
	});
});
</script>