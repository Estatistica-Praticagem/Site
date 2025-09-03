<?php
declare(strict_types=1);

// ────── Configuração CORS igual seu backend ──────
$allowed_origins = [
    'https://www.meusimulador.com',
    'https://meusimulador.com',
    'https://www.orizzonttebi.com',
    'https://orizzonttebi.com'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://www.meusimulador.com");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

// Pré-flight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ─────── SUA API KEY SEGURA ───────
$OPENWEATHER_KEY = '10fe60f23364376f39951ae7c07d0007'; // Troque por sua chave real

// ────── Parâmetros esperados ──────
$lat = isset($_GET['lat']) ? trim($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? trim($_GET['lon']) : null;
if (!$lat || !$lon) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros lat/lon obrigatórios']);
    exit;
}

// Parâmetros extras opcionais
$units = $_GET['units'] ?? 'metric';
$lang  = $_GET['lang'] ?? 'pt';

// ────── Monta URL da API OpenWeather (One Call 3.0) ──────
$ow_url = "https://api.openweathermap.org/data/3.0/onecall?lat={$lat}&lon={$lon}&units={$units}&lang={$lang}&appid={$OPENWEATHER_KEY}";

// ────── Chamada segura ──────
try {
    $ch = curl_init($ow_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $curl_err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curl_err || $http_code !== 200) {
        http_response_code($http_code ?: 500);
        echo json_encode(['error' => 'Erro ao acessar OpenWeather', 'curl_err' => $curl_err, 'http_code' => $http_code]);
        exit;
    }
    echo $response;
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor', 'msg' => $e->getMessage()]);
}
