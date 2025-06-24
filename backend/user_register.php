<?php
require_once __DIR__ . '/config.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

$resposta = [
  "status" => "falha",
  "mensagem" => "Algo deu errado, tente novamente.",
];

// Dados recebidos
$criador_id = $_POST['usuario_id'] ?? null; // quem está logado
$usuario    = $_POST['usuario'] ?? null;
$email      = $_POST['email'] ?? null;
$senha      = $_POST['senha'] ?? null;
$imagem     = $_FILES['imagem'] ?? null;

// Verifica se todos os campos obrigatórios vieram
if (!$criador_id || !$usuario || !$email || !$senha) {
  http_response_code(400);
  $resposta["mensagem"] = "Campos obrigatórios ausentes.";
  echo json_encode($resposta); exit;
}

// Conecta com o banco
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
  http_response_code(500);
  $resposta["mensagem"] = "Erro ao conectar ao banco.";
  echo json_encode($resposta); exit;
}

// Verifica se o criador (usuário logado) existe
$stmt = $conn->prepare("SELECT id FROM users_orizzonttebi WHERE id = ?");
$stmt->bind_param("i", $criador_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
  http_response_code(401);
  $resposta["mensagem"] = "Usuário logado inválido.";
  echo json_encode($resposta); exit;
}
$stmt->close();

// Verifica se o email já está cadastrado
$stmt = $conn->prepare("SELECT id FROM users_orizzonttebi WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  http_response_code(409);
  $resposta["mensagem"] = "E-mail já cadastrado.";
  echo json_encode($resposta); exit;
}
$stmt->close();

// Cadastra o novo usuário
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users_orizzonttebi (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $usuario, $email, $senhaHash);
if (!$stmt->execute()) {
  http_response_code(500);
  $resposta["mensagem"] = "Erro ao cadastrar usuário.";
  $resposta["erro"] = $conn->error;
  echo json_encode($resposta); exit;
}
$userId = $conn->insert_id;
$stmt->close();

// Envia imagem, se existir
$imageUrl = null;
if ($imagem && $imagem['tmp_name']) {
  $url = "https://04lg3w3swi.execute-api.us-east-1.amazonaws.com/DEV/userpicture/{$userId}";
  $fields = [
    'file' => curl_file_create($imagem['tmp_name'], $imagem['type'], $imagem['name'])
  ];
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-api-token: 5534fzd41g35df4g32df4g35df4gd3f54gd3f584gf3'
  ]);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $lambdaResp = curl_exec($ch);
  $lambdaJson = json_decode($lambdaResp, true);
  $imageUrl = $lambdaJson['content']['imageUrl'] ?? null;
  curl_close($ch);

  if ($imageUrl) {
    $stmt = $conn->prepare("UPDATE users_orizzonttebi SET image_url = ? WHERE id = ?");
    $stmt->bind_param("si", $imageUrl, $userId);
    $stmt->execute();
    $stmt->close();
  }
}

// Finaliza
$resposta["status"]     = "sucesso";
$resposta["mensagem"]   = "Usuário cadastrado!";
$resposta["user_id"]    = $userId;
$resposta["usuario"]    = $usuario;
$resposta["email"]      = $email;
$resposta["image_url"]  = $imageUrl;

http_response_code(200);
$conn->close();
echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
