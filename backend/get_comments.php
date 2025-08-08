<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

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


$data = json_decode(file_get_contents("php://input"), true);
$contact_id = isset($data['contact_id']) ? intval($data['contact_id']) : 0;
$user_id    = isset($data['user_id']) ? $data['user_id'] : '';

if (!$contact_id || $user_id === '') {
    http_response_code(400);
    echo json_encode(["message" => "Dados obrigatórios ausentes."]);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Erro na conexão com o banco."]);
    exit;
}

$user_id_sql = $conn->real_escape_string($user_id);

// Verifica se o usuário existe
$res = $conn->query("SELECT id FROM users_orizzonttebi WHERE id = '$user_id_sql' LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["message" => "Usuário inválido."]);
    exit;
}

$sql = "SELECT id, comment, user_id, created_at, updated_at, image_url FROM comments WHERE contact_id = $contact_id ORDER BY created_at DESC";
$res = $conn->query($sql);
$arr = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $arr[] = $row;
    }
}
echo json_encode(["message" => "Comentários carregados.", "data" => $arr]);
$conn->close();
