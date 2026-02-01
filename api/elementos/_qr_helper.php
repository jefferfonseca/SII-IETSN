<?php

function generarQRMonkey(array $payload, string $logoUrl): string
{
    $data = [
        "data" => json_encode($payload, JSON_UNESCAPED_UNICODE),
        "config" => [
            "body" => "circular",
            "eye" => "frame6",
            "eyeBall" => "ball6",
            "bodyColor" => "#ffffff",
            "bgColor" => "#ffffff",
            "eye1Color" => "#191938",
            "eye2Color" => "#a3071a",
            "eye3Color" => "#a3071a",
            "eyeBall1Color" => "#a3071a",
            "eyeBall2Color" => "#191938",
            "eyeBall3Color" => "#191938",
            "gradientColor1" => "#191938",
            "gradientColor2" => "#191938",
            "gradientType" => "radial",
            "gradientOnEyes" => false,
            "logo" => $logoUrl
        ],
        "size" => 300,
        "download" => false,
        "file" => "png"
    ];

    $ch = curl_init("https://api.qrcode-monkey.com/qr/custom");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("QR Monkey error: $error");
    }

    curl_close($ch);

    return $response; // BINARIO REAL
}
