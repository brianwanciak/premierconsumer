<?php
	if ( defined( 'API_Util_Functions' ) ) { return ; }	
	define( 'API_Util_Functions', true ) ;

	FUNCTION Util_Functions_Sort_Compare($a, $b){ return strnatcmp($b['total'], $a['total']) ; }

	FUNCTION Util_Functions_Bytes( $bytes )
	{
		$string = "" ;
		$kils = round ( $bytes/1000 ) ;
		$kil_re = ( $bytes % 1000 ) ;
		if ( $kils >= 1000 )
		{
			$megs = floor ( $kils/1000 ) ;
			$meg_re = ( $kils % 1000 ) ;
			$meg_per = round( $meg_re/1000 ) ;
			$megs_final = $megs + $meg_per ;
			$string = "$megs_final M" ;
		}
		elseif ( ( $bytes < 1000 ) && ( $bytes ) )
			$string = "$bytes byte" ;
		else if ( $bytes )
			$string = "$kils k" ;
		else
			$string = "0 byte" ;

		return $string ;
	}

	FUNCTION Util_Functions_Page( $page, $index, $page_per, $total, $url, $query )
	{
		global $text ; global $s_as ;
		if ( !isset( $text ) ) { $text = "" ; }
		if ( !isset( $s_as ) || !$s_as ) { $s_as = "text" ; }

		$string = "" ;
		$pages = $remainder = 0 ;

		$remainder = ( $total % $page_per ) ;
		$pages = floor( $total/$page_per ) ;
		$pages = ( $remainder ) ? $pages + 1 : $pages ;

		$span = 10 ;
		$remainder = ( $pages % $span ) ;
		$groups = floor( $pages/$span ) ;
		$groups = ( $remainder ) ? $groups + 1 : $groups ;
		$start = ( $index * $span ) ;
		$end = $start + $span ;

		$group_prev = "" ;
		if ( $index > 0 )
		{
			$c = $start - $span ;
			$new_index = $index - 1 ;
			$group_prev = "<div class=\"page\" onClick=\"location.href='$url?page=$c&index=$new_index&$query'\">...prev</div>" ;
		}

		$group_next = "" ;
		if ( $index < ( $groups - 1 ) )
		{
			$c = $end ;
			$new_index = $index + 1 ;
			$group_next = "<div class=\"page\" onClick=\"location.href='$url?page=$c&index=$new_index&$query'\">next...</div>" ;
		}

		$string .= $group_prev ;
		for ( $c = $start; $c < $end; ++$c )
		{
			if ( $c < $pages )
			{
				$this_page = $c + 1 ;

				if ( $c == $page )
					$string .= "<div class=\"page_focus\">$this_page</div>" ;
				else
					$string .= "<div class=\"page\" onClick=\"location.href='$url?page=$c&index=$index&$query'\">$this_page</div>" ;
			}
		}
		$string .= $group_next ;

		if ( preg_match( "/(op_trans.php)|(transcripts.php)/", $url ) )
			$string .= "<div style=\"float: left; padding-left: 10px;\"><form method=\"POST\" onSubmit=\"return false;\" id=\"form_search\">Search: <input type=\"text\" class=\"input_text_search\" size=\"25\" maxlength=\"255\" style=\"font-size: 10px;\" id=\"input_search\" value=\"$text\" onKeydown=\"input_text_listen_search(event);\"> &nbsp; <select name=\"s_as\" id=\"s_as\" style=\"font-size: 10px;\"><option value=\"text\">text</option><option value=\"ces\">chat ID</option><option value=\"vid\">visitor ID</option></select> &nbsp; <input type=\"button\" id=\"btn_page_search\" style=\"\" class=\"input_button\" value=\"submit\" onClick=\"do_search('$url?$query')\"> <input type=\"button\" style=\"\" class=\"input_button\" value=\"reset\" onClick=\"location.href=reset_url\"></form></div><script type=\"text/javascript\">$('#s_as').val('$s_as')</script>" ;

		$string .= "<div style=\"clear: both;\"></div>" ;

		return $string ;
	}

	FUNCTION Util_Functions_Stars( $directory, $rating )
	{
		global $theme ;
		$star_img = "$directory/themes/$theme/stars.png" ;

		$output = "<div style=''>" ;
		for ( $c = 1; $c <= $rating; ++$c )
			$output .= "<div style='float: left; width: 12px; height: 12px; background: url( $star_img ) no-repeat; background-position: 0px -12px;'></div>" ;
		for ( $c2 = $c; $c2 <= 5; ++$c2 )
			$output .= "<div style='float: left; width: 12px; height: 12px; background: url( $star_img ) no-repeat;'></div>" ;
		$output .= "<div style='clear: both;'></div></div>" ;
		
		return $output ;
	}

?>