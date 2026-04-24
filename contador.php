<?php
$archivoContador = 'contador.txt';
$archivoLog = 'visitas.log';

$fecha = date('Y-m-d');
$hora = date('H:i:s');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'IP desconocida';
$url = $_SERVER['HTTP_REFERER'] ?? 'URL directa o desconocida';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

function detectarNavegador($ua) {
    if (strpos($ua, 'Firefox') !== false) return 'Firefox';
    if (strpos($ua, 'Chrome') !== false && strpos($ua, 'Edg') === false) return 'Chrome';
    if (strpos($ua, 'Safari') !== false && strpos($ua, 'Chrome') === false) return 'Safari';
    if (strpos($ua, 'Edg') !== false) return 'Edge';
    if (strpos($ua, 'Opera') !== false || strpos($ua, 'OPR') !== false) return 'Opera';
    if (strpos($ua, 'MSIE') !== false || strpos($ua, 'Trident') !== false) return 'Internet Explorer';
    return 'Otro';
}
function detectarSO($ua) {
    if (strpos($ua, 'Windows') !== false) return 'Windows';
    if (strpos($ua, 'Mac') !== false) return 'macOS';
    if (strpos($ua, 'Linux') !== false && strpos($ua, 'Android') === false) return 'Linux';
    if (strpos($ua, 'Android') !== false) return 'Android';
    if (strpos($ua, 'iPhone') !== false || strpos($ua, 'iPad') !== false || strpos($ua, 'iPod') !== false) return 'iOS';
    return 'Otro';
}

$navegador = detectarNavegador($userAgent);
$so = detectarSO($userAgent);
$userAgentResumido = "$navegador / $so";

$contador = 1;
if (file_exists($archivoContador)) {
    $contenido = file_get_contents($archivoContador);
    $contador = (int)trim($contenido) + 1;
}
file_put_contents($archivoContador, $contador);

$registro = "$fecha | $hora | IP: $ip | URL: $url | $userAgentResumido | (Completo: $userAgent)\n";
file_put_contents($archivoLog, $registro, FILE_APPEND | LOCK_EX);

http_response_code(200);
?>