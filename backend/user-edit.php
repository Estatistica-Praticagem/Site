<?php
require_once __DIR__ . '/config.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$resposta = [
  "status" => "erro",
  "mensagem" => "Falha ao atualizar dados.",
  "resposta_lambda" => null
];

// Dados recebidos
$userId  = $_POST['usuario_id'] ?? null;
$usuario = $_POST['usuario'] ?? null;
$email   = $_POST['email'] ?? null;
$senha   = $_POST['senha'] ?? null;
$imagem  = $_FILES['imagem'] ?? null;

// Verifica se o ID veio
if (!$userId) {
  http_response_code(400);
  $resposta["mensagem"] = "ID do usuário ausente.";
  echo json_encode($resposta); exit;
}

// Conexão com banco
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
  http_response_code(500);
  $resposta["mensagem"] = "Erro ao conectar ao banco.";
  echo json_encode($resposta); exit;
}

// Monta UPDATE dinâmico
$campos = [];
$paramTypes = '';
$paramValues = [];

if ($usuario) {
  $campos[] = 'name = ?';
  $paramTypes .= 's';
  $paramValues[] = $usuario;
}
if ($email) {
  $campos[] = 'email = ?';
  $paramTypes .= 's';
  $paramValues[] = $email;
}
if ($senha) {
  $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
  $campos[] = 'password = ?';
  $paramTypes .= 's';
  $paramValues[] = $senhaHash;
}

if (!empty($campos)) {
  $sql = "UPDATE users_orizzonttebi SET " . implode(', ', $campos) . " WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $paramTypes .= 's'; // UUID é string
  $paramValues[] = $userId;
  $stmt->bind_param($paramTypes, ...$paramValues);
  if (!$stmt->execute()) {
    http_response_code(500);
    $resposta["mensagem"] = "Erro ao atualizar dados.";
    echo json_encode($resposta); exit;
  }
  $stmt->close();
}

// Upload da imagem
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
  curl_close($ch);

  $resposta["resposta_lambda"] = $lambdaJson;

  $imageUrl = $lambdaJson['content']['imageUrl'] ?? null;
  if ($imageUrl) {
    $stmt = $conn->prepare("UPDATE users_orizzonttebi SET image_url = ? WHERE id = ?");
    $stmt->bind_param("ss", $imageUrl, $userId); // UUID como string
    $stmt->execute();
    $stmt->close();
    $resposta["imageUrl"] = $imageUrl;
  }
}

// Finaliza resposta
$resposta["status"] = "sucesso";
$resposta["mensagem"] = "Dados atualizados!";
http_response_code(200);
$conn->close();
echo json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
