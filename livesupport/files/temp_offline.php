<?php
	$r = isset( $_GET["r"] ) ? preg_replace( "/[^phplive_embed]/i", "", $_GET["r"] ) : "" ;
	$embed = preg_match( "/(phplive_embed)/", $r ) ? 1 : 0 ;
?>
<!DOCTYPE html>
<html lang="en-US">
<head><title> Live Chat Temporarily Unavailable </title></head>
<body style="background: #F6F3F3; margin: 0; padding: 0; overflow: auto; font-family: Arial; font-size: 12px; color: #524F4F;">
<div style="padding: 10px;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%">
	<tr>
		<td width="100%"><div style="font-size: 18px; font-weight: bold; color: #FD7D7F;">Live Chat Temporarily Unavailable</div></td>
		<td width="16"><?php if ( $embed ): ?><img src="../pics/icons/close.png" width="16" height="16" border="0" alt=""><?php endif ; ?></td>
	</tr>
	</table>
	<div style="margin-top: 10px;">Live Chat is temporarily unavailable.  Please try back at a later time.  Thank you for your patience.</div>
	<div style="margin-top: 25px; border-top: 1px solid #A19E9E; font-size: 10px; padding-top: 10px;">PHP Live! Support &copy; OSI Codes Inc.</div>
</body>
</html>