<?php
/////////////////////////////////////////////////////////////////////////////
//      Target Country by IP Address  - advanced redirection
//		- for MySql, Plain text databases-
//	Copyright (C) 2005 Jgsoft Associates - http://www.analysespider.com/
/////////////////////////////////////////////////////////////////////////////

//  If somewhere on your website, you want to apply other redirection rules, in other files, you could use the same databases and the same script. 
//  All you can make a copy of _re_rules.php -> new_re_rules.php,edit new_re_rules.php, just change the REDIRECTION RULES. 
//  Then include it in your files with 
//  $re_rules = "new_re_rules.php" ;
//  $anp_path="cr/"; 
//  include($anp_path."cr.php");

// apply other redirection rules file
if (empty($re_rules))
    $re_rules ="cur_re_rules.php";

include($anp_path.$re_rules);


//////////////////////////////////////////////////////////////////////////////
//START General Options
      define("_DEBUG_MODE","1");	// Debug Mode on/off
					// Possible options: "0", "1", "2"
					// "0" (Debug Mode off) means that this script will not print any information. Cookies will be set, so the detection will take place only once per session. If you change the redirection rules, to test or check the actions of the script you have to close all browser windows, and open a new browser window to your site.
					// "1" (Debug Mode on) means that the script prints runtime information above all the html of your site. this shows you step by step what is this script doing. When debug mode is on (1) no cookie will be set, so the detection takes place each time the script is executed.
					// "2" (Debug Mode on) means that the script will print all runtime information but will also set cookies and session variables, So the country detection takes place only once, then it is loaded from cookie/session vars.

      define("_NODE_ID","Redirection ID");		//This is the ID/Name of this set of redirection rules.

      $anp_db_type="text";		// IP -> country Database type
					// Possible options: "mysql", "text"
					// "mysql" : data is placed in a mysql database on your server
					// "text" : data is in one or more .dat files on your server

      $anp_url_save_method="session";	// The method used to save the country code for a certain visitor, in order to avoid searching the database every time he requests a page.
					// Possible options: "cookie", "session"
					// "cookie" : this sets a cookie in the visitor`s browser who expire in one year. The problem might be that the visitor could disable cookies for your domain, and the detection will be repeated each time he requests a page with CR installed. Also if you change the redirection rules, the redirection url saved in the visitor`s browser will not be updated. You`ll have to change the _NODE_ID in order to make sure all returning visitors are redirected like in the new set of rules. This method is not secure, the visitor might have the knowledge to change the value of the country code saved in his browser, and this way he could visit sections of your website not accessible to his country...
					// "session" : this way the country code is saved on the server and it`s secure in case you restrict access to your pages from certain countries, but the session expires when the user closes his browser, so the detection will take place each time the visitor comes back to your website. Also this way a ?PHP_SESSID might be added to the URL of your pages if the user disables cookies, this also means that search engine crawlers migh skip pages with CR installed if "session" used.
//END General Options


//START MySql options

	// NOTE: The MySql database can have only IP numbers.
	$anp_mysql_host = "localhost";	// MySQL hostname
	$anp_mysql_user = "root";			// MySQL user anp
	$anp_mysql_pass = "";			// MySQL password
	$anp_mysql_dbname = "_anp";		// MySQL database name
	$anp_mysql_table="anp_ip2country";	// Default: "anp_ip2country"
//END MySql options

//START Plain text database options

     //Note: The plain text database can have either IP numbers or IP addresses
     // If you purchased ip2country Redirector, this is the name of the plain text database file. Use "anp_ips.dat" for the commercial www.analysespider.com IP -> country database.

	$anp_text_db_name="anp_ips.dat";
									
//END Plain text database options


