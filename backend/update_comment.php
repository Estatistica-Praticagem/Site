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
$comment_id = isset($data['comment_id']) ? intval($data['comment_id']) : 0;
$user_id    = isset($data['user_id']) ? $data['user_id'] : '';
$comment    = isset($data['comment']) ? trim($data['comment']) : '';

if (!$comment_id || $user_id === '' || $comment === '') {
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

// Só deixa editar se o comentário é do usuário
$res = $conn->query("SELECT id FROM comments WHERE id = $comment_id AND user_id = '$user_id_sql' LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["message" => "Você não pode editar este comentário."]);
    exit;
}

$comment_sql = $conn->real_escape_string($comment);
$sql = "UPDATE comments SET comment = '$comment_sql', updated_at = NOW() WHERE id = $comment_id";
if ($conn->query($sql)) {
    echo json_encode(["message" => "Comentário editado com sucesso."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao editar comentário.", "erro_sql" => $conn->error]);
}
$conn->close();
