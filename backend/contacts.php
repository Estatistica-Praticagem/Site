<?php
// Habilita CORS apenas para o domínio autorizado
header("Access-Control-Allow-Origin: https://www.orizzonttebi.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Responde a requisição de pré-verificação (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexão com o banco
$host = "mysql.meusimulador.com";
$usuario = "meusimulador";
$senha = "@OrizzontteBI2025";
$banco = "meusimulador";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Lê o corpo da requisição JSON
$data = json_decode(file_get_contents("php://input"), true);

// Verifica se todos os campos existem
if (
    !isset($data['nome']) || !isset($data['email']) || !isset($data['ddd']) ||
    !isset($data['telefone']) || !isset($data['servico']) || !isset($data['descricao'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

// Escapa os dados
$name = $conn->real_escape_string($data['nome']);
$email = $conn->real_escape_string($data['email']);
$country_code = $conn->real_escape_string($data['ddd']);
$phone = $conn->real_escape_string($data['telefone']);
$service = $conn->real_escape_string($data['servico']);
$message = $conn->real_escape_string($data['descricao']);

// Insere no banco
$sql = "INSERT INTO contacts (name, email, country_code, phone, service, message)
        VALUES ('$name', '$email', '$country_code', '$phone', '$service', '$message')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Contact saved successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
