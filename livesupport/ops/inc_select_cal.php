<?php
	$cal_year = date( "Y", time() ) ;
	$c_start = ( isset( $y_start ) ) ? $y_start : 2002 ;
?>
<script type="text/javascript">
<!--
	function select_day( thescript )
	{
		month = $('#day_month').val() ;
		year = $('#day_year').val() ;
		
		location.href = thescript+"?console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&ses=<?php echo $ses ?>&m="+month+"&y="+year+"&"+unixtime() ;
	}
//-->
</script>
<?php $path = explode( "/", $_SERVER['PHP_SELF'] ) ; $total = count( $path ) ; $script = $path[$total-1] ; ?>
<form><select class="select_calendar" id="day_month"><?php for( $c = 1; $c <= 12; ++$c ){ $selected = ( $c == $m ) ? "selected" : "" ; print "<option value=\"$c\" $selected>".date("F", mktime( 0, 0, 1, $c, 1, 2010 ))."</option>" ; } ?></select> <select class="select_calendar" id="day_year"><?php for( $c = $c_start; $c <= $cal_year; ++$c ){ $selected = ( $c == $y ) ? "selected" : "" ; print "<option value=\"$c\" $selected>$c</option>" ; } ?></select> <button type="button" style="font-size: 12px;" onClick="select_day('<?php echo $script ?>');">submit</button></form>