	</div>

</div>

<div style="padding: 25px; background: url( <?php echo $CONF["BASE_URL"] ?>/pics/bg_fade_lite.png ) no-repeat; background-position: center top;">
	<div style="width: 970px; margin: 0 auto; font-size: 10px; color: #FFFFFF; text-shadow: 1px 1px #779A22;">
		&copy; OSI Codes Inc. - powered by <a href="http://www.phplivesupport.com/?plk=osicodes-5-ykq-m" target="_blank" style="color: #FFFFFF;">PHP Live! Support</a> v.<?php echo $VERSION ?>

		<div id="div_debug_console_footer" style="<?php echo ( isset( $VAR_DEBUG_OUT ) && $VAR_DEBUG_OUT ) ? "" : "display: none;" ?> margin-top: 15px; text-align: right;">
			<span>
			<?php
				$var_process_end = ( $var_microtime ) ? microtime(true) : time() ;
				$pd = $var_process_end - $var_process_start ; if ( !$pd ) { $pd = 0.001 ; }
				$pd = str_replace( ",", ".", $pd ) ;
				$var_mem_end = ( function_exists( "memory_get_usage" ) ) ? memory_get_usage() : 0 ;
				$var_mem_usage = round( ( $var_mem_end - $var_mem_start ) * .001 ) ;
				print "DB queries: " . $dbh['qc'] . " / Process Duration: ".number_format( $pd, 3 )." / Server Mem: ".$var_mem_usage."Kb" ;
			?>
			</span>
		</div>
	</div>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; }
?>