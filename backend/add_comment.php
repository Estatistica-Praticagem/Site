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

// Recebe o JSON enviado pelo front
$data = json_decode(file_get_contents("php://input"), true);
$contact_id = isset($data['contact_id']) ? intval($data['contact_id']) : 0;
$user_id    = isset($data['user_id']) ? $data['user_id'] : '';
$comment    = isset($data['comment']) ? trim($data['comment']) : '';
$image_url  = isset($data['image_url']) ? trim($data['image_url']) : ''; // NOVO

if (!$contact_id || $user_id === '' || $comment === '') {
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

// Verifica se o usuário existe
$res = $conn->query("SELECT id FROM users_orizzonttebi WHERE id = '$user_id' LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["message" => "Usuário inválido."]);
    exit;
}

// Verifica se o contato existe
$res = $conn->query("SELECT id FROM contacts WHERE id = '$contact_id' LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["message" => "Contato não encontrado."]);
    exit;
}

// Salva o comentário, incluindo a imagem se vier
$comment_sql = $conn->real_escape_string($comment);
$image_url_sql = $image_url ? ("'" . $conn->real_escape_string($image_url) . "'") : 'NULL';

$sql = "INSERT INTO comments (contact_id, user_id, comment, image_url) VALUES ('$contact_id', '$user_id', '$comment_sql', $image_url_sql)";
if ($conn->query($sql)) {
    echo json_encode(["message" => "Comentário adicionado com sucesso."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao adicionar comentário.", "erro_sql" => $conn->error]);
}

$conn->close();
