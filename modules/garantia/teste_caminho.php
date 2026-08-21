<?php
$diretorio = __DIR__ . '/garantiaTemp/imagens/';

echo "Tentando salvar em: " . $diretorio . "<br>";

if (!is_dir($diretorio)) {
    echo "❌ Erro: A pasta não existe.<br>";
    // Tenta criar
    if(mkdir($diretorio, 0777, true)) {
        echo "✅ Pasta criada agora.<br>";
    } else {
        echo "❌ Erro crítico: Não foi possível criar a pasta.<br>";
    }
} else {
    echo "✅ A pasta existe.<br>";
}

if (is_writable(__DIR__ . '/garantiaTemp/')) {
    echo "✅ A pasta tem permissão de escrita.<br>";
} else {
    echo "❌ Erro: Sem permissão de escrita (chmod).<br>";
}

// Teste de gravação real
$teste = file_put_contents(__DIR__ . '/garantiaTemp/teste.txt', 'Teste de escrita ' . date('Y-m-d H:i:s'));
if ($teste !== false) {
    echo "✅ Gravação de arquivo de texto funcionou!<br>";
} else {
    echo "❌ Falha ao gravar arquivo.<br>";
}