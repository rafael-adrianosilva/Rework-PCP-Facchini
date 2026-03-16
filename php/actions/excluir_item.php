<?php
header('Content-Type: application/json');

$response = array(
    'sucesso' => false,
    'mensagem' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caminho_relativo = isset($_POST['caminho']) ? trim($_POST['caminho']) : '';

    if (empty($caminho_relativo)) {
        $response['mensagem'] = 'Caminho do item não fornecido.';
        echo json_encode($response);
        exit;
    }

    // O caminho vem como "documentos/pdfs/REGIAO/TIPO/..."
    // Precisamos validar se ele não sai do diretório documentos/pdfs/
    $base_dir = realpath(__DIR__ . '/../../documentos/pdfs');
    $target_path = realpath(__DIR__ . '/../../' . $caminho_relativo);

    if (!$target_path || strpos($target_path, $base_dir) !== 0) {
        $response['mensagem'] = 'Acesso negado ou caminho inválido.';
        echo json_encode($response);
        exit;
    }

    if (is_dir($target_path)) {
        // Função para apagar pasta recursivamente
        function deleteDir($dirPath) {
            if (!is_dir($dirPath)) return false;
            $files = array_diff(scandir($dirPath), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dirPath/$file")) ? deleteDir("$dirPath/$file") : unlink("$dirPath/$file");
            }
            return rmdir($dirPath);
        }

        if (deleteDir($target_path)) {
            $response['sucesso'] = true;
            $response['mensagem'] = 'Pasta excluída com sucesso.';
        } else {
            $response['mensagem'] = 'Erro ao excluir a pasta.';
        }
    } else if (file_exists($target_path)) {
        if (unlink($target_path)) {
            $response['sucesso'] = true;
            $response['mensagem'] = 'Arquivo excluído com sucesso.';
        } else {
            $response['mensagem'] = 'Erro ao excluir o arquivo.';
        }
    } else {
        $response['mensagem'] = 'Item não encontrado.';
    }
} else {
    $response['mensagem'] = 'Método inválido.';
}

echo json_encode($response);
?>
