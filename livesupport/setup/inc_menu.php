		<script type="text/javascript">
		<!--
		function show_div( thediv )
		{
			var divs = Array( "marketing", "external", "apis", "smtp", "emoticons" ) ;
			for ( var c = 0; c < divs.length; ++c )
			{
				$('#extras_'+divs[c]).hide() ;
				$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
			}

			$('input#jump').val( thediv ) ;
			$('#extras_'+thediv).show() ;
			$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
		}
		//-->
		</script>
		<?php $addon_emo = ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emo.php" ) ) ? 1 : 0 ; ?>
		<?php $addon_smtp = ( is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/smtp.php" ) ) ? 1 : 0 ; ?>
		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/extras.php?ses=<?php echo $ses ?>&jump=apis'" id="menu_apis">Dev APIs</div>
			<div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/marketing.php?ses=<?php echo $ses ?>'" id="menu_marketing">Marketing</div>
			<div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/extras_geo.php?ses=<?php echo $ses ?>'" id="menu_geoip">GeoIP</div>
			<div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/extras_geo.php?ses=<?php echo $ses ?>&jump=geomap'" id="menu_geomap">Google Maps</div>
			<div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/extras.php?ses=<?php echo $ses ?>&jump=external'" id="menu_external">External URLs</div>
			<?php if ( $addon_smtp ): ?><div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/addons/smtp/smtp.php?ses=<?php echo $ses ?>'" id="menu_smtp">SMTP</div><?php endif ; ?>
			<?php if ( $addon_emo ): ?><div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/addons/emoticons/emo.php?ses=<?php echo $ses ?>'" id="menu_emoticons" id="menu_emoticons">Emoticons</div><?php endif ; ?>
			<div style="clear: both"></div>
		</div>