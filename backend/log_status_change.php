<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Google\Cloud\BigQuery\BigQueryClient;

header("Content-Type: application/json; charset=utf-8");
$allowed_origins = [
    'https://www.meusimulador.com',
    'https://www.orizzonttebi.com'
];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: https://www.meusimulador.com");
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "CORS preflight"]);
    exit;
}

try {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    if (!$data) {
        throw new Exception("JSON inválido.");
    }

    // Valores
    $contact_id = $data['contact_id'] ?? '';
    $email      = $data['email']      ?? '';
    $old_status = $data['old_status'] ?? '';
    $new_status = $data['new_status'] ?? '';
    $user_id    = $data['user_id']    ?? '';
    $changed_at = date('c'); // ISO8601

    if (!$email || !$new_status) {
        throw new Exception("Campos obrigatórios ausentes.");
    }

    // ------------------- MYSQL -------------------
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Erro ao conectar no MySQL: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    $sql = "INSERT INTO contact_status_logs (contact_id, email, old_status, new_status, user_id, changed_at)
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare MySQL: " . $conn->error);
    }
    $stmt->bind_param("sssss", $contact_id, $email, $old_status, $new_status, $user_id);

    $stmt->execute();
    $stmt->close();
    $conn->close();

    // ------------------- BIGQUERY -------------------
    putenv("GOOGLE_APPLICATION_CREDENTIALS=" . BQ_CREDENTIALS_PATH);
    $bigQuery = new BigQueryClient([
        'projectId' => BQ_PROJECT_ID,
        'keyFilePath' => BQ_CREDENTIALS_PATH
    ]);

    $dataset = $bigQuery->dataset(BQ_DATASET_ID);
    $tableId = 'contact_status_logs';
    $table = $dataset->table($tableId);

    // Cria a tabela se não existir
    if (!$table->exists()) {
        $schema = [
            ['name' => 'contact_id', 'type' => 'STRING'],
            ['name' => 'email', 'type' => 'STRING'],
            ['name' => 'old_status', 'type' => 'STRING'],
            ['name' => 'new_status', 'type' => 'STRING'],
            ['name' => 'user_id', 'type' => 'STRING'],
            ['name' => 'changed_at', 'type' => 'TIMESTAMP'],
        ];
        $table = $dataset->createTable($tableId, ['schema' => ['fields' => $schema]]);
    }

    // Inserção no BigQuery
    $insert = $table->insertRows([[
        'data' => [
            'contact_id' => $contact_id,
            'email' => $email,
            'old_status' => $old_status,
            'new_status' => $new_status,
            'user_id' => $user_id,
            'changed_at' => $changed_at
        ]
    ]]);

    if (!$insert->isSuccessful()) {
        $erros = [];
        foreach ($insert->info()['insertErrors'] ?? [] as $row) {
            $erros[] = json_encode($row['errors']);
        }
        throw new Exception("Erro ao salvar no BigQuery: " . implode(' | ', $erros));
    }

    echo json_encode(["success" => true, "message" => "Log salvo em MySQL e BigQuery com sucesso."]);

} catch (Exception $e) {
    http_response_code(200); // evita bloqueio por CORS
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