//Check Exceptions IP address List File
$bCheckExcept = true;

      //Check Blocks IP address List File
      $bCheckBlocked = false;

      $anp_blocked_reject_link="";
                // Blocks certain IP address's from your site and redirects the user to a blocked page. Leave blank (="") to take no action, and to load your pages for this visitors.

      $anp_default_reject_link="";
		// This is the URL where visitors who are in a country that has no REDIRECTION RULE specified will be redirected; ="" means no URL and your pages are loaded (visitors are accepted) (use an invalid url if you want to display a 404 error message to the visitor - ex: "index.asphp")

      $anp_default_redirect_link="";	
		// This is the URL where visitors who are in a country that has a REDIRECTION RULE but who has no redirection link associated are redirected. Leave blank (="") to take no action, and to load your pages for this visitors (use an invalid url to display a 404 error message to the visitor - ex: "index.asphp").


////////////////////////////////////////////////////////////////////////////
//START Exceptions

// EDIT cr/exceptions_ips.dat		
//Exceptions (the script do not execute) apply to all visitors with IP addresses included in exceptions_ips.dat so edit and place there all IP addresses or IP ranges you want to be excluded from the scan process.

//END Exceptions


////////////////////////////////////////////////////////////////////////////
//Blocks certain IP address's from your site

// EDIT cr/blocked_ips.dat And Set $bCheckBlocked = true;

//Blocks certain IP address's from your site,It will be redirected to $anp_blocked_reject_link
//and if $anp_blocked_reject_link is blank they will be accepted to view your pages
//apply to all visitors with IP addresses included in blocked_ips.dat so edit and place there all IP addresses or IP ranges you want to be excluded from the scan process.

//END Blocked
////////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////////
//START Redirection Rules
//Description: You can specify for each country a redirection link (URL), you could place a blank as redirection link (use an invalid url to display a 404 error message to the visitor - ex: "index.asphp") or you could comment the line (the default reject link will be used). By default you have a list of all countries but all have blank redirection links and the lines are commented. 
//Here there are 244 country rule lines but the free database included in this distribution (available from http://www.ip2country.com) has only 179 countries and it`s by far not as accurate and updated as our commercial database, available at http://www.analysespider.com
//Structure: <ISO Country code>=<Redirection link>

// EDIT cr/cur_re_rules.php

//END Redirection Rules


/////////////////////////////////
//DO NOT EDIT BEGIN THIS LINE
/////////////////////////////////

//Get the real IP address of the user
  $anp_ip = anp_realip();

  $anp_ip_number = anp_ip2long($anp_ip);

  $ip_blocked = 0;

  if(_DEBUG_MODE=="2") ob_start();

  if ("$anp_url_save_method"=="session") session_start();

  $anp_cookie_name="anp_".substr(md5(_NODE_ID),0,10);


  //Check Exceptions IP address List File
  if ($bCheckExcept)
   {
    //exceptions ip address
    if (anp_exceptions_ips($anp_ip,$anp_ip_number)==1) 
    {
     anp_save_status("T",2);
     $ip_blocked = 1;
     $anp_url="";
    }
   }

  //Check Blocks IP address List File
  if ($bCheckBlocked)
   {
    if ($ip_blocked==0)
    {
     //blocked ip address
     if (anp_block_ips($anp_ip,$anp_ip_number)==1) 
       {
        anp_save_status("T",3);
        $ip_blocked = 2;
        $anp_url=$anp_blocked_reject_link;
       }  
    }
   }
  
  if ($ip_blocked == 0)
   {
      if ($anp_db_type=="mysql") include($anp_path."anp_mysql_full.php"); 
      if ($anp_db_type=="text") include($anp_path."anp_text_full.php");

      $anp_country_code=anp_get_cookie();

      if ("$anp_country_code"=="none")
      {
         if ($anp_ip_number>0)
          {
          $anp_country_code = anp_get_country($anp_ip_number);

          //GB=Great Britain  UK=United Kingdom 
          if ($anp_country_code=="GB")
            $anp_country_code="UK";

           anp_save_status($anp_country_code,1);

          } 
      }	

     $anp_url=anp_country_to_url($anp_country_code);     
     $ip_blocked = 3;
   }	
 
