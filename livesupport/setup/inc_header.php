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
	<div style="padding: 5px; background: #DFDFDF; border: 1px solid #B9B9B9; border-right: 0px; text-shadow: 1px 1px #FFFFFF; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-bottom-left-radius: 5px 5px; -moz-border-radius-bottomleft: 5px 5px; cursor: pointer;" onClick="scroll_top()"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/arrow_top.png" width="15" height="16" border="0" alt=""> top</div>
</div>

<div id="header_wrapper" style="background: #77B6C5;">
	<div style="background: url( <?php echo $CONF["BASE_URL"] ?>/pics/clouds.png ) repeat-x; background-position: bottom;">
		<div style="width: 970px; margin: 0 auto;">
			<div id="menu_wrapper" style="padding-top: 20px;">
				<div id="menu_home" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_home.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Home</div>
				<div id="menu_depts" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/depts.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_depts.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Departments</div>
				<div id="menu_ops" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/ops.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_ops.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Operators</div>
				<div id="menu_interface" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/interface_themes.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_icons.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Interface</div>
				<div id="menu_icons" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/icons.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_icons.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Chat Icons</div>
				<div id="menu_html" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/code.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_code.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> HTML Code</div>
				<div id="menu_rchats" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/reports_chat.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_chats.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round">Reports</div>
				<div id="menu_trans" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/transcripts.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_trans.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Transcripts</div>
				<div id="menu_rtraffic" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/reports_traffic.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_marketing.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Traffic</div>
				<div id="menu_extras" class="menu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/extras.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_extras.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Extras</div>
				<div id="menu_settings" class="menu" style="margin-right: 0px;" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/setup/settings.php?ses=<?php echo $ses ?>'"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/menu_settings.png" width="12" height="12" border="0" alt="" style="padding: 2px; background: #77B6C5;" class="round"> Settings</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div style="margin-top: 15px; padding-top: 15px;">
			<div style="width: 970px; margin: 0 auto; padding-bottom: 10px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td align="right" width="100%">&nbsp;</td>
					<td width="160" nowrap>
						<div style="padding: 10px; background: #7CBDCD; border: 5px solid #EDF6FA; color: #FFFFFF; text-align: center;" class="round_top"><img src="<?php echo $CONF["BASE_URL"] ?>/pics/icons/bulb.png" width="14" height="14" border="0" alt="" id="img_bulb"> <a href="<?php echo $CONF["BASE_URL"] ?>/setup/ops.php?ses=<?php echo $ses ?>&jump=online" style="color: #FFFFFF;">Go <span style="font-weight: bold;">ONLINE</span></a></div>
					</td>
					<td align="right" style="padding-left: 15px;" nowrap>
						<div style="padding: 10px; background: #7CBDCD; border: 5px solid #EDF6FA; color: #FFFFFF; text-align: center;" class="round_top">Setup Admin: 
						<?php if ( $admininfo["status"] != -1 ): ?><a href="<?php echo $CONF["BASE_URL"] ?>/setup/settings.php?ses=<?php echo $ses ?>&jump=profile" style="color: #FFFFFF;"><span style="font-size: 14px; font-weight: bold;"><?php echo $admininfo["login"] ?></span></a>
						<?php else: ?>
						<span style="font-size: 14px; font-weight: bold;"><?php echo $admininfo["login"] ?></span>
						<?php endif ; ?>
						&nbsp; &bull; &nbsp; <a href="<?php echo $CONF["BASE_URL"] ?>/logout.php?action=logout&menu=sa" style="color: #FFFFFF;">logout</a></div>
					</td>
				</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<div style="width: 100%; padding-top: 60px; background: url( <?php echo $CONF["BASE_URL"] ?>/pics/bg_header.gif ) repeat-x #FFFFFF;">
	<div style="width: 970px; margin: 0 auto; background: url( <?php echo $CONF["BASE_URL"] ?>/pics/grass.png ) no-repeat left bottom; padding-bottom: 100px;">
