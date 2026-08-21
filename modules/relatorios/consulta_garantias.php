<?php
$allowedOrigin = $_ENV['ALLOWED_ORIGIN'] ?? '';

if (!empty($allowedOrigin)) {
    header("Access-Control-Allow-Origin: {$allowedOrigin}");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('MYSQL_HOST') ?: 'garantia_db';
$port = getenv('MYSQL_PORT') ?: '3306';
$database = getenv('MYSQL_NAME') ?: 'db_garantia';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: 'odlareg';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT 
            g.id AS protocolo,
            DATE_FORMAT(g.dt_cad, '%d/%m/%Y') AS dt_cad,
            DATE_FORMAT(g.dt_cadastro, '%d/%m/%Y %H:%i:%s') AS dt_cadastro,
            c.CODCLI,
            c.CLIENTE,
            g.codusur,
            r.NOME,
            g.responsavel,
            COALESCE(g.status, 'Em Digitação') AS status 
        FROM garantia g
        LEFT JOIN winthor_pcclient c ON g.codcli = c.CODCLI
        LEFT JOIN winthor_pcusuari r ON g.codusur = r.CODUSUR
        WHERE 1=1
    ";

    $params = [];

    // FILTRO POR NOME DO CLIENTE (funcionará melhor)
    if (isset($_GET['nomeCliente']) && !empty($_GET['nomeCliente'])) {
        $sql .= " AND c.CLIENTE LIKE :nomeCliente";
        $params[':nomeCliente'] = '%' . $_GET['nomeCliente'] . '%';
    }

    // FILTRO POR CÓDIGO RCA (este funciona)
    if (isset($_GET['codigoRCA']) && !empty($_GET['codigoRCA'])) {
        $sql .= " AND g.codusur = :codigoRCA";
        $params[':codigoRCA'] = $_GET['codigoRCA'];
    }

    $sql .= " ORDER BY g.id DESC";

    $stmt = $conn->prepare($sql);
    
    // Bind dos parâmetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $dados,
        'count' => count($dados),
        'filters' => array_keys($params)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao consultar garantias: ' . $e->getMessage()
    ]);
}
?>