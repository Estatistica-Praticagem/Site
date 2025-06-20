<?php
$allowed_origins = [
    'https://www.meusimulador.com',
    'https://www.orizzonttebi.com'
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: https://www.meusimulador.com"); // fallback
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$logs = [];
function log_msg($mensagem) {
    global $logs;
    $logs[] = "[" . date('H:i:s') . "] $mensagem";
}

log_msg("📥 Requisição recebida");
$data = json_decode(file_get_contents("php://input"), true);
log_msg("📦 Dados recebidos: " . json_encode($data));

if (!isset($data['nome'], $data['email'], $data['ddd'], $data['telefone'], $data['servico'], $data['descricao'])) {
    log_msg("❌ Campos obrigatórios ausentes.");
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields.", "logs" => $logs]);
    exit;
}

$name         = $data['nome'];
$email        = $data['email'];
$country_code = $data['ddd'];
$phone        = $data['telefone'];
$service      = $data['servico'];
$message      = $data['descricao'];
$created_at   = date('Y-m-d H:i:s');

// Inserção no MySQL
$host = "mysql.meusimulador.com";
$usuario = "meusimulador";
$senha = "@OrizzontteBI2025";
$banco = "meusimulador";
$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    log_msg("❌ Erro MySQL: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro na conexão com o banco.", "logs" => $logs]);
    exit;
}

$sql = "INSERT INTO contacts (name, email, country_code, phone, service, message)
        VALUES ('$name', '$email', '$country_code', '$phone', '$service', '$message')";
$conn->query($sql);
$conn->close();
log_msg("✅ Inserido no MySQL");

// Inserção no BigQuery
require_once __DIR__ . '/vendor/autoload.php';
use Google\Cloud\BigQuery\BigQueryClient;

try {
    $credenciaisJson = __DIR__ . '/orizzonttebi-2743f6195331.json';
    putenv("GOOGLE_APPLICATION_CREDENTIALS=$credenciaisJson");

    $bigQuery = new BigQueryClient([
        'projectId' => 'orizzonttebi',
        'keyFilePath' => $credenciaisJson
    ]);
    log_msg("🔐 Conectado ao BigQuery");

    $dataset = $bigQuery->dataset('form_data');
    $table = $dataset->table('contacts');

    $insert = $table->insertRows([
        ['data' => [
            'name'         => $name,
            'email'        => $email,
            'country_code' => $country_code,
            'phone'        => $phone,
            'service'      => $service,
            'message'      => $message,
            'created_at'   => date('c')
        ]]
    ]);

    if ($insert->isSuccessful()) {
        log_msg("✅ Enviado ao BigQuery com sucesso.");
        echo json_encode([
            "success" => true,
            "message" => "Dados salvos em MySQL e BigQuery com sucesso.",
            "logs" => $logs
        ]);
    } else {
        $erros = [];
        foreach ($insert->info()['insertErrors'] ?? [] as $row) {
            $erros[] = json_encode($row['errors']);
        }
        log_msg("❌ Falha no BigQuery: " . implode(" | ", $erros));
        echo json_encode([
            "success" => false,
            "message" => "Erro ao enviar para o BigQuery.",
            "logs" => $logs
        ]);
    }

} catch (Exception $e) {
    log_msg("❌ Exceção BigQuery: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Erro na integração com o BigQuery.",
        "logs" => $logs
    ]);
}
