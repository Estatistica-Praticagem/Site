<?php

require_once __DIR__ . '/config.php';


$allowed_origins = [
    'https://www.meusimulador.com',
    'https://meusimulador.com',
    'https://www.orizzonttebi.com',
    'https://orizzonttebi.com'
];

// Normaliza: remove barra no final, se existir
$origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';

// Verifica se a origem está na lista permitida
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Você pode logar aqui se quiser saber de onde veio
    // file_put_contents(__DIR__.'/debug_origin.txt', "Bloqueado: $origin\n", FILE_APPEND);

    header("Access-Control-Allow-Origin: https://www.meusimulador.com"); // fallback seguro
}

// Cabeçalhos obrigatórios para CORS
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Responde as requisições OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


$logs = [];
function log_msg($mensagem) {
    global $logs;
    $logs[] = "[" . date('H:i:s') . "] $mensagem";
}

log_msg("📥 Requisição recebida");
$data = json_decode(file_get_contents("php://input"), true);
log_msg("📦 Dados recebidos: " . json_encode($data));

// Lista de palavras proibidas
function contemConteudoProibido($texto) {
  $palavras = array_merge([
    "buceta","xereca","xoxota","vagina","clitoris","xana","pau","caralho","rola",
    "piroca","penis","pênis","c@ralho","c*ralho","foda","foder","foda-se","fudido",
    "fudida","sexo","transar","boquete","gozada","tarado","siririca","masturbação",
    "ejacular","putaria","puta","puto","putinha","vagabunda","tarada","safada",
    "nudes","nude","gemido","tesão","gemendo","sacanagem","safado","gozar"
  ], [
    "merda","porra","cocô","bosta","cacet*","cacete","fdp","f*d*","vtc","vai tnc",
    "diabo","capeta","arrombado","corno","vagabundo","nojento","escroto","viado",
    "gayzinho","sapatão","traveco","travec*","macaco","macaca","preto fedido",
    "crente lixo","terrorista","verme","escória"
  ]);

  $texto = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $texto));
  foreach ($palavras as $palavra) {
    if (strpos($texto, $palavra) !== false) {
      return true;
    }
  }

  // Verifica se contém link
  if (preg_match('/(https?:\/\/|www\.)\S+/i', $texto)) {
    return true;
  }

  return false;
}

// ------- VERIFICAÇÃO reCAPTCHA ANTES DE TUDO --------
function verificarRecaptcha($token) {
    $secret = RECAPTCHA_SECRET_KEY;
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($url . '?secret=' . $secret . '&response=' . $token);
    $data = json_decode($response);
    return isset($data->success) && $data->success;
}

if (!isset($data['g-recaptcha-response']) || !verificarRecaptcha($data['g-recaptcha-response'])) {
    log_msg("❌ reCAPTCHA inválido.");
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "reCAPTCHA inválido.", "logs" => $logs]);
    exit;
}

// -------- FIM reCAPTCHA --------

if (!isset($data['nome'], $data['email'], $data['ddd'], $data['telefone'], $data['servico'], $data['descricao'])) {
    log_msg("❌ Campos obrigatórios ausentes.");
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields.", "logs" => $logs]);
    exit;
}

// ------------- FILTRO DE CONTEÚDO PROIBIDO ---------------
if (contemConteudoProibido($data['nome']) || contemConteudoProibido($data['descricao'])) {
    log_msg("❌ Conteúdo proibido detectado nos campos do formulário.");
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "O texto contém palavras proibidas, links ou termos ofensivos.",
        "logs" => $logs
    ]);
    exit;
}
// ----------------------------------------------------------

$name         = $data['nome'];
$email        = $data['email'];
$country_code = $data['ddd'];
$phone        = $data['telefone'];
$service      = $data['servico'];
$message      = $data['descricao'];
$created_at   = date('Y-m-d H:i:s');

// Inserção no MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    log_msg("❌ Erro MySQL: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro na conexão com o banco.", "logs" => $logs]);
    exit;
}

$sql = "INSERT INTO contacts (name, email, country_code, phone, service, message)
        VALUES ('$name', '$email', '$country_code', '$phone', '$service', '$message')";
if (!$conn->query($sql)) {
    log_msg("❌ Erro ao inserir no MySQL: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao inserir no banco de dados.", "logs" => $logs]);
    exit;
}

$conn->close();
log_msg("✅ Inserido no MySQL");

// Inserção no BigQuery
require_once __DIR__ . '/vendor/autoload.php';
use Google\Cloud\BigQuery\BigQueryClient;

try {
    putenv("GOOGLE_APPLICATION_CREDENTIALS=" . BQ_CREDENTIALS_PATH);

    $bigQuery = new BigQueryClient([
        'projectId' => BQ_PROJECT_ID,
        'keyFilePath' => BQ_CREDENTIALS_PATH
    ]);

    log_msg("🔐 Conectado ao BigQuery");

    $dataset = $bigQuery->dataset(BQ_DATASET_ID);
    $table = $dataset->table(BQ_TABLE_ID);


    $insert = $table->insertRows([
        ['data' => [
            'name'         => $name,
            'email'        => $email,
            'country_code' => $country_code,
            'phone'        => $phone,
            'service'      => $service,
            'message'      => $message,
            'created_at'   => date('c')
        ]]
    ]);

    if ($insert->isSuccessful()) {
        log_msg("✅ Enviado ao BigQuery com sucesso.");
        echo json_encode([
            "success" => true,
            "message" => "Dados salvos em MySQL e BigQuery com sucesso.",
            "logs" => $logs
        ]);
    } else {
        $erros = [];
        foreach ($insert->info()['insertErrors'] ?? [] as $row) {
            $erros[] = json_encode($row['errors']);
        }
        log_msg("❌ Falha no BigQuery: " . implode(" | ", $erros));
        echo json_encode([
            "success" => false,
            "message" => "Erro ao enviar para o BigQuery.",
            "logs" => $logs
        ]);
    }

} catch (Exception $e) {
    log_msg("❌ Exceção BigQuery: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Erro na integração com o BigQuery.",
        "logs" => $logs
    ]);
}
