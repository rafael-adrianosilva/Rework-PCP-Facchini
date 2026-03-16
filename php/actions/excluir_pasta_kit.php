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
        $response['mensagem'] = 'Nome do Kit não fornecido.';
        echo json_encode($response);
        exit;
    }

    // Sanitiza apenas para garantir segurança e validação do diretório
    $nome_pasta = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $nome_pasta);

    if (empty($nome_pasta)) {
        $response['mensagem'] = 'Nome do Kit inválido.';
        echo json_encode($response);
        exit;
    }

    $diretorio_base = '../../documentos/pdfs/';
    $pasta_alvo = $diretorio_base . $regiao . '/upload_kits/' . $nome_pasta;

    // Verificar se existe e é diretório
    if (!is_dir($pasta_alvo)) {
        $response['mensagem'] = 'Kit não encontrado no servidor.';
        echo json_encode($response);
        exit;
    }

    // Função recursiva para deletar pasta e seus arquivos
    function deletarPastaRecursiva($dir) {
        if (!is_dir($dir)) return false;
        
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? deletarPastaRecursiva("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    if (deletarPastaRecursiva($pasta_alvo)) {
        $response['sucesso'] = true;
        $response['mensagem'] = 'Kit excluído com sucesso.';
    } else {
        $response['mensagem'] = 'Falha ao tentar excluir a pasta ou seus arquivos do servidor.';
    }

} else {
    $response['mensagem'] = 'Método de requisição inválido.';
}

echo json_encode($response);
?>
