<?PHP $anp_path="cr/"; include($anp_path."cr.php"); ?><html>
<head>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red">
<p align="center">&nbsp;</p>
<?PHP include($anp_path."countries.php"); $anp_cinfo=get_cinfo($anp_country_code);?>
<div align="center"><font size="2" face="Arial, Helvetica, sans-serif">Your IP 
  is : <?PHP echo $anp_ip; ?> </font> <br><br>
  Here are the country details as loaded into : 
<br>
  Country Code:  <?php echo $anp_cinfo["code"]; ?><br>
  Country Name: <?php echo $anp_cinfo["name"]; ?><br>
  Region: <?php echo $anp_cinfo["region"]; ?><br>
  Country Capital: <?php echo $anp_cinfo["capital"] ?> <br>
  Currency: <?php echo $anp_cinfo["currency"] ?><br>
  Flag: <img src="<?php echo $anp_cinfo["flag_path"]; ?>" alt="" border="0"><br>
 </div>
<p align="center"><B>The free IP2Country database(73% accurate) included in this distribution has only 172
countries with 37879 records. it`s by far not as accurate and updated as our commercial IP2Country database(98% accurate).</B></p>
<p align="center">[<A HREF="http://www.analysespider.com/order.html">Click here to purchase Commercial IP2Country Database</A>]</p>
</body>

</html>