if (strlen($anp_url) > 0)
 {
  anp_redirect($anp_url);
 }

if(_DEBUG_MODE=="2") ob_end_flush();


// Returns the real IP address of the user
function anp_realip()
{
    // No IP found (will be overwritten by for
    // if any IP is found behind a firewall)
    $ip = FALSE;
    
    // User is behind a proxy and check that we discard RFC1918 IP addresses
    // if they are behind a proxy then only figure out which IP belongs to the
    // user.  Might not need any more hackin if there is a squid reverse proxy
    // infront of apache.
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        // Put the IP's into an array which we shall work with shortly.
        $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);

        for ($i = 0; $i < count($ips); $i++) {
            // Skip RFC 1918 IP's 10.0.0.0/8, 172.16.0.0/12 and
            // 192.168.0.0/16 
            // below.
            if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    // Return with the found IP or the remote address
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

function anp_ip2long ($IPaddr)
{
if ($IPaddr == "") {
	return 0;
} else {
	$ips = split ("\.", "$IPaddr");
       return ($ips[3] + $ips[2] * 256 + $ips[1] * 65536 + $ips[0] * 16777216);
	}
}

function anp_ip_address_is_in_range($ipn,$min0,$max0)
{
	$min=anp_ip2long ($min0);
	$max=anp_ip2long ($max0);
	return (anp_ip_number_is_in_range($ipn,$min,$max));
}

function anp_ip_number_is_in_range($ipn,$min,$max)
{
	if (($min <= $ipn) && ($ipn <= $max))	return(1);
	return (0);
}

function anp_country_to_url($c)
{
	global $anp_rr,$anp_default_redirect_link,$anp_default_reject_link;
	if(_DEBUG_MODE) echo "Finding the URL for country '$c' <br>";
	if ((!is_array($anp_rr)) || (strlen("$c")==0)) return($anp_default_reject_link);
	if (!isset($anp_rr[$c])) return($anp_default_reject_link);
	if (strlen($anp_rr[$c])==0) return ($anp_default_redirect_link);
	return ($anp_rr[$c]);
}

function anp_redirect($url)
{
	if (strlen($url)!=0) 
	{
		if(_DEBUG_MODE) echo "The visitor should be redirected now to $url<br>";
		if(_DEBUG_MODE==0) header("Location: $url");
		die();
	}
	if(_DEBUG_MODE) echo "The visitor is not redirected. The page is loaded.<br>";
}


function anp_exceptions_ips($ip,$ipn)
{	
     global $anp_path;

     if (anp_get_exceptions()=="T") return (1);

	$gh=fopen($anp_path."exceptions_ips.dat","r");
	while(!feof($gh))
	{
		$line=trim(fgets($gh,41));

		if (strpos($line,";")>1) 
		{
			list($i1,$i2)=explode(";",$line);
			if (anp_ip_address_is_in_range($ipn,$i1,$i2)) 
			{
				if(_DEBUG_MODE) echo "The user is in the IP exception list<br>";
				fclose($gh);
				return (1);
			}
		}
		else 
		{
			if ("$ip"=="$line") 
			{
				if(_DEBUG_MODE) echo "The user is in the IP exception list<br>";
				fclose($gh);
				return (1);
			}
		}
	}
	fclose($gh);

	if(_DEBUG_MODE) echo "The user is NOT in the exception lists<br>";
	return (0);
}

function anp_block_ips($ip,$ipn)
{	
     global $anp_path;
     if (anp_get_blocked()=="T") return (1);
    
	$gh=fopen($anp_path."blocked_ips.dat","r");
	while(!feof($gh))
	{
		$line=trim(fgets($gh,41));
		if (strpos($line,";")>1) 
		{
			list($i1,$i2)=explode(";",$line);
			if (anp_ip_address_is_in_range($ipn,$i1,$i2)) 
			{
				if(_DEBUG_MODE) echo "The user is in the blocked IP list<br>";
				fclose($gh);
				return (1);
			}
		}
		else 
		{
			if ("$ip"=="$line") 
			{
				if(_DEBUG_MODE) echo "The user is in the blocked IP list<br>";
				fclose($gh);
				return (1);
			}
		}
	}
	fclose($gh);

	if(_DEBUG_MODE) echo "The user is NOT in the blocked IP lists<br>";
	return (0);
}


// $imethod Possible options: 
// 1 : Save Visitor`s country code status
// 2 : Save Visitor`s exception status
// 3 : Save Visitor`s blocked status
function anp_save_status($c,$imethod)
{
	global $anp_url_save_method,$anp_cookie_name;

        $prefix ="CC_";
        $dstr ="Visitor`s country code";

        if ($imethod==1)
          $prefix ="CC_";

        if ($imethod==2)
         {
          $prefix ="EX_";
          $dstr ="Visitor`s exception status ";
         }

        if ($imethod==3)
         {
          $dstr ="Visitor`s blocked status ";
          $prefix ="BL_";
         }      
  
	if ("$anp_url_save_method"=="cookie")
	{
		if(_DEBUG_MODE) echo "$dstr '$c' is saved in a COOKIE<br>";
		if(_DEBUG_MODE!="1")
		{
		 if ($imethod==1)
		  setcookie($prefix.$anp_cookie_name,"$c",time()+31536000);
		 else
		  setcookie($prefix.$anp_cookie_name,"$c");
		}

	}
	if ("$anp_url_save_method"=="session")
	{
		if(_DEBUG_MODE) echo "$dstr '$c' is saved in a SESSION var<br>";
		$GLOBALS[$prefix."$anp_cookie_name"]="$c";
		session_unregister($prefix."$anp_cookie_name");
		if(_DEBUG_MODE!="1") session_register($prefix."$anp_cookie_name");
	}
}

function anp_get_cookie()
{
	global $anp_url_save_method,$anp_cookie_name,$HTTP_COOKIE_VARS,$HTTP_SESSION_VARS;
	if ("$anp_url_save_method"=="cookie") $anp_country_code=$HTTP_COOKIE_VARS["CC_"."$anp_cookie_name"];
	if ("$anp_url_save_method"=="session") $anp_country_code=$HTTP_SESSION_VARS["CC_"."$anp_cookie_name"];
	if ("$anp_country_code"=="")  { $anp_country_code="none"; if(_DEBUG_MODE) echo "No country code is saved for current visitor (_DEBUG_MODE=1 prevents cookies to be sent) ; cookie name: $anp_cookie_name<br>";}
	if(_DEBUG_MODE) echo "Country Code for this visitor is loaded from COOKIE/SESSION: '$anp_country_code' <br>";
	return $anp_country_code;
}

function anp_get_exceptions()
{
	global $anp_url_save_method,$anp_cookie_name,$HTTP_COOKIE_VARS,$HTTP_SESSION_VARS;
	if ("$anp_url_save_method"=="cookie") $anp_country_code=$HTTP_COOKIE_VARS["EX_"."$anp_cookie_name"];
	if ("$anp_url_save_method"=="session") $anp_country_code=$HTTP_SESSION_VARS["EX_"."$anp_cookie_name"];
	return $anp_country_code;
}

function anp_get_blocked()
{
	global $anp_url_save_method,$anp_cookie_name,$HTTP_COOKIE_VARS,$HTTP_SESSION_VARS;
	if ("$anp_url_save_method"=="cookie") $anp_country_code=$HTTP_COOKIE_VARS["BL_"."$anp_cookie_name"];
	if ("$anp_url_save_method"=="session") $anp_country_code=$HTTP_SESSION_VARS["BL_"."$anp_cookie_name"];
	return $anp_country_code;
}

?>