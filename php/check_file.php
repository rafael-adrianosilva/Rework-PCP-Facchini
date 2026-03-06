<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_upload = isset($_POST['tipo_upload']) ? $_POST['tipo_upload'] : 'normal';
    $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : '';
    $filename = isset($_POST['filename']) ? $_POST['filename'] : '';

    if (empty($filename)) {
        echo json_encode(['existe' => false, 'erro' => 'Nome do arquivo não fornecido']);
        exit;
    }

    $diretorio_base = '../documentos/pdfs/';
    $pasta_destino = $diretorio_base . $regiao . '/' . ($tipo_upload === 'kit' ? 'upload_kits' : 'upload_normal') . '/';
    $diretorio_base = '../documentos/';
    $pasta_destino = ($tipo_upload === 'kit') ? $diretorio_base . 'upload_kit/' : $diretorio_base . 'upload_normal/';

    // Padroniza o nome do arquivo da mesma forma que o upload
    $nomeSanitizado = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
    $caminhoCompleto = $pasta_destino . $nomeSanitizado;

    if (file_exists($caminhoCompleto)) {
        echo json_encode(['existe' => true]);
    }
    else {
        echo json_encode(['existe' => false]);
    }
}
else {
    echo json_encode(['existe' => false, 'erro' => 'Método inválido']);
}
?>
