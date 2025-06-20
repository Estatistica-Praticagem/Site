<?php
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

// Conexão com o banco
$host = "mysql.meusimulador.com";
$user = "meusimulador";
$pass = "@OrizzontteBI2025";
$db   = "meusimulador";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection error."]);
    exit;
}

// Recebe e valida os dados
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Missing email or password."]);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = $conn->real_escape_string($data['password']);

// Primeiro verifica se o email existe
$checkUser = "SELECT id, name, password FROM users_orizzonttebi WHERE email = '$email' LIMIT 1";
$result = $conn->query($checkUser);

if ($result && $result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found."]);
} else {
    $user = $result->fetch_assoc();
    if ($user['password'] === $password) {
        echo json_encode([
            "success" => true,
            "id" => $user['id'],
            "name" => $user['name']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect password."]);
    }
}

$conn->close();
?>
