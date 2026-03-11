<?php
header('Content-Type: application/json');

$diretorio_base = '../documentos/';

$pastas = [
    'upload_normal' => $diretorio_base . 'upload_normal/',
    'upload_kit' => $diretorio_base . 'upload_kit/',
];

$arquivos = [];

foreach ($pastas as $tipo => $caminho) {
    if (!is_dir($caminho)) {
        continue;
    }

    $lista = glob($caminho . '*.pdf');
    if (!$lista) {
        continue;
    }

    foreach ($lista as $arquivo) {
        $nome = basename($arquivo);
        $tamanho = filesize($arquivo);
        $modificado = filemtime($arquivo);

        $arquivos[] = [
            'nome' => $nome,
            'tipo' => $tipo,
            'tamanho' => $tamanho,
            'data' => date('d/m/Y H:i', $modificado),
            'timestamp' => $modificado,
        ];
    }
}

// Ordena por data mais recente
usort($arquivos, function ($a, $b) {
    return $b['timestamp'] - $a['timestamp'];
});

echo json_encode([
    'sucesso' => true,
    'arquivos' => $arquivos,
    'total' => count($arquivos),
]);
?>
