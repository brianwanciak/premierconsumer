<body style="">

<script type="text/javascript">
<!--
	$(init_inview) ;
	$(window).scroll( init_inview ) ;

	function check_inview( theobject )
	{
		var scroll_top = $(window).scrollTop() ;
		var scroll_view = scroll_top + $(window).height() ;

		var pos_top = $(theobject).offset().top ;
		var pos_bottom = pos_top + $(theobject).height() ;

		return ((pos_bottom <= scroll_view) && (pos_top >= scroll_top) ) ;
	}

	function init_inview() {
		if ( check_inview( $('#menu_wrapper') ) )
			$('#div_scrolltop').fadeOut("fast") ;
		else
			$('#div_scrolltop').fadeIn("fast") ;
	}

	function scroll_top()
	{
		$('html, body').animate({
			scrollTop: 0
		}, 200);
	}

//-->
</script>

<div id="div_scrolltop" style="display: none; position: fixed; top: 25%; right: 0px; z-index: 1000;">
	<div style="padding: 5px; background: #DFDFDF; border: 1px solid #B9B9B9; border-right: 0px; text-shadow: 1px 1px #FFFFFF; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-bottom-left-radius: 5px 5px; -moz-border-radius-bottomleft: 5px 5px; cursor: pointer;" onClick="scroll_top()"><img src="../pics/icons/arrow_top.png" width="15" height="16" border="0" alt=""> top</div>
</div>

<?php include("premier_header.php"); ?>

<div id="header_wrapper" style="background: #77B6C5;">
	<div style="background: url( <?php echo $CONF["BASE_URL"] ?>/pics/clouds.png ) repeat-x; background-position: bottom;">
		<div style="width: <?php echo $body_width  ?>px; margin: 0 auto;">
			<div id="menu_wrapper" style="padding-top: 20px;">
				<?php if ( !$console ): ?><div id="menu_go" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(notifications)|(transcript)|(activity)|(report)|(settings)/", $menu ) ) ? "location.href='./?ses=$ses&console=$console&wp=$wp&auto=$auto'" : "toggle_menu_op('go', '$ses')" ; ?>"><img src="../pics/icons/bulb.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Go ONLINE!</div><?php endif ; ?>
				<div id="menu_themes" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(notifications)|(transcript)|(activity)|(report)|(settings)/", $menu ) ) ? "location.href='./?menu=themes&ses=$ses&console=$console&wp=$wp&auto=$auto'" : "toggle_menu_op('themes', '$ses')" ; ?>"><img src="../pics/icons/menu_icons.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Themes</div>
				<div id="menu_notifications" class="menu" onClick="location.href='./notifications.php?ses=<?php echo $ses ?>&console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>'"><img src="../pics/icons/menu_sound.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Notifications</div>
				<?php if ( !$console ): ?><div id="menu_cans" class="menu" onClick="location.href='./cans.php?ses=<?php echo $ses ?>&console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>'"><img src="../pics/icons/menu_cans.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Canned Responses</div><?php endif ; ?>
				<div id="menu_reports" class="menu" onClick="location.href='./reports.php?ses=<?php echo $ses ?>&console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>'"><img src="../pics/icons/menu_calendar.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Reports</div>
				<?php if ( !$console ): ?><div id="menu_trans" class="menu" onClick="location.href='transcripts.php?ses=<?php echo $ses ?>&console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>'"><img src="../pics/icons/menu_trans.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Transcripts</div><?php endif ; ?>
				<div id="menu_activity" class="menu" onClick="location.href='./activity.php?ses=<?php echo $ses ?>&console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>'"><img src="../pics/icons/menu_calendar.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Online Activity</div>
				<div id="menu_settings" class="menu" onClick="location.href='./settings.php?ses=<?php echo $ses ?>&console=<?php echo $console ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>'"><img src="../pics/icons/menu_settings.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Settings</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div style="margin-top: 15px; padding-top: 15px;">
			<div style="width: <?php echo $body_width  ?>px; margin: 0 auto; padding-bottom: 10px; text-align: right;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="70%">&nbsp;</td>
					<td align="right" nowrap>
						<div style="padding: 10px; background: #7CBDCD; border: 5px solid #EDF6FA; color: #FFFFFF; text-align: center;" class="round_top">Chat Operator: <span style="font-size: 14px; font-weight: bold;"><?php echo $opinfo["login"] ?></span> <?php if ( !$console ): ?>&bull; <a href="JavaScript:void(0)" onClick="logout_op('<?php echo $ses ?>')" style="color: #FFFFFF;">sign out</a><?php endif ; ?></div>
					</td>
				</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<div style="width: 100%; padding-top: 25px; background: url( ../pics/bg_header.gif ) repeat-x #FFFFFF;">
	<div style="width: <?php echo $body_width  ?>px; margin: 0 auto; background: url( ../pics/grass.png ) no-repeat left bottom; padding-bottom: 100px;">

