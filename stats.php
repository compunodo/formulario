<?php
// ==============================================
// CONTRASEÑA - Cambia aquí el usuario y clave
// ==============================================
$usuario_correcto = 'admin';
$clave_correcta = '1234';
// ==============================================

if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] != $usuario_correcto || 
    $_SERVER['PHP_AUTH_PW'] != $clave_correcta) {
    header('WWW-Authenticate: Basic realm="Estadísticas restringidas"');
    header('HTTP/1.0 401 Unauthorized');
    echo '🔒 Acceso denegado. Usuario o contraseña incorrectos.';
    exit;
}
// ==============================================

$archivoContador = 'contador.txt';
$archivoLog = 'visitas.log';

if (!file_exists($archivoContador) || !file_exists($archivoLog)) {
    die("Aún no hay datos de visitas. Espera la primera visita.");
}

$totalVisitas = (int)file_get_contents($archivoContador);
$lineas = file($archivoLog, FILE_IGNORE_NEW_LINES);

$ips = [];
$visitasPorDia = [];

foreach ($lineas as $linea) {
    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $linea, $fechaMatch)) {
        $fecha = $fechaMatch[1];
        $visitasPorDia[$fecha] = ($visitasPorDia[$fecha] ?? 0) + 1;
    }
    if (preg_match('/IP: ([\d\.]+)/', $linea, $ipMatch)) {
        $ips[] = $ipMatch[1];
    }
}

$visitasUnicas = count(array_unique($ips));
arsort($visitasPorDia);
$ultimas = array_slice($lineas, -10);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadísticas privadas</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f0f2f5; margin: 2rem; }
        .container { max-width: 900px; margin: auto; background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e466e; border-bottom: 2px solid #ccc; padding-bottom: 0.5rem; }
        .card { background: #f9f9f9; padding: 1rem; margin: 1rem 0; border-radius: 8px; }
        .numero { font-size: 2rem; font-weight: bold; color: #2c7da0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.5rem; border-bottom: 1px solid #ddd; }
        th { background: #e9ecef; }
        .aviso { background: #fff3cd; padding: 0.75rem; border-radius: 6px; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <h1>📊 Panel de estadísticas</h1>
    <div class="card">
        <strong>📈 Total de visitas:</strong>
        <div class="numero"><?php echo $totalVisitas; ?></div>
    </div>
    <div class="card">
        <strong>👥 Visitas únicas (por IP):</strong>
        <div class="numero"><?php echo $visitasUnicas; ?></div>
    </div>
    <div class="card">
        <strong>📅 Visitas por día (últimos 7 días):</strong>
        <ul>
        <?php
        $i = 0;
        foreach ($visitasPorDia as $fecha => $cantidad) {
            echo "<li><strong>$fecha</strong>: $cantidad visitas</li>";
            $i++;
            if ($i >= 7) break;
        }
        ?>
        </ul>
    </div>
    <div class="card">
        <strong>🕒 Últimas 10 visitas:</strong>
        <table>
            <tr><th>Fecha/Hora</th><th>IP</th><th>Navegador/SO</th><th>URL</th></tr>
            <?php foreach (array_reverse($ultimas) as $linea) :
                preg_match('/^(\d{4}-\d{2}-\d{2}) \| (\d{2}:\d{2}:\d{2})/', $linea, $fechah);
                preg_match('/IP: ([\d\.]+)/', $linea, $ipMatch);
                preg_match('/URL: (.*?) \|/', $linea, $urlMatch);
                preg_match('/\|\s([A-Za-z]+\s?\/\s?[A-Za-z0-9]+)\s/', $linea, $uaMatch);
                $fechaHora = isset($fechah[1]) ? $fechah[1] . ' ' . $fechah[2] : '--';
                $ipMostrar = $ipMatch[1] ?? '--';
                $uaMostrar = $uaMatch[1] ?? 'desconocido';
                $urlMostrar = isset($urlMatch[1]) ? htmlspecialchars($urlMatch[1]) : '--';
            ?>
            <tr>
                <td><?php echo $fechaHora; ?></td>
                <td><?php echo $ipMostrar; ?></td>
                <td><?php echo $uaMostrar; ?></td>
                <td><?php echo $urlMostrar; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="aviso">
        🔒 Protegido por contraseña (PHP). Usuario: <strong>admin</strong> Contraseña: <strong>1234</strong><br>
        Para cambiarlo, edita las primeras líneas de <code>stats.php</code>.
    </div>
</div>
</body>
</html>