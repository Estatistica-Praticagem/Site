<?php
// DEBUG SEMPRE: Mostra erros no browser (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco
require_once __DIR__ . '/config.php';

// CORS e tipo de resposta
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
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'CORS preflight']);
    exit;
}

try {
    // Conexão
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception('Erro ao conectar no banco: ' . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    // Recebe JSON puro
    $input = file_get_contents("php://input");
    if (!$input) {
        throw new Exception('Nenhum dado enviado (body vazio).');
    }
    $data = json_decode($input, true);
    if (!$data) {
        throw new Exception('JSON malformado ou inválido: ' . $input);
    }

    // Validação mínima
    $contact_id = $conn->real_escape_string($data['contact_id'] ?? '');
    $email      = $conn->real_escape_string($data['email'] ?? '');
    $old_status = $conn->real_escape_string($data['old_status'] ?? '');
    $new_status = $conn->real_escape_string($data['new_status'] ?? '');
    $user_id    = $conn->real_escape_string($data['user_id'] ?? '');

    if (!$email || !$new_status) {
        throw new Exception('Dados incompletos: email ou novo status ausente.');
    }

    // Prepara SQL
    $sql = "INSERT INTO contact_status_logs (contact_id, email, old_status, new_status, user_id)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Erro na preparação do SQL: ' . $conn->error);
    }
    $stmt->bind_param("sssss", $contact_id, $email, $old_status, $new_status, $user_id);
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Log salvo com sucesso.']);
    } else {
        throw new Exception('Erro ao salvar no banco: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Sempre retorna erro detalhado
    http_response_code(200); // Garante sempre resposta 200 + erro JSON (evita problemas de CORS)
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
