<?php
header('Content-Type: application/json');

$response = array(
    'sucesso' => false,
    'mensagem' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regiao = isset($_POST['regiao']) ? trim($_POST['regiao']) : '';
    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : ''; // 'upload_normal' ou 'upload_kits'

    if (empty($regiao) || empty($tipo)) {
        $response['mensagem'] = 'Parâmetros insuficientes.';
        echo json_encode($response);
        exit;
    }

    $pasta_alvo = realpath(__DIR__ . "/../../documentos/pdfs/{$regiao}/{$tipo}");
    $base_dir = realpath(__DIR__ . '/../../documentos/pdfs');

    if (!$pasta_alvo || strpos($pasta_alvo, $base_dir) !== 0) {
        $response['mensagem'] = 'Diretório não encontrado ou acesso negado.';
        echo json_encode($response);
        exit;
    }

    function clearDir($dirPath) {
        if (!is_dir($dirPath)) return false;
        $files = array_diff(scandir($dirPath), array('.', '..'));
        foreach ($files as $file) {
            $path = "$dirPath/$file";
            if (is_dir($path)) {
                // Função recursiva para apagar subpastas
                function deleteRecursive($d) {
                    $items = array_diff(scandir($d), array('.', '..'));
                    foreach ($items as $item) {
                        (is_dir("$d/$item")) ? deleteRecursive("$d/$item") : unlink("$d/$item");
                    }
                    return rmdir($d);
                }
                deleteRecursive($path);
            } else {
                unlink($path);
            }
        }
        return true;
    }

    if (clearDir($pasta_alvo)) {
        $response['sucesso'] = true;
        $response['mensagem'] = 'Todos os arquivos foram removidos com sucesso.';
    } else {
        $response['mensagem'] = 'Erro ao limpar o diretório.';
    }
} else {
    $response['mensagem'] = 'Método inválido.';
}

echo json_encode($response);
?>
