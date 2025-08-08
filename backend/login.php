<?php
require_once __DIR__ . '/config.php';

// Cabeçalhos CORS e tipo de resposta
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
header("Content-Type: application/json; charset=utf-8");

// Responde pré-verificação CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexão com o banco (MySQLi)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao conectar no banco."]);
    exit;
}

// Recebe e valida os dados
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$contact_id = $conn->real_escape_string($data['contact_id'] ?? '');
$email      = $conn->real_escape_string($data['email'] ?? '');
$old_status = $conn->real_escape_string($data['old_status'] ?? '');
$new_status = $conn->real_escape_string($data['new_status'] ?? '');
$user_id    = $conn->real_escape_string($data['user_id'] ?? '');

if (!$email || !$new_status) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

// Prepara e executa inserção
$sql = "INSERT INTO contact_status_logs (contact_id, email, old_status, new_status, user_id)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro na preparação da query."]);
    exit;
}

$stmt->bind_param("sssss", $contact_id, $email, $old_status, $new_status, $user_id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(["success" => true, "message" => "Log salvo com sucesso."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao salvar log."]);
}

$stmt->close();
$conn->close();
