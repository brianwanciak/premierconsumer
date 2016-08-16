<?php

    ob_start();
    include('downloadContent.php');
    $content = ob_get_clean();

    // convert in PDF
    require_once('html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', $marges = array(10, 10, 10, 15));
//      $html2pdf->setModeDebug();
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output(array_pop(explode("/", $_SERVER['REQUEST_URI'])).".pdf");
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }


 ?>
 
