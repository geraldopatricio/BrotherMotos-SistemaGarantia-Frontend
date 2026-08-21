<?php
// update_password_hash.php

// INICIA A SESSÃO (copiado do seu login.php, embora não estritamente necessário aqui, é boa prática)
ini_set('session.cookie_path', '/');
session_start();

header('Content-Type: text/plain'); // Para ver a saída diretamente no navegador

$env = parse_ini_file(__DIR__ . '/../.env'); // Ajuste o caminho se necessário

if (!$env) {
    die('Erro ao carregar o arquivo .env');
}

try {
    $conn = new PDO("mysql:host={$env['MYSQL_HOST']};dbname={$env['MYSQL_NAME']};charset=utf8", $env['MYSQL_USER'], $env['MYSQL_PASSWORD']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexão com o banco de dados estabelecida.\n\n";

    $usuario_alvo = "geraldo"; // Usuário cuja senha será atualizada
    $nova_senha_texto_puro = "odlareg"; // A senha original em texto puro

    // Gera o hash da nova senha
    $nova_senha_hashed = password_hash($nova_senha_texto_puro, PASSWORD_DEFAULT);

    if ($nova_senha_hashed === false) {
        die("Erro ao gerar o hash da senha. Verifique a extensão OpenSSL do PHP.");
    }

    echo "Senha original para o usuário '{$usuario_alvo}': '{$nova_senha_texto_puro}'\n";
    echo "Hash gerado: '{$nova_senha_hashed}'\n\n";

    // Atualiza a senha no banco de dados
    $stmt = $conn->prepare("UPDATE login SET senha = :senha_hashed WHERE usuario = :usuario");
    $stmt->bindParam(':senha_hashed', $nova_senha_hashed);
    $stmt->bindParam(':usuario', $usuario_alvo);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Senha do usuário '{$usuario_alvo}' atualizada com sucesso no banco de dados!\n";
        echo "Agora você pode tentar fazer login com a senha '{$nova_senha_texto_puro}' usando seu formulário.\n";
    } else {
        echo "Nenhum registro atualizado. O usuário '{$usuario_alvo}' pode não existir ou a senha já estava hashada (o que é improvável neste caso).\n";
    }

} catch (PDOException $e) {
    echo "Erro no banco de dados: " . $e->getMessage() . "\n";
}

// Opcional: Para ver o hash no console/terminal se estiver rodando via CLI
// echo $nova_senha_hashed;
?>