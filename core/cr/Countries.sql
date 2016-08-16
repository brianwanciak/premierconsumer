# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Sep 23, 2003 at 04:42 PM
# Server version: 4.0.10
# PHP Version: 4.3.0
# 
# Database : `ip2`
# 

# --------------------------------------------------------

#
# Table structure for table `_countries`
#

DROP TABLE IF EXISTS `_countries`;
CREATE TABLE `_countries` (
  `ISO_Code` varchar(2) NOT NULL default '',
  `Country` varchar(255) NOT NULL default '',
  `Region` varchar(255) NOT NULL default '',
  `Capital` varchar(255) NOT NULL default '',
  `Currency` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ISO_Code`)
) TYPE=MyISAM;

#
# Dumping data for table `_countries`
#

INSERT INTO `_countries` VALUES ('AD', 'Andorra', 'Europe', 'Andorra la Vella', 'Euro');
INSERT INTO `_countries` VALUES ('AE', 'United Arab Emirates', 'Middle East', 'Abu Dhabi', 'UAE Dirham');
INSERT INTO `_countries` VALUES ('AF', 'Afghanistan', 'Asia', 'Kabul', 'Afghani');
INSERT INTO `_countries` VALUES ('AG', 'Antigua and Barbuda', 'Central America and the Caribbean', 'Saint John\'s', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('AI', 'Anguilla', 'Central America and the Caribbean', 'The Valley', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('AL', 'Albania', 'Europe', 'Tirana', 'Lek');
INSERT INTO `_countries` VALUES ('AM', 'Armenia', 'Commonwealth of Independent States', 'Yerevan', 'Armenian Dram');
INSERT INTO `_countries` VALUES ('AN', 'Netherlands Antilles', 'Central America and the Caribbean', 'Willemstad', 'Netherlands Antillean guilder');
INSERT INTO `_countries` VALUES ('AO', 'Angola', 'Africa', 'Luanda', 'Kwanza');
INSERT INTO `_countries` VALUES ('AQ', 'Antarctica', 'Antarctic Region', '--', '');
INSERT INTO `_countries` VALUES ('AR', 'Argentina', 'South America', 'Buenos Aires', 'Argentine Peso');
INSERT INTO `_countries` VALUES ('AS', 'American Samoa', 'Oceania', 'Pago Pago', 'US Dollar');
INSERT INTO `_countries` VALUES ('AT', 'Austria', 'Europe', 'Vienna', 'Euro');
INSERT INTO `_countries` VALUES ('AU', 'Australia', 'Oceania', 'Canberra', 'Australian dollar');
INSERT INTO `_countries` VALUES ('AW', 'Aruba', 'Central America and the Caribbean', 'Oranjestad', 'Aruban Guilder');
INSERT INTO `_countries` VALUES ('AZ', 'Azerbaijan', 'Commonwealth of Independent States', 'Baku (Baki)', 'Azerbaijani Manat');
INSERT INTO `_countries` VALUES ('BA', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina, Europe', 'Sarajevo', 'Convertible Marka');
INSERT INTO `_countries` VALUES ('BB', 'Barbados', 'Central America and the Caribbean', 'Bridgetown', 'Barbados Dollar');
INSERT INTO `_countries` VALUES ('BD', 'Bangladesh', 'Asia', 'Dhaka', 'Taka');
INSERT INTO `_countries` VALUES ('BE', 'Belgium', 'Europe', 'Brussels', 'Euro');
INSERT INTO `_countries` VALUES ('BF', 'Burkina Faso', 'Africa', 'Ouagadougou', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('BG', 'Bulgaria', 'Europe', 'Sofia', 'Lev');
INSERT INTO `_countries` VALUES ('BH', 'Bahrain', 'Middle East', 'Manama', 'Bahraini Dinar');
INSERT INTO `_countries` VALUES ('BI', 'Burundi', 'Africa', 'Bujumbura', 'Burundi Franc');
INSERT INTO `_countries` VALUES ('BJ', 'Benin', 'Africa', 'Porto-Novo', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('BM', 'Bermuda', 'North America', 'Hamilton', 'Bermudian Dollar');
INSERT INTO `_countries` VALUES ('BN', 'Brunei Darussalam', 'Southeast Asia', 'Bandar Seri Begawan', 'Brunei Dollar');
INSERT INTO `_countries` VALUES ('BO', 'Bolivia', 'South America', 'La Paz /Sucre', 'Boliviano');
INSERT INTO `_countries` VALUES ('BR', 'Brazil', 'South America', 'Brasilia', 'Brazilian Real');
INSERT INTO `_countries` VALUES ('BS', 'The Bahamas', 'Central America and the Caribbean', 'Nassau', 'Bahamian Dollar');
INSERT INTO `_countries` VALUES ('BT', 'Bhutan', 'Asia', 'Thimphu', 'Ngultrum');
INSERT INTO `_countries` VALUES ('BV', 'Bouvet Island', 'Antarctic Region', '--', 'Norwegian Krone');
INSERT INTO `_countries` VALUES ('BW', 'Botswana', 'Africa', 'Gaborone', 'Pula');
INSERT INTO `_countries` VALUES ('BY', 'Belarus', 'Commonwealth of Independent States', 'Minsk', 'Belarussian Ruble');
INSERT INTO `_countries` VALUES ('BZ', 'Belize', 'Central America and the Caribbean', 'Belmopan', 'Belize Dollar');
INSERT INTO `_countries` VALUES ('CA', 'Canada', 'North America', 'Ottawa', 'Canadian Dollar');
INSERT INTO `_countries` VALUES ('CC', 'Cocos (Keeling) Islands', 'Southeast Asia', 'West Island', 'Australian Dollar');
INSERT INTO `_countries` VALUES ('CD', 'Congo, Democratic Republic of the', 'Africa', 'Kinshasa', 'Franc Congolais');
INSERT INTO `_countries` VALUES ('CF', 'Central African Republic', 'Africa', 'Bangui', 'CFA Franc BEAC');
INSERT INTO `_countries` VALUES ('CG', 'Congo, Republic of the', 'Africa', 'Brazzaville', 'CFA Franc BEAC');
INSERT INTO `_countries` VALUES ('CH', 'Switzerland', 'Europe', 'Bern', 'Swiss Franc');
INSERT INTO `_countries` VALUES ('CI', 'Cote d\'Ivoire', 'Africa', 'Yamoussoukro', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('CK', 'Cook Islands', 'Oceania', 'Avarua', 'New Zealand Dollar');
INSERT INTO `_countries` VALUES ('CL', 'Chile', 'South America', 'Santiago', 'Chilean Peso');
INSERT INTO `_countries` VALUES ('CM', 'Cameroon', 'Africa', 'Yaounde', 'CFA Franc BEAC');
INSERT INTO `_countries` VALUES ('CN', 'China', 'Asia', 'Beijing', 'Yuan Renminbi');
INSERT INTO `_countries` VALUES ('CO', 'Colombia', 'South America, Central America and the Caribbean', 'Bogota', 'Colombian Peso');
INSERT INTO `_countries` VALUES ('CR', 'Costa Rica', 'Central America and the Caribbean', 'San Jose', 'Costa Rican Colon');
INSERT INTO `_countries` VALUES ('CU', 'Cuba', 'Central America and the Caribbean', 'Havana', 'Cuban Peso');
INSERT INTO `_countries` VALUES ('CV', 'Cape Verde', 'World', 'Praia', 'Cape Verdean Escudo');
INSERT INTO `_countries` VALUES ('CX', 'Christmas Island', 'Southeast Asia', 'The Settlement', 'Australian Dollar');
INSERT INTO `_countries` VALUES ('CY', 'Cyprus', 'Middle East', 'Nicosia', 'Cyprus Pound');
INSERT INTO `_countries` VALUES ('CZ', 'Czech Republic', 'Europe', 'Prague', 'Czech Koruna');
INSERT INTO `_countries` VALUES ('DE', 'Germany', 'Europe', 'Berlin', 'Euro');
INSERT INTO `_countries` VALUES ('DJ', 'Djibouti', 'Africa', 'Djibouti', 'Djibouti Franc');
INSERT INTO `_countries` VALUES ('DK', 'Denmark', 'Europe', 'Copenhagen', 'Danish Krone');
INSERT INTO `_countries` VALUES ('DM', 'Dominica', 'Central America and the Caribbean', 'Roseau', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('DO', 'Dominican Republic', 'Central America and the Caribbean', 'Santo Domingo', 'Dominican Peso');
INSERT INTO `_countries` VALUES ('DZ', 'Algeria', 'Africa', 'Algiers', 'Algerian Dinar');
INSERT INTO `_countries` VALUES ('EC', 'Ecuador', 'South America', 'Quito', 'US dollar');
INSERT INTO `_countries` VALUES ('EE', 'Estonia', 'Europe', 'Tallinn', 'Kroon');
INSERT INTO `_countries` VALUES ('EG', 'Egypt', 'Africa', 'Cairo', 'Egyptian Pound');
INSERT INTO `_countries` VALUES ('EH', 'Western Sahara', 'Africa', '--', 'Moroccan Dirham');
INSERT INTO `_countries` VALUES ('ER', 'Eritrea', 'Africa', 'Asmara', 'Nakfa');
INSERT INTO `_countries` VALUES ('ES', 'Spain', 'Europe', 'Madrid', 'Euro');
INSERT INTO `_countries` VALUES ('ET', 'Ethiopia', 'Africa', 'Addis Ababa', 'Ethiopian Birr');
INSERT INTO `_countries` VALUES ('FI', 'Finland', 'Europe', 'Helsinki', 'Euro');
INSERT INTO `_countries` VALUES ('FJ', 'Fiji', 'Oceania', 'Suva', 'Fijian Dollar');
INSERT INTO `_countries` VALUES ('FK', 'Falkland Islands (Islas Malvinas)', 'South America', 'Stanley', 'Falkland Islands Pound');
INSERT INTO `_countries` VALUES ('FM', 'Micronesia, Federated States of', 'Oceania', 'Palikir', 'US dollar');
INSERT INTO `_countries` VALUES ('FO', 'Faroe Islands', 'Europe', 'Torshavn', 'Danish Krone');
INSERT INTO `_countries` VALUES ('FR', 'France', 'Europe', 'Paris', 'Euro');
INSERT INTO `_countries` VALUES ('FX', 'France, Metropolitan', '', '--', 'Euro');
INSERT INTO `_countries` VALUES ('GA', 'Gabon', 'Africa', 'Libreville', 'CFA Franc BEAC');
INSERT INTO `_countries` VALUES ('GD', 'Grenada', 'Central America and the Caribbean', 'Saint George\'s', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('GE', 'Georgia', 'Commonwealth of Independent States', 'T\'bilisi', 'Lari');
INSERT INTO `_countries` VALUES ('GF', 'French Guiana', 'South America', 'Cayenne', 'Euro');
INSERT INTO `_countries` VALUES ('GG', 'Guernsey', 'Europe', 'Saint Peter Port', 'Pound Sterling');
INSERT INTO `_countries` VALUES ('GH', 'Ghana', 'Africa', 'Accra', 'Cedi');
INSERT INTO `_countries` VALUES ('GI', 'Gibraltar', 'Europe', 'Gibraltar', 'Gibraltar Pound');
INSERT INTO `_countries` VALUES ('GL', 'Greenland', 'Arctic Region', 'Nuuk', 'Danish Krone');
INSERT INTO `_countries` VALUES ('GM', 'The Gambia', 'Africa', 'Banjul', 'Dalasi');
INSERT INTO `_countries` VALUES ('GN', 'Guinea', 'Africa', 'Conakry', 'Guinean Franc');
INSERT INTO `_countries` VALUES ('GP', 'Guadeloupe', 'Central America and the Caribbean', 'Basse-Terre', 'Euro');
INSERT INTO `_countries` VALUES ('GQ', 'Equatorial Guinea', 'Africa', 'Malabo', 'CFA Franc BEAC');
INSERT INTO `_countries` VALUES ('GR', 'Greece', 'Europe', 'Athens', 'Euro');
INSERT INTO `_countries` VALUES ('GS', 'South Georgia and the South Sandwich Islands', 'Antarctic Region', '--', 'Pound Sterling');
INSERT INTO `_countries` VALUES ('GT', 'Guatemala', 'Central America and the Caribbean', 'Guatemala', 'Quetzal');
INSERT INTO `_countries` VALUES ('GU', 'Guam', 'Oceania', 'Hagatna', 'US Dollar');
INSERT INTO `_countries` VALUES ('GW', 'Guinea-Bissau', 'Africa', 'Bissau', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('GY', 'Guyana', 'South America', 'Georgetown', 'Guyana Dollar');
INSERT INTO `_countries` VALUES ('HK', 'Hong Kong (SAR)', 'Southeast Asia', 'Hong Kong', 'Hong Kong Dollar');
INSERT INTO `_countries` VALUES ('HM', 'Heard Island and McDonald Islands', 'Antarctic Region', '--', 'Australian Dollar');
INSERT INTO `_countries` VALUES ('HN', 'Honduras', 'Central America and the Caribbean', 'Tegucigalpa', 'Lempira');
INSERT INTO `_countries` VALUES ('HR', 'Croatia', 'Europe', 'Zagreb', 'Kuna');
INSERT INTO `_countries` VALUES ('HT', 'Haiti', 'Central America and the Caribbean', 'Port-au-Prince', 'Gourde');
INSERT INTO `_countries` VALUES ('HU', 'Hungary', 'Europe', 'Budapest', 'Forint');
INSERT INTO `_countries` VALUES ('ID', 'Indonesia', 'Southeast Asia', 'Jakarta', 'Rupiah');
INSERT INTO `_countries` VALUES ('IE', 'Ireland', 'Europe', 'Dublin', 'Euro');
INSERT INTO `_countries` VALUES ('IL', 'Israel', 'Middle East', 'Jerusalem', 'New Israeli Sheqel');
INSERT INTO `_countries` VALUES ('IM', 'Man, Isle of', 'Europe', 'Douglas', 'Pound Sterling');
INSERT INTO `_countries` VALUES ('IN', 'India', 'Asia', 'New Delhi', 'Indian Rupee');
INSERT INTO `_countries` VALUES ('IO', 'British Indian Ocean Territory', 'World', '--', 'US Dollar');
INSERT INTO `_countries` VALUES ('IQ', 'Iraq', 'Middle East', 'Baghdad', 'Iraqi Dinar');
INSERT INTO `_countries` VALUES ('IR', 'Iran', 'Middle East', 'Tehran', 'Iranian Rial');
INSERT INTO `_countries` VALUES ('IS', 'Iceland', 'Arctic Region', 'Reykjavik', 'Iceland Krona');
INSERT INTO `_countries` VALUES ('IT', 'Italy', 'Europe', 'Rome', 'Euro');
INSERT INTO `_countries` VALUES ('JE', 'Jersey', 'Europe', 'Saint Helier', 'Pound Sterling');
INSERT INTO `_countries` VALUES ('JM', 'Jamaica', 'Central America and the Caribbean', 'Kingston', 'Jamaican dollar');
INSERT INTO `_countries` VALUES ('JO', 'Jordan', 'Middle East', 'Amman', 'Jordanian Dinar');
INSERT INTO `_countries` VALUES ('JP', 'Japan', 'Asia', 'Tokyo', 'Yen');
INSERT INTO `_countries` VALUES ('KE', 'Kenya', 'Africa', 'Nairobi', 'Kenyan shilling');
INSERT INTO `_countries` VALUES ('KG', 'Kyrgyzstan', 'Commonwealth of Independent States', 'Bishkek', 'Som');
INSERT INTO `_countries` VALUES ('KH', 'Cambodia', 'Southeast Asia', 'Phnom Penh', 'Riel');
INSERT INTO `_countries` VALUES ('KI', 'Kiribati', 'Oceania', 'Tarawa', 'Australian dollar');
INSERT INTO `_countries` VALUES ('KM', 'Comoros', 'Africa', 'Moroni', 'Comoro Franc');
INSERT INTO `_countries` VALUES ('KN', 'Saint Kitts and Nevis', 'Central America and the Caribbean', 'Basseterre', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('KP', 'Korea, North', 'Asia', 'P\'yongyang', 'North Korean Won');
INSERT INTO `_countries` VALUES ('KR', 'Korea, South', 'Asia', 'Seoul', 'Won');
INSERT INTO `_countries` VALUES ('KW', 'Kuwait', 'Middle East', 'Kuwait', 'Kuwaiti Dinar');
INSERT INTO `_countries` VALUES ('KY', 'Cayman Islands', 'Central America and the Caribbean', 'George Town', 'Cayman Islands Dollar');
INSERT INTO `_countries` VALUES ('KZ', 'Kazakhstan', 'Commonwealth of Independent States', 'Astana', 'Tenge');
INSERT INTO `_countries` VALUES ('LA', 'Laos', 'Southeast Asia', 'Vientiane', 'Kip');
INSERT INTO `_countries` VALUES ('LB', 'Lebanon', 'Middle East', 'Beirut', 'Lebanese Pound');
INSERT INTO `_countries` VALUES ('LC', 'Saint Lucia', 'Central America and the Caribbean', 'Castries', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('LI', 'Liechtenstein', 'Europe', 'Vaduz', 'Swiss Franc');
INSERT INTO `_countries` VALUES ('LK', 'Sri Lanka', 'Asia', 'Colombo', 'Sri Lanka Rupee');
INSERT INTO `_countries` VALUES ('LR', 'Liberia', 'Africa', 'Monrovia', 'Liberian Dollar');
INSERT INTO `_countries` VALUES ('LS', 'Lesotho', 'Africa', 'Maseru', 'Loti');
INSERT INTO `_countries` VALUES ('LT', 'Lithuania', 'Europe', 'Vilnius', 'Lithuanian Litas');
INSERT INTO `_countries` VALUES ('LU', 'Luxembourg', 'Europe', 'Luxembourg', 'Euro');
INSERT INTO `_countries` VALUES ('LV', 'Latvia', 'Europe', 'Riga', 'Latvian Lats');
INSERT INTO `_countries` VALUES ('LY', 'Libya', 'Africa', 'Tripoli', 'Libyan Dinar');
INSERT INTO `_countries` VALUES ('MA', 'Morocco', 'Africa', 'Rabat', 'Moroccan Dirham');
INSERT INTO `_countries` VALUES ('MC', 'Monaco', 'Europe', 'Monaco', 'Euro');
INSERT INTO `_countries` VALUES ('MD', 'Moldova', 'Commonwealth of Independent States', 'Chisinau', 'Moldovan Leu');
INSERT INTO `_countries` VALUES ('MG', 'Madagascar', 'Africa', 'Antananarivo', 'Malagasy Franc');
INSERT INTO `_countries` VALUES ('MH', 'Marshall Islands', 'Oceania', 'Majuro', 'US dollar');
INSERT INTO `_countries` VALUES ('MK', 'Macedonia, The Former Yugoslav Republic of', 'Europe', 'Skopje', 'Denar');
INSERT INTO `_countries` VALUES ('ML', 'Mali', 'Africa', 'Bamako', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('MM', 'Burma', 'Southeast Asia', 'Rangoon', 'kyat');
INSERT INTO `_countries` VALUES ('MN', 'Mongolia', 'Asia', 'Ulaanbaatar', 'Tugrik');
INSERT INTO `_countries` VALUES ('MO', 'Macao', 'Southeast Asia', 'Macao', 'Pataca');
INSERT INTO `_countries` VALUES ('MP', 'Northern Mariana Islands', 'Oceania', 'Saipan', 'US Dollar');
INSERT INTO `_countries` VALUES ('MQ', 'Martinique', 'Central America and the Caribbean', 'Fort-de-France', 'Euro');
INSERT INTO `_countries` VALUES ('MR', 'Mauritania', 'Africa', 'Nouakchott', 'Ouguiya');
INSERT INTO `_countries` VALUES ('MS', 'Montserrat', 'Central America and the Caribbean', 'Plymouth', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('MT', 'Malta', 'Europe', 'Valletta', 'Maltese Lira');
INSERT INTO `_countries` VALUES ('MU', 'Mauritius', 'World', 'Port Louis', 'Mauritius Rupee');
INSERT INTO `_countries` VALUES ('MV', 'Maldives', 'Asia', 'Male', 'Rufiyaa');
INSERT INTO `_countries` VALUES ('MW', 'Malawi', 'Africa', 'Lilongwe', 'Kwacha');
INSERT INTO `_countries` VALUES ('MX', 'Mexico', 'North America', 'Mexico', 'Mexican Peso');
INSERT INTO `_countries` VALUES ('MY', 'Malaysia', 'Southeast Asia', 'Kuala Lumpur', 'Malaysian Ringgit');
INSERT INTO `_countries` VALUES ('MZ', 'Mozambique', 'Africa', 'Maputo', 'Metical');
INSERT INTO `_countries` VALUES ('NA', 'Namibia', 'Africa', 'Windhoek', 'Namibian Dollar');
INSERT INTO `_countries` VALUES ('NC', 'New Caledonia', 'Oceania', 'Noumea', 'CFP Franc');
INSERT INTO `_countries` VALUES ('NE', 'Niger', 'Africa', 'Niamey', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('NF', 'Norfolk Island', 'Oceania', 'Kingston', 'Australian Dollar');
INSERT INTO `_countries` VALUES ('NG', 'Nigeria', 'Africa', 'Abuja', 'Naira');
INSERT INTO `_countries` VALUES ('NI', 'Nicaragua', 'Central America and the Caribbean', 'Managua', 'Cordoba Oro');
INSERT INTO `_countries` VALUES ('NL', 'Netherlands', 'Europe', 'Amsterdam', 'Euro');
INSERT INTO `_countries` VALUES ('NO', 'Norway', 'Europe', 'Oslo', 'Norwegian Krone');
INSERT INTO `_countries` VALUES ('NP', 'Nepal', 'Asia', 'Kathmandu', 'Nepalese Rupee');
INSERT INTO `_countries` VALUES ('NR', 'Nauru', 'Oceania', '--', 'Australian Dollar');
INSERT INTO `_countries` VALUES ('NU', 'Niue', 'Oceania', 'Alofi', 'New Zealand Dollar');
INSERT INTO `_countries` VALUES ('NZ', 'New Zealand', 'Oceania', 'Wellington', 'New Zealand Dollar');
INSERT INTO `_countries` VALUES ('OM', 'Oman', 'Middle East', 'Muscat', 'Rial Omani');
INSERT INTO `_countries` VALUES ('PA', 'Panama', 'Central America and the Caribbean', 'Panama', 'balboa');
INSERT INTO `_countries` VALUES ('PE', 'Peru', 'South America', 'Lima', 'Nuevo Sol');
INSERT INTO `_countries` VALUES ('PF', 'French Polynesia', 'Oceania', 'Papeete', 'CFP Franc');
INSERT INTO `_countries` VALUES ('PG', 'Papua New Guinea', 'Oceania', 'Port Moresby', 'Kina');
INSERT INTO `_countries` VALUES ('PH', 'Philippines', 'Southeast Asia', 'Manila', 'Philippine Peso');
INSERT INTO `_countries` VALUES ('PK', 'Pakistan', 'Asia', 'Islamabad', 'Pakistan Rupee');
INSERT INTO `_countries` VALUES ('PL', 'Poland', 'Europe', 'Warsaw', 'Zloty');
INSERT INTO `_countries` VALUES ('PM', 'Saint Pierre and Miquelon', 'North America', 'Saint-Pierre', 'Euro');
INSERT INTO `_countries` VALUES ('PN', 'Pitcairn Islands', 'Oceania', 'Adamstown', 'New Zealand Dollar');
INSERT INTO `_countries` VALUES ('PR', 'Puerto Rico', 'Central America and the Caribbean', 'San Juan', 'US dollar');
INSERT INTO `_countries` VALUES ('PS', 'Palestinian Territory, Occupied', '', '--', '');
INSERT INTO `_countries` VALUES ('PT', 'Portugal', 'Europe', 'Lisbon', 'Euro');
INSERT INTO `_countries` VALUES ('PW', 'Palau', 'Oceania', 'Koror', 'US dollar');
INSERT INTO `_countries` VALUES ('PY', 'Paraguay', 'South America', 'Asuncion', 'Guarani');
INSERT INTO `_countries` VALUES ('QA', 'Qatar', 'Middle East', 'Doha', 'Qatari Rial');
INSERT INTO `_countries` VALUES ('RE', 'Réunion', 'World', 'Saint-Denis', 'Euro');
INSERT INTO `_countries` VALUES ('RO', 'Romania', 'Europe', 'Bucharest', 'Leu');
INSERT INTO `_countries` VALUES ('RU', 'Russia', 'Asia', 'Moscow', 'Russian Ruble');
INSERT INTO `_countries` VALUES ('RW', 'Rwanda', 'Africa', 'Kigali', 'Rwanda Franc');
INSERT INTO `_countries` VALUES ('SA', 'Saudi Arabia', 'Middle East', 'Riyadh', 'Saudi Riyal');
INSERT INTO `_countries` VALUES ('SB', 'Solomon Islands', 'Oceania', 'Honiara', 'Solomon Islands Dollar');
INSERT INTO `_countries` VALUES ('SC', 'Seychelles', 'Africa', 'Victoria', 'Seychelles Rupee');
INSERT INTO `_countries` VALUES ('SD', 'Sudan', 'Africa', 'Khartoum', 'Sudanese Dinar');
INSERT INTO `_countries` VALUES ('SE', 'Sweden', 'Europe', 'Stockholm', 'Swedish Krona');
INSERT INTO `_countries` VALUES ('SG', 'Singapore', 'Southeast Asia', 'Singapore', 'Singapore Dollar');
INSERT INTO `_countries` VALUES ('SH', 'Saint Helena', 'Africa', 'Jamestown', 'Saint Helenian Pound');
INSERT INTO `_countries` VALUES ('SI', 'Slovenia', 'Europe', 'Ljubljana', 'Tolar');
INSERT INTO `_countries` VALUES ('SJ', 'Svalbard', 'Arctic Region', 'Longyearbyen', 'Norwegian Krone');
INSERT INTO `_countries` VALUES ('SK', 'Slovakia', 'Europe', 'Bratislava', 'Slovak Koruna');
INSERT INTO `_countries` VALUES ('SL', 'Sierra Leone', 'Africa', 'Freetown', 'Leone');
INSERT INTO `_countries` VALUES ('SM', 'San Marino', 'Europe', 'San Marino', 'Euro');
INSERT INTO `_countries` VALUES ('SN', 'Senegal', 'Africa', 'Dakar', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('SO', 'Somalia', 'Africa', 'Mogadishu', 'Somali Shilling');
INSERT INTO `_countries` VALUES ('SR', 'Suriname', 'South America', 'Paramaribo', 'Suriname Guilder');
INSERT INTO `_countries` VALUES ('ST', 'São Tom?and Príncipe', 'Africa', 'Sao Tome', 'Dobra');
INSERT INTO `_countries` VALUES ('SV', 'El Salvador', 'Central America and the Caribbean', 'San Salvador', 'El Salvador Colon');
INSERT INTO `_countries` VALUES ('SY', 'Syria', 'Middle East', 'Damascus', 'Syrian Pound');
INSERT INTO `_countries` VALUES ('SZ', 'Swaziland', 'Africa', 'Mbabane', 'Lilangeni');
INSERT INTO `_countries` VALUES ('TC', 'Turks and Caicos Islands', 'Central America and the Caribbean', 'Cockburn Town', 'US Dollar');
INSERT INTO `_countries` VALUES ('TD', 'Chad', 'Africa', 'N\'Djamena', 'CFA Franc BEAC');
INSERT INTO `_countries` VALUES ('TF', 'French Southern and Antarctic Lands', 'Antarctic Region', '--', 'Euro');
INSERT INTO `_countries` VALUES ('TG', 'Togo', 'Africa', 'Lome', 'CFA Franc BCEAO');
INSERT INTO `_countries` VALUES ('TH', 'Thailand', 'Southeast Asia', 'Bangkok', 'Baht');
INSERT INTO `_countries` VALUES ('TJ', 'Tajikistan', 'Commonwealth of Independent States', 'Dushanbe', 'Somoni');
INSERT INTO `_countries` VALUES ('TK', 'Tokelau', 'Oceania', '--', 'New Zealand Dollar');
INSERT INTO `_countries` VALUES ('TM', 'Turkmenistan', 'Commonwealth of Independent States', 'Ashgabat', 'Manat');
INSERT INTO `_countries` VALUES ('TN', 'Tunisia', 'Africa', 'Tunis', 'Tunisian Dinar');
INSERT INTO `_countries` VALUES ('TO', 'Tonga', 'Oceania', 'Nuku\'alofa', 'Pa\'anga');
INSERT INTO `_countries` VALUES ('TP', 'East Timor', '', '--', 'Timor Escudo');
INSERT INTO `_countries` VALUES ('TR', 'Turkey', 'Middle East', 'Ankara', 'Turkish Lira');
INSERT INTO `_countries` VALUES ('TT', 'Trinidad and Tobago', 'Central America and the Caribbean', 'Port-of-Spain', 'Trinidad and Tobago Dollar');
INSERT INTO `_countries` VALUES ('TV', 'Tuvalu', 'Oceania', 'Funafuti', 'Australian Dollar');
INSERT INTO `_countries` VALUES ('TW', 'Taiwan', 'Southeast Asia', 'Taipei', 'New Taiwan Dollar');
INSERT INTO `_countries` VALUES ('TZ', 'Tanzania', 'Africa', 'Dar es Salaam', 'Tanzanian Shilling');
INSERT INTO `_countries` VALUES ('UA', 'Ukraine', 'Commonwealth of Independent States', 'Kiev', 'Hryvnia');
INSERT INTO `_countries` VALUES ('UG', 'Uganda', 'Africa', 'Kampala', 'Uganda Shilling');
INSERT INTO `_countries` VALUES ('UK', 'United Kingdom', 'Europe', 'London', 'Pound Sterling');
INSERT INTO `_countries` VALUES ('UM', 'United States Minor Outlying Islands', '', '--', 'US Dollar');
INSERT INTO `_countries` VALUES ('US', 'United States', 'North America', 'Washington, DC', 'US Dollar');
INSERT INTO `_countries` VALUES ('UY', 'Uruguay', 'South America', 'Montevideo', 'Peso Uruguayo');
INSERT INTO `_countries` VALUES ('UZ', 'Uzbekistan', 'Commonwealth of Independent States', 'Tashkent', 'Uzbekistan Sum');
INSERT INTO `_countries` VALUES ('VA', 'Holy See (Vatican City)', 'Europe', 'Vatican City', 'Euro');
INSERT INTO `_countries` VALUES ('VC', 'Saint Vincent and the Grenadines', 'Central America and the Caribbean', 'Kingstown', 'East Caribbean Dollar');
INSERT INTO `_countries` VALUES ('VE', 'Venezuela', 'South America, Central America and the Caribbean', 'Caracas', 'Bolivar');
INSERT INTO `_countries` VALUES ('VG', 'British Virgin Islands', 'Central America and the Caribbean', 'Road Town', 'US dollar');
INSERT INTO `_countries` VALUES ('VI', 'Virgin Islands', 'Central America and the Caribbean', 'Charlotte Amalie', 'US Dollar');
INSERT INTO `_countries` VALUES ('VN', 'Vietnam', 'Southeast Asia', 'Hanoi', 'Dong');
INSERT INTO `_countries` VALUES ('VU', 'Vanuatu', 'Oceania', 'Port-Vila', 'Vatu');
INSERT INTO `_countries` VALUES ('WF', 'Wallis and Futuna', 'Oceania', 'Mata-Utu', 'CFP Franc');
INSERT INTO `_countries` VALUES ('WS', 'Samoa', 'Oceania', 'Apia', 'Tala');
INSERT INTO `_countries` VALUES ('YE', 'Yemen', 'Middle East', 'Sanaa', 'Yemeni Rial');
INSERT INTO `_countries` VALUES ('YT', 'Mayotte', 'Africa', 'Mamoutzou', 'Euro');
INSERT INTO `_countries` VALUES ('YU', 'Yugoslavia', 'Europe', 'Belgrade', 'Yugoslavian Dinar');
INSERT INTO `_countries` VALUES ('ZA', 'South Africa', 'Africa', 'Pretoria', 'Rand');
INSERT INTO `_countries` VALUES ('ZM', 'Zambia', 'Africa', 'Lusaka', 'Kwacha');
INSERT INTO `_countries` VALUES ('ZW', 'Zimbabwe', 'Africa', 'Harare', 'Zimbabwe Dollar');
INSERT INTO `_countries` VALUES ('PX', 'Proxy Server', 'Internet', '', '');
INSERT INTO `_countries` VALUES ('EU', 'European Union', 'Europe', 'none', 'Euro');
