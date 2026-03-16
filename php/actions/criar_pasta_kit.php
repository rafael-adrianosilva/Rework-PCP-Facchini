<?php
header('Content-Type: application/json');

$response = array(
    'sucesso' => false,
    'mensagem' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_pasta = isset($_POST['nome_pasta']) ? trim($_POST['nome_pasta']) : '';
    $regiao = isset($_POST['regiao']) ? trim($_POST['regiao']) : '';

    if (empty($regiao)) {
        $response['mensagem'] = 'Região não selecionada.';
        echo json_encode($response);
        exit;
    }

    if (empty($nome_pasta)) {
        $response['mensagem'] = 'Nome do Kit não pode estar vazio.';
        echo json_encode($response);
        exit;
    }

    // Sanitiza o nome da pasta e substitui espaços por '_'
    $nome_pasta = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $nome_pasta);
    $nome_pasta = trim($nome_pasta);
    $nome_pasta = str_replace(' ', '_', $nome_pasta);

    if (empty($nome_pasta)) {
        $response['mensagem'] = 'Nome do Kit inválido após sanitização.';
        echo json_encode($response);
        exit;
    }

    $diretorio_base = '../../documentos/pdfs/';
    $pasta_destino = $diretorio_base . $regiao . '/upload_kits/' . $nome_pasta;

    if (is_dir($pasta_destino)) {
        $response['mensagem'] = 'Um Kit com este nome já existe.';
        echo json_encode($response);
        exit;
    }

    if (mkdir($pasta_destino, 0777, true)) {
        $response['sucesso'] = true;
        $response['mensagem'] = 'Kit criado com sucesso.';
        $response['nome_pasta'] = $nome_pasta;
    } else {
        $response['mensagem'] = 'Falha ao criar a pasta no servidor.';
    }
} else {
    $response['mensagem'] = 'Método de requisição inválido.';
}

echo json_encode($response);
?>
