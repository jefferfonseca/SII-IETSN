<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;

function generarQRLocal(string $token, string $logoPath): string
{
    if (!file_exists($logoPath)) {
        throw new Exception("Logo no encontrado: " . $logoPath);
    }

    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($token) // 🔥 ahora es string plano
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(340)
        ->margin(20)
        ->foregroundColor(new Color(10, 20, 80))
        ->backgroundColor(new Color(245, 246, 250))
        ->logoPath($logoPath)
        ->logoResizeToWidth(80)
        ->logoPunchoutBackground(true)
        ->build();

    return $result->getString();
}
