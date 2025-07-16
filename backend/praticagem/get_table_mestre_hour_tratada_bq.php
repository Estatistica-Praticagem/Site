<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
// Se estiver em praticagem/arquivo.php, dirname(__DIR__) volta para a raiz

use Google\Cloud\BigQuery\BigQueryClient;

/* ─────────────── CORS ─────────────────────────────── */

$allowed_origins = [
    'https://www.meusimulador.com',
    'https://meusimulador.com',
    'https://www.orizzonttebi.com',
    'https://orizzonttebi.com'
];

// Normaliza: remove barra no final, se existir
$origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';

// Verifica se a origem está na lista permitida
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Você pode logar aqui se quiser saber de onde veio
    // file_put_contents(__DIR__.'/debug_origin.txt', "Bloqueado: $origin\n", FILE_APPEND);

    header("Access-Control-Allow-Origin: https://www.meusimulador.com"); // fallback seguro
}

// Cabeçalhos obrigatórios para CORS
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Responde as requisições OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/* ─────────── Credencial local ─────────────────────── */
$credFile = __DIR__ . '/local-bliss-359814-77f4ec3f5fce.json';
if (!is_readable($credFile)) {
    http_response_code(500);
    echo json_encode(['erro' => 'Credencial não encontrada', 'path' => $credFile]);
    exit;
}
putenv("GOOGLE_APPLICATION_CREDENTIALS={$credFile}");
$sa = json_decode(file_get_contents($credFile), true)['client_email'] ?? 'desconhecido';

/* ─────────── Limite de linhas ───────────────────────
 *  • Sem parâmetro → 1000
 *  • Com parâmetro → usa o valor enviado (>=1)
 */
$limit = 1000;
if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
    $custom = (int) $_GET['limit'];
    if ($custom >= 1) {
        $limit = $custom;
    }
}

/* ─────────── Cliente BigQuery ─────────────────────── */
$bq = new BigQueryClient([
    'projectId'   => 'local-bliss-359814',
    'keyFilePath' => $credFile
]);

/* ─────────── Consulta SQL ─────────────────────────── */
$sql = "
  SELECT *
  FROM `local-bliss-359814.wherehouse_tratado.mestre_hour_tratada`
  ORDER BY timestamp_br DESC
  LIMIT $limit
";

try {
    $results = $bq->runQuery($bq->query($sql));
    $jobInfo = $results->job()->info();
    $status  = $jobInfo['status'];

    $stateOk  = ($status['state'] ?? '') === 'DONE';
    $hasError = isset($status['errorResult']);

    if (!$stateOk || $hasError) {
        http_response_code(500);
        echo json_encode([
            'erro'   => 'Job não concluído ou retornou erro',
            'status' => $status,
            'sa'     => $sa
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'sa'      => $sa,
        'limit'   => $limit,
        'data'    => iterator_to_array($results)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage(), 'sa' => $sa]);
}
