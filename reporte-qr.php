<?php

require_once __DIR__ . '/vendor/autoload.php';

/*
====================================
EXTENSION FPDF PARA LINEA PUNTEADA
====================================
*/

class PDF extends FPDF
{
    function SetDash($black=null,$white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';

        $this->_out($s);
    }
}

/*
====================================
CONFIGURACION
====================================
*/

$carpetaBase = "qr_estudiantes";
$carpetaSalida = "qr-pdf";

if(!file_exists($carpetaSalida)){
    mkdir($carpetaSalida,0777,true);
}

$carpetas = scandir($carpetaBase);

/*
====================================
RECORRER CARPETAS
====================================
*/

foreach($carpetas as $carpeta){

    if($carpeta == "." || $carpeta == ".."){
        continue;
    }

    $rutaCarpeta = $carpetaBase . "/" . $carpeta;

    if(!is_dir($rutaCarpeta)){
        continue;
    }

    $imagenes = glob($rutaCarpeta."/*.png");

    if(empty($imagenes)){
        continue;
    }

    /*
    ====================================
    CREAR PDF
    ====================================
    */

    $pdf = new PDF('P','mm','Letter');
    $pdf->AddPage();

    /*
    ====================================
    GRID 4x4
    ====================================
    */

    $qrWidth = 50;
    $qrHeight = 58;

    $cols = 4;
    $rows = 4;

    $pageWidth = 216;
    $pageHeight = 279;

    $gridWidth = $cols * $qrWidth;
    $gridHeight = $rows * $qrHeight;

    $startX = ($pageWidth - $gridWidth) / 2;
    $startY = ($pageHeight - $gridHeight) / 2;

    $x = $startX;
    $y = $startY;

    $col = 0;
    $row = 0;

    foreach($imagenes as $qr){

        /*
        ====================================
        MARCO PUNTEADO DE CORTE
        ====================================
        */

        $pdf->SetLineWidth(0.2);
        $pdf->SetDash(1,1);
        $pdf->Rect($x,$y,$qrWidth,$qrHeight);
        $pdf->SetDash();

        /*
        ====================================
        IMAGEN QR
        ====================================
        */

        $pdf->Image($qr,$x,$y,$qrWidth,$qrHeight);

        /*
        ====================================
        CONTROL GRID
        ====================================
        */

        $col++;

        if($col >= $cols){

            $col = 0;
            $row++;

            $x = $startX;
            $y += $qrHeight;

        }else{

            $x += $qrWidth;

        }

        if($row >= $rows){

            $pdf->AddPage();

            $x = $startX;
            $y = $startY;

            $col = 0;
            $row = 0;

        }

    }

    /*
    ====================================
    GUARDAR PDF
    ====================================
    */

    $nombrePDF = $carpetaSalida."/".$carpeta.".pdf";

    $pdf->Output("F",$nombrePDF);

}

echo "PDF generados correctamente";