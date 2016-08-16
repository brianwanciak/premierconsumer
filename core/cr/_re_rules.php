<?php

//START Redirection Rules
//Description: You can specify for each country a redirection link (URL), you could place a blank as redirection link (the default redirect link will be used) (use an invalid url to display a 404 error message to the visitor - ex: "index.aphp") or you could comment the line (the default reject link will be used). By default you have a list of all countries but all have blank redirection links and the lines are commented. 
//Here there are 244 country rule lines but the free database included in this distribution has only 179 countries and it`s by far not as accurate and updated as our commercial database, available at http://www.analysespider.com
//Structure: ISO Country code=<Redirection link>;

/*Exemples:
 //CA=http://www.yoursite.com/ca_index.html // Canada  - In this case the visitors from Canada will be redirected to $anp_default_reject_link (because the line is commented) and if $anp_default_reject_link is blank they will be accepted to view your pages
 CA= // Canada  - in this case users from CA will be redirected to $anp_default_redirect_link (because the redirection link for this country is blank)
 CA=http://www.yoursite.com/ca_index.html // Canada  - Here it`s simple, visitors from CA will be redirected to http://www.yoursite.com/ca_index.html
*/

	//REDIRECTION RULES
	//$anp_rr["AD"]=""; // Andorra
	//$anp_rr["AE"]=""; // United Arab Emirates
	//$anp_rr["AF"]=""; // Afghanistan 
	//$anp_rr["AG"]=""; // Antigua and Barbuda 
	//$anp_rr["AI"]=""; // Anguilla 
	//$anp_rr["AL"]=""; // Albania 
	//$anp_rr["AM"]=""; // Armenia 
	//$anp_rr["AN"]=""; // Netherlands Antilles 
	//$anp_rr["AO"]=""; // Angola 
	//$anp_rr["AQ"]=""; // Antarctica 
	//$anp_rr["AR"]=""; // Argentina 
	//$anp_rr["AS"]=""; // American Samoa 
	//$anp_rr["AT"]=""; // Austria 
	//$anp_rr["AU"]=""; // Australia 
	//$anp_rr["AW"]=""; // Aruba 
	//$anp_rr["AZ"]=""; // Azerbaijan 
	//$anp_rr["BA"]=""; // Bosnia and Herzegovina 
	//$anp_rr["BB"]=""; // Barbados 
	//$anp_rr["BD"]=""; // Bangladesh 
	//$anp_rr["BE"]=""; // Belgium 
	//$anp_rr["BF"]=""; // Burkina Faso 
	//$anp_rr["BG"]=""; // Bulgaria 
	//$anp_rr["BH"]=""; // Bahrain 
	//$anp_rr["BI"]=""; // Burundi 
	//$anp_rr["BJ"]=""; // Benin 
	//$anp_rr["BM"]=""; // Bermuda 
	//$anp_rr["BN"]=""; // Brunei Darussalam 
	//$anp_rr["BO"]=""; // Bolivia 
	//$anp_rr["BR"]=""; // Brazil 
	//$anp_rr["BS"]=""; // The Bahamas 
	//$anp_rr["BT"]=""; // Bhutan 
	//$anp_rr["BV"]=""; // Bouvet Island 
	//$anp_rr["BW"]=""; // Botswana 
	//$anp_rr["BY"]=""; // Belarus 
	//$anp_rr["BZ"]=""; // Belize 
	//$anp_rr["CA"]=""; // Canada 
	//$anp_rr["CC"]=""; // Cocos (Keeling) Islands 
	//$anp_rr["CD"]=""; // Congo, Democratic Republic of the 
	//$anp_rr["CF"]=""; // Central African Republic 
	//$anp_rr["CG"]=""; // Congo, Republic of the 
	//$anp_rr["CH"]=""; // Switzerland 
	//$anp_rr["CI"]=""; // Cote d'Ivoire 
	//$anp_rr["CK"]=""; // Cook Islands 
	//$anp_rr["CL"]=""; // Chile 
	//$anp_rr["CM"]=""; // Cameroon 
	//$anp_rr["CN"]=""; // China 
	//$anp_rr["CO"]=""; // Colombia 
	//$anp_rr["CR"]=""; // Costa Rica 
	//$anp_rr["CU"]=""; // Cuba 
	//$anp_rr["CV"]=""; // Cape Verde 
	//$anp_rr["CX"]=""; // Christmas Island 
	//$anp_rr["CY"]=""; // Cyprus 
	//$anp_rr["CZ"]=""; // Czech Republic 
	//$anp_rr["DE"]=""; // Germany 
	//$anp_rr["DJ"]=""; // Djibouti 
	//$anp_rr["DK"]=""; // Denmark 
	//$anp_rr["DM"]=""; // Dominica 
	//$anp_rr["DO"]=""; // Dominican Republic 
	//$anp_rr["DZ"]=""; // Algeria 
	//$anp_rr["EC"]=""; // Ecuador 
	//$anp_rr["EE"]=""; // Estonia 
	//$anp_rr["EG"]=""; // Egypt 
	//$anp_rr["EH"]=""; // Western Sahara 
	//$anp_rr["ER"]=""; // Eritrea 
	//$anp_rr["ES"]=""; // Spain 
	//$anp_rr["ET"]=""; // Ethiopia 
	//$anp_rr["FI"]=""; // Finland 
	//$anp_rr["FJ"]=""; // Fiji 
	//$anp_rr["FK"]=""; // Falkland Islands (Islas Malvinas) 
	//$anp_rr["FM"]=""; // Micronesia, Federated States of 
	//$anp_rr["FO"]=""; // Faroe Islands 
	//$anp_rr["FR"]=""; // France 
	//$anp_rr["FX"]=""; // France, Metropolitan 
	//$anp_rr["GA"]=""; // Gabon 
	//$anp_rr["GD"]=""; // Grenada 
	//$anp_rr["GE"]=""; // Georgia 
	//$anp_rr["GF"]=""; // French Guiana 
	//$anp_rr["GG"]=""; // Guernsey 
	//$anp_rr["GH"]=""; // Ghana 
	//$anp_rr["GI"]=""; // Gibraltar 
	//$anp_rr["GL"]=""; // Greenland 
	//$anp_rr["GM"]=""; // The Gambia 
	//$anp_rr["GN"]=""; // Guinea 
	//$anp_rr["GP"]=""; // Guadeloupe 
	//$anp_rr["GQ"]=""; // Equatorial Guinea 
	//$anp_rr["GR"]=""; // Greece 
	//$anp_rr["GS"]=""; // South Georgia and the South Sandwich Islands 
	//$anp_rr["GT"]=""; // Guatemala 
	//$anp_rr["GU"]=""; // Guam 
	//$anp_rr["GW"]=""; // Guinea-Bissau 
	//$anp_rr["GY"]=""; // Guyana 
	//$anp_rr["HK"]=""; // Hong Kong (SAR) 
	//$anp_rr["HM"]=""; // Heard Island and McDonald Islands 
	//$anp_rr["HN"]=""; // Honduras 
	//$anp_rr["HR"]=""; // Croatia 
	//$anp_rr["HT"]=""; // Haiti 
	//$anp_rr["HU"]=""; // Hungary 
	//$anp_rr["ID"]=""; // Indonesia 
	//$anp_rr["IE"]=""; // Ireland 
	//$anp_rr["IL"]=""; // Israel 
	//$anp_rr["IM"]=""; // Man, Isle of 
	//$anp_rr["IN"]=""; // India 
	//$anp_rr["IO"]=""; // British Indian Ocean Territory 
	//$anp_rr["IQ"]=""; // Iraq 
	//$anp_rr["IR"]=""; // Iran 
	//$anp_rr["IS"]=""; // Iceland 
	//$anp_rr["IT"]=""; // Italy 
	//$anp_rr["JE"]=""; // Jersey 
	//$anp_rr["JM"]=""; // Jamaica 
	//$anp_rr["JO"]=""; // Jordan 
	//$anp_rr["JP"]=""; // Japan 
	//$anp_rr["KE"]=""; // Kenya 
	//$anp_rr["KG"]=""; // Kyrgyzstan 
	//$anp_rr["KH"]=""; // Cambodia 
	//$anp_rr["KI"]=""; // Kiribati 
	//$anp_rr["KM"]=""; // Comoros 
	//$anp_rr["KN"]=""; // Saint Kitts and Nevis 
	//$anp_rr["KP"]=""; // Korea, North 
	//$anp_rr["KR"]=""; // Korea, South 
	//$anp_rr["KW"]=""; // Kuwait 
	//$anp_rr["KY"]=""; // Cayman Islands 
	//$anp_rr["KZ"]=""; // Kazakhstan 
	//$anp_rr["LA"]=""; // Laos 
	//$anp_rr["LB"]=""; // Lebanon 
	//$anp_rr["LC"]=""; // Saint Lucia 
	//$anp_rr["LI"]=""; // Liechtenstein 
	//$anp_rr["LK"]=""; // Sri Lanka 
	//$anp_rr["LR"]=""; // Liberia 
	//$anp_rr["LS"]=""; // Lesotho 
	//$anp_rr["LT"]=""; // Lithuania 
	//$anp_rr["LU"]=""; // Luxembourg 
	//$anp_rr["LV"]=""; // Latvia 
	//$anp_rr["LY"]=""; // Libya 
	//$anp_rr["MA"]=""; // Morocco 
	//$anp_rr["MC"]=""; // Monaco 
	//$anp_rr["MD"]=""; // Moldova 
	//$anp_rr["MG"]=""; // Madagascar 
	//$anp_rr["MH"]=""; // Marshall Islands 
	//$anp_rr["MK"]=""; // Macedonia, The Former Yugoslav Republic of 
	//$anp_rr["ML"]=""; // Mali 
	//$anp_rr["MM"]=""; // Burma 
	//$anp_rr["MN"]=""; // Mongolia 
	//$anp_rr["MO"]=""; // Macao 
	//$anp_rr["MP"]=""; // Northern Mariana Islands 
	//$anp_rr["MQ"]=""; // Martinique 
	//$anp_rr["MR"]=""; // Mauritania 
	//$anp_rr["MS"]=""; // Montserrat 
	//$anp_rr["MT"]=""; // Malta 
	//$anp_rr["MU"]=""; // Mauritius 
	//$anp_rr["MV"]=""; // Maldives 
	//$anp_rr["MW"]=""; // Malawi 
	//$anp_rr["MX"]=""; // Mexico 
	//$anp_rr["MY"]=""; // Malaysia 
	//$anp_rr["MZ"]=""; // Mozambique 
	//$anp_rr["NA"]=""; // Namibia 
	//$anp_rr["NC"]=""; // New Caledonia 
	//$anp_rr["NE"]=""; // Niger 
	//$anp_rr["NF"]=""; // Norfolk Island 
	//$anp_rr["NG"]=""; // Nigeria 
	//$anp_rr["NI"]=""; // Nicaragua 
	//$anp_rr["NL"]=""; // Netherlands 
	//$anp_rr["NO"]=""; // Norway 
	//$anp_rr["NP"]=""; // Nepal 
	//$anp_rr["NR"]=""; // Nauru 
	//$anp_rr["NU"]=""; // Niue 
	//$anp_rr["NZ"]=""; // New Zealand 
	//$anp_rr["OM"]=""; // Oman 
	//$anp_rr["PA"]=""; // Panama 
	//$anp_rr["PE"]=""; // Peru 
	//$anp_rr["PF"]=""; // French Polynesia 
	//$anp_rr["PG"]=""; // Papua New Guinea 
	//$anp_rr["PH"]=""; // Philippines 
	//$anp_rr["PK"]=""; // Pakistan 
	//$anp_rr["PL"]=""; // Poland 
	//$anp_rr["PM"]=""; // Saint Pierre and Miquelon 
	//$anp_rr["PN"]=""; // Pitcairn Islands 
	//$anp_rr["PR"]=""; // Puerto Rico 
	//$anp_rr["PS"]=""; // Palestinian Territory, Occupied 
	//$anp_rr["PT"]=""; // Portugal 
	//$anp_rr["PW"]=""; // Palau 
	//$anp_rr["PX"]=""; // Proxy Server 
	//$anp_rr["PY"]=""; // Paraguay 
	//$anp_rr["QA"]=""; // Qatar 
	//$anp_rr["RE"]=""; // R�union 
	//$anp_rr["RO"]=""; // Romania 
	//$anp_rr["RU"]=""; // Russia 
	//$anp_rr["RW"]=""; // Rwanda 
	//$anp_rr["SA"]=""; // Saudi Arabia 
	//$anp_rr["SB"]=""; // Solomon Islands 
	//$anp_rr["SC"]=""; // Seychelles 
	//$anp_rr["SD"]=""; // Sudan 
	//$anp_rr["SE"]=""; // Sweden 
	//$anp_rr["SG"]=""; // Singapore 
	//$anp_rr["SH"]=""; // Saint Helena 
	//$anp_rr["SI"]=""; // Slovenia 
	//$anp_rr["SJ"]=""; // Svalbard 
	//$anp_rr["SK"]=""; // Slovakia 
	//$anp_rr["SL"]=""; // Sierra Leone 
	//$anp_rr["SM"]=""; // San Marino 
	//$anp_rr["SN"]=""; // Senegal 
	//$anp_rr["SO"]=""; // Somalia 
	//$anp_rr["SR"]=""; // Suriname 
	//$anp_rr["ST"]=""; // S�o Tom?and Pr�ncipe 
	//$anp_rr["SV"]=""; // El Salvador 
	//$anp_rr["SY"]=""; // Syria 
	//$anp_rr["SZ"]=""; // Swaziland 
	//$anp_rr["TC"]=""; // Turks and Caicos Islands 
	//$anp_rr["TD"]=""; // Chad 
	//$anp_rr["TF"]=""; // French Southern and Antarctic Lands 
	//$anp_rr["TG"]=""; // Togo 
	//$anp_rr["TH"]=""; // Thailand 
	//$anp_rr["TJ"]=""; // Tajikistan 
	//$anp_rr["TK"]=""; // Tokelau 
	//$anp_rr["TM"]=""; // Turkmenistan 
	//$anp_rr["TN"]=""; // Tunisia 
	//$anp_rr["TO"]=""; // Tonga 
	//$anp_rr["TP"]=""; // East Timor 
	//$anp_rr["TR"]=""; // Turkey 
	//$anp_rr["TT"]=""; // Trinidad and Tobago 
	//$anp_rr["TV"]=""; // Tuvalu 
	//$anp_rr["TW"]=""; // Taiwan 
	//$anp_rr["TZ"]=""; // Tanzania 
	//$anp_rr["UA"]=""; // Ukraine 
	//$anp_rr["UG"]=""; // Uganda 
	//$anp_rr["UK"]=""; // United Kingdom 
	//$anp_rr["UM"]=""; // United States Minor Outlying Islands 
	//$anp_rr["US"]=""; // United States 
	//$anp_rr["UY"]=""; // Uruguay 
	//$anp_rr["UZ"]=""; // Uzbekistan 
	//$anp_rr["VA"]=""; // Holy See (Vatican City) 
	//$anp_rr["VC"]=""; // Saint Vincent and the Grenadines 
	//$anp_rr["VE"]=""; // Venezuela 
	//$anp_rr["VG"]=""; // British Virgin Islands 
	//$anp_rr["VI"]=""; // Virgin Islands 
	//$anp_rr["VN"]=""; // Vietnam 
	//$anp_rr["VU"]=""; // Vanuatu 
	//$anp_rr["WF"]=""; // Wallis and Futuna 
	//$anp_rr["WS"]=""; // Samoa 
	//$anp_rr["YE"]=""; // Yemen 
	//$anp_rr["YT"]=""; // Mayotte 
	//$anp_rr["YU"]=""; // Yugoslavia 
	//$anp_rr["ZA"]=""; // South Africa 
	//$anp_rr["ZM"]=""; // Zambia 
	//$anp_rr["ZW"]=""; // Zimbabwe 
?>