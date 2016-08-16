<?php

	if($site->getLang() == "es"){
		$file = "SPDebtAmount";
	}else{
		$file = "DebtAmount";
	
	
	}

?>
<link type="text/css" rel="StyleSheet" href="/css/calculators/KJE.css" />
<link type="text/css" rel="StyleSheet" href="/css/calculators/KJESiteSpecific.css" />
 <div id="KJEAllContent" class=#KJEAllContent></div>
<!--[if lt IE 9]>
<script language="JavaScript" SRC="excanvas.js"></script>
<![endif]-->
<script language="JavaScript" type="text/javascript" SRC="/js/calculators/KJE.js"></script>
<script language="JavaScript" type="text/javascript" SRC="/js/calculators/KJESiteSpecific.js"></script>

<script language="JavaScript" type="text/javascript" SRC="/js/calculators/<?php echo $file; ?>.js"></script>

<script language="JavaScript" type="text/javascript" SRC="/js/calculators/<?php echo $file; ?>Params.js"></script>