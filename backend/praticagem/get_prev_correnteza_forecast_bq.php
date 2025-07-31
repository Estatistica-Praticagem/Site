<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Google\Cloud\BigQuery\BigQueryClient;

/* ─────────────── CORS ─────────────────────────────── */
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
    header("Access-Control-Allow-Origin: https://www.meusimulador.com"); // fallback seguro
}
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Pré-flight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/* ─────────── Credencial local ─────────────────────── */
$credFile = __DIR__ . '/local-bliss-359814-77f4ec3f5fce.json';
if (!is_readable($credFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'erro' => 'Credencial não encontrada', 'path' => $credFile], JSON_UNESCAPED_UNICODE);
    exit;
}
putenv("GOOGLE_APPLICATION_CREDENTIALS={$credFile}");
$sa = json_decode(file_get_contents($credFile), true)['client_email'] ?? 'desconhecido';

/* ─────────── Parâmetros ───────────────────────────── */
$tblParam = '';
if (isset($_GET['tabela'])) {
    $tblParam = strtolower(trim((string)$_GET['tabela']));
} elseif (isset($_GET['tipo'])) {
    $tblParam = strtolower(trim((string)$_GET['tipo']));
}

$isHourly = true; // padrão
if (in_array($tblParam, ['5', '5m', '5min', '5-min', 'cinco', '5minutos'], true)) {
    $isHourly = false;
} elseif (in_array($tblParam, ['hora', 'horaria', 'hour', 'hourly', 'h1'], true)) {
    $isHourly = true;
}

$TABLE_HOURLY = 'local-bliss-359814.wherehouse_previsoes.prev_correnteza';
$TABLE_5MIN   = 'local-bliss-359814.wherehouse_previsoes.prev_correnteza_5min';
$tableFq      = $isHourly ? $TABLE_HOURLY : $TABLE_5MIN;

// limit: padrão 1000, clamp 1..5000
$limit = 1000;
if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
    $custom = (int)$_GET['limit'];
    if ($custom >= 1) {
        $limit = min($custom, 5000);
    }
}

/* ─────────── Cliente BigQuery ─────────────────────── */
$bq = new BigQueryClient([
    'projectId'   => 'local-bliss-359814',
    'keyFilePath' => $credFile,
]);

/* ─────────── Monta SQL (ABS para remover “-” nas previsões) ─────────── */
/* ─────────── Monta SQL (ABS para remover “-” nas previsões) ─────────── */
function buildSql(string $tableFq, int $limit): string {
    $isPrevHour = preg_match('/\.prev_correnteza$/', $tableFq) === 1;
    $isPrev5m   = preg_match('/\.prev_correnteza_5min$/', $tableFq) === 1;

    if ($isPrevHour || $isPrev5m) {
        $depths = ['1_5m','3m','6m','7_5m','9m','10_5m','12m','13_5m','superficie'];
        $cols = ["CAST(timestamp_br AS DATETIME) AS timestamp_br"];
        foreach ($depths as $d) {
            // Usa ABS para remover o sinal negativo
            $cols[] = "ABS(SAFE_CAST(valor_previsto_{$d} AS FLOAT64)) AS valor_previsto_{$d}";
        }
        return sprintf(
            "SELECT\n  %s\nFROM `%s`\nORDER BY timestamp_br DESC\nLIMIT %d",
            implode(",\n  ", $cols),
            $tableFq,
            $limit
        );
    }

    // Qualquer outra tabela (ex.: mestre_*): retorna tudo como está
    return sprintf(
        "SELECT * FROM `%s` ORDER BY timestamp_br DESC LIMIT %d",
        $tableFq,
        $limit
    );
}


$sql = buildSql($tableFq, $limit);

/* ─────────── Execução e resposta ──────────────────── */
try {
    $queryJob = $bq->query($sql);
    $results  = $bq->runQuery($queryJob);

    $jobInfo = $results->job()->info();
    $status  = $jobInfo['status'] ?? [];
    $stateOk = ($status['state'] ?? '') === 'DONE';
    $hasErr  = isset($status['errorResult']);

    if (!$stateOk || $hasErr) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'erro'    => 'Job não concluído ou retornou erro',
            'status'  => $status,
            'sa'      => $sa,
            'table'   => $tableFq,
            'limit'   => $limit,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Converte resultados em array simples, convertendo campos array vazios em null
    $rows = [];
    foreach ($results as $row) {
        $item = iterator_to_array($row);
        foreach ($item as $k => $v) {
            if (is_array($v) && count($v) === 0) {
                $item[$k] = null;
            }
        }
        $rows[] = $item;
    }

    echo json_encode([
        'success' => true,
        'sa'      => $sa,
        'table'   => $tableFq,
        'limit'   => $limit,
        'data'    => $rows,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'erro'    => $e->getMessage(),
        'sa'      => $sa,
        'table'   => $tableFq ?? null,
        'limit'   => $limit ?? null,
    ], JSON_UNESCAPED_UNICODE);
}
