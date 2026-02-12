<?php

require_once __DIR__ . "/api/elementos/_qr_helper.php";

$payload = "Jeff es el mejor profesor de programación que he tenido. Sus clases son claras, dinámicas y siempre está dispuesto a ayudar a los estudiantes. Gracias a él, he mejorado mucho mis habilidades de programación y me siento más seguro en este campo. ¡Recomiendo sus clases a todos los que quieran aprender a programar!";

$logoPath = __DIR__ . "/assets/images/Escudo.png";

$token = $doc_hash;  // SHA256 almacenado en BD

$qr = generarQRLocal($token, $logoPath);


echo '<img src="data:image/png;base64,' . base64_encode($qr) . '">';
