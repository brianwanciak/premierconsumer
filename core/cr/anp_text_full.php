<?php
/////////////////////////////////////////////////////////////////////////////
//      Target Country by IP Address  - advanced redirection
//		- for MySql, Plain text databases-
//	Copyright (C) 2005 Jgsoft Associates - http://www.analysespider.com/
/////////////////////////////////////////////////////////////////////////////

class TRec
{
var $StartIP;
var $EndIP;
var $Country;
}

class TGeoip
{
var $ip;
var $fp;
var $Rec;
var $DATAFIELDBEGIN= 0x0;
var $RECORDLENGTH;
var $szLocal;
var $szQuery="";

// Check IP and Format IP
function FormatIpNumber($ip)
{
$this->ip= "$ip";

$isize = ($this->RECORDLENGTH -3) / 2;
$n = $isize - strlen($this->ip); 

for ($i=1;$i <= $n;$i++) { 
  $this->ip = "0".$this->ip; 
};

if ($n <0) 
 return -1;
else
 return 0;
}

// read a record from DB
function ReadRec($RecNo)
{
$this->Seek($RecNo);
$buf= fread($this->fp, $this->RECORDLENGTH);
if (strlen($buf) == 0)
{
return 1;
}
$isize = ($this->RECORDLENGTH -3) / 2;

$this->Rec->StartIP= substr($buf, 0, $isize);
$this->Rec->EndIP= substr($buf, $isize, $isize);
$this->Rec->Country= substr($buf, 2 * $isize, 2);
return 0;
}

// Go to Record Number
function Seek($RecNo)
{
return fseek($this->fp, $RecNo * $this->RECORDLENGTH + $this->DATAFIELDBEGIN, SEEK_SET);
}

function searchip($DBFILENAME)
{
$nRet= 0;
$this->Rec= new TRec;

$this->fp= fopen($DBFILENAME, "rb");

if ($this->fp == NULL) {
$this->szLocal= "OpenFileError";
return 1;
}

// Get Record Count
fseek($this->fp, 0, SEEK_END);
$RecordCount= floor((ftell($this->fp) - $this->DATAFIELDBEGIN) / $this->RECORDLENGTH);

if ($RecordCount <= 1)
{
$this->szLocal= "FileDataError";
$nRet= 2;
}
else
{
$RangB= 0;
$RangE= $RecordCount;
// Match ...
while ($RangB < $RangE-1)
{
$RecNo= floor(($RangB + $RangE) / 2);
$this->ReadRec($RecNo);

if (strcmp($this->ip, $this->Rec->StartIP) >=0 && strcmp($this->ip, $this->Rec->EndIP) <=0 )
break; //Found match record

if (strcmp($this->ip, $this->Rec->StartIP) > 0)
$RangB= $RecNo;
else
$RangE= $RecNo;
}

if (!($RangB < $RangE-1))
{
$this->szLocal= "UnknowLocal!";
$nRet= 3;
}
else
{ // Match Success
$this->szLocal= $this->Rec->Country;
}
}
fclose($this->fp);
return $nRet;
}

}

function anp_get_country($ipn)
{
 global $anp_default_reject_link, $anp_path , $anp_text_db_name, $anp_text_db_ip_type;

   $nRet = 1;
   $geoip= new TGeoip;

	if (is_file($anp_path.$anp_text_db_name))
	{
                # Set format :0001111345 + 2345678910 + cn + \n
                $geoip->RECORDLENGTH = 23;   #10 + 10 + 2 + 1;
	 
                if ($geoip->FormatIpNumber($ipn) != 0)
                {  
                 $geoip->szLocal= "InvalidIP";
                 $nRet = -1;
                } 
               else
                {
          	   $nRet = $geoip->searchip($anp_path.$anp_text_db_name);
                }
	}

     if ($nRet == 0)
     {
	$country= $geoip->Rec->Country;
       $geoip->szLocal = "Geting country from text full: '".$country."' <br>";
     }
    else
     {
	$country= "N/A";
       $country= $geoip->Rec->Country;

     }

    if(_DEBUG_MODE) echo $geoip->szLocal;

   return($country);
}
?>