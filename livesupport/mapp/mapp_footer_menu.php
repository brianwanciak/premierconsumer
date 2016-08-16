<div id="chat_footer_mapp" style="display: none; position: absolute; z-Index: 102; width: 95%; bottom: 0px;">
		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td width="100%">
				<div style="background: url( ../pics/glass/glass_grey.png ) repeat-x #C2CDD2; border-top: 1px solid #9AA3A7; border-right: 1px solid #9AA3A7; padding: 6px; height: 36px; border-top-right-radius: 10px 10px; -moz-border-radius-topright: 10px 10px; ">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td width="50%">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td><span style="cursor: pointer;" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_chats', '', '', 'Chat Sessions' )"><img src="../mapp/pics/menu_chats.png?<?php echo $VERSION ?>" width="36" height="36" border="0" alt="" class="round" style="border: 1px solid #939B9F;" id="mapp_icon_chats"></span></td>
								<td style="padding-left: 25px;"><span style="cursor: pointer;" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_cans', '', '', 'Canned Responses' )"><img src="../mapp/pics/menu_cans.png?<?php echo $VERSION ?>" width="36" height="36" border="0" alt="" class="round" style="border: 1px solid #939B9F;" id="mapp_icon_cans"></span></td>
							</tr>
							</table>
						</td>
						<td width="50%" align="right" style="">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<?php if ( $opinfo["traffic"] && ( $CONF['icon_check'] == "on" ) ): ?>
								<td><div style="width: 20px; height: 20px; background: url( ../pics/glass/glass_grey.png ) repeat-x #C2CDD2; border: 1px solid #9AA3A7; padding: 5px; text-align: center; line-height: 20px; text-shadow: -1px 1px #FFFFFF; cursor: pointer;" class="round" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_traffic', '', '', 'Traffic Monitor' )" id="mapp_icon_traffic"><span id="chat_footer_traffic_counter_mapp">00</span></div></td>
								<?php endif ; ?>
								<td style="padding-left: 25px;"><span style="cursor: pointer;" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_power', '', '', 'Online Status' )"><img src="../mapp/pics/menu_power.png?<?php echo $VERSION ?>" width="36" height="36" border="0" alt="" class="round" style="border: 1px solid #939B9F;" id="mapp_icon_power"></span></td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
				</div>
			</td>
			<td style="padding-left: 25px;" width="61"><span style="cursor: pointer;" onClick="toggle_mapp_menu_prefs(0)"><img src="../mapp/pics/menu_prefs.png?<?php echo $VERSION ?>" width="36" height="36" border="0" alt="" class="round" style="border: 1px solid #939B9F;" id="mapp_icon_prefs"></span></td>
		</tr>
		</table>
</div>
<div id="info_disconnect_mapp" class="info_disconnect" style="position: absolute; display: none; top: 0px; right: 0px; z-Index: 104;" onClick="pre_disconnect();"></div>

<div id="div_menu_prefs" style="display: none; position: absolute; bottom: 0px; right: 0px; width: 200px; height: 280px; background: url( ../pics/glass/glass_grey.png ) repeat-x #61696D; border-top-left-radius: 10px 10px; -moz-border-radius-topleft: 10px 10px; z-Index: 101;">
	<div class="menu_mapp_pref" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_trans', '', '', 'Transcripts' )" style="border-top-left-radius: 10px 10px; -moz-border-radius-topleft: 10px 10px;" >Transcripts</div>
	<?php if ( $opinfo["op2op"] ): ?><div class="menu_mapp_pref" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_operators', '', '', 'Operators' )">Operators</div><?php endif ; ?>
	<div class="menu_mapp_pref" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_themes', '', '', 'Themes' )">Themes & Sounds</div>
	<div class="menu_mapp_pref" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_prefs', '', '', 'Preferences' )">Preferences</div>
</div>
<span id="div_mapp_chat_bubble_red" style="display: none; position: absolute; bottom: 22px; left: 0px; padding: 10px; cursor: pointer; z-Index: 103;" onClick="toggle_mapp_menu_prefs(1);toggle_extra( 'mapp_chats', '', '', 'Chat Sessions' )"><img src="../mapp/pics/chat_bubble.png" width="32" height="32" border="0" alt=""></span>