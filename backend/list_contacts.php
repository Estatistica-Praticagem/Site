<?php
require_once __DIR__ . '/config.php';

// Habilita CORS apenas para os domínios autorizados
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

// Responde a requisição de pré-verificação (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexão com o banco
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco"]);
    exit();
}

// Recebe o JSON com o user_id
$data = json_decode(file_get_contents("php://input"), true);
$user_id = $conn->real_escape_string($data['user_id'] ?? '');

if (!$user_id) {
    http_response_code(400);
    echo json_encode(["error" => "ID do usuário não enviado."]);
    exit();
}

// Valida o usuário
$verifica = $conn->query("SELECT id FROM users_orizzonttebi WHERE id = '$user_id' LIMIT 1");
if (!$verifica || $verifica->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["error" => "Usuário inválido."]);
    exit();
}

// Busca os contatos
$sql = "SELECT id, name, email, country_code, phone, service, message, created_at, status FROM contacts ORDER BY created_at DESC";
$result = $conn->query($sql);

$dados = [];
while ($row = $result->fetch_assoc()) {
    $dados[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $dados
]);

$conn->close();
