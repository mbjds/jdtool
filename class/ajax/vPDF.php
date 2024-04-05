<?php

namespace JDCustom\ajax;

use JDCustom\pdf\makePDF;

class vPDF
{
    public function __construct()
    {
        add_action('wp_ajax_generatePDF', [$this, 'generatePDF']);
        add_action('wp_ajax_nopriv_generatePDF', [$this, 'generatePDF']);
    }

    /**
     * @return string
     */
    public function generatePDF()
    {
        $id = $_POST['id'];
        $pdf = new makePDF($id);
        echo $pdf->generatePDF();

        exit;
    }
}
