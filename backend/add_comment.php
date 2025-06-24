<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// Recebe o JSON enviado pelo front
$data = json_decode(file_get_contents("php://input"), true);
$contact_id = isset($data['contact_id']) ? intval($data['contact_id']) : 0;
$user_id    = isset($data['user_id']) ? $data['user_id'] : '';
$comment    = isset($data['comment']) ? trim($data['comment']) : '';

// Validação básica
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

// Salva o comentário
$comment_sql = $conn->real_escape_string($comment);
$sql = "INSERT INTO comments (contact_id, user_id, comment) VALUES ('$contact_id', '$user_id', '$comment_sql')";
if ($conn->query($sql)) {
    echo json_encode(["message" => "Comentário adicionado com sucesso."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao adicionar comentário.", "erro_sql" => $conn->error]);
}

$conn->close();
