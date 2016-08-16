<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php

//http://www.w3schools.com/php/php_ref_simplexml.asp

// Load XML with SimpleXml from string
$root = simplexml_load_string('<root><a>foo</a></root>');
// Modify a node
$root->a = 'bar';
// Saving the whole modified XML to a new filename
$root->asXml('updated.xml');
// Save only the modified node
$root->a->asXml('only-a.xml');

?>
</body>
</html>
