<?php
header('Content-Type: application/json');

$response = array(
    'sucesso' => false,
    'mensagem' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo_upload = isset($_POST['tipo_upload']) ? $_POST['tipo_upload'] : 'normal';
    $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : '';

    if (empty($regiao)) {
        $response['mensagem'] = 'Região não selecionada.';
        echo json_encode($response);
        exit;
    }

    $diretorio_base = '../documentos/pdfs/';

    $pasta_destino = $diretorio_base . $regiao . '/' . ($tipo_upload === 'kit' ? 'upload_kits' : 'upload_normal') . '/';

    if (!is_dir($pasta_destino)) {
        if (!mkdir($pasta_destino, 0777, true)) {
            $response['mensagem'] = 'Falha ao criar o diretório destino.';
            echo json_encode($response);
            exit;
        }
    }

    if (isset($_FILES['arquivos']) && is_array($_FILES['arquivos']['name'])) {
        $totalArquivos = count($_FILES['arquivos']['name']);
        $arquivosSalvos = 0;
        $erros = [];

        for ($i = 0; $i < $totalArquivos; $i++) {
            $nomeOriginal = $_FILES['arquivos']['name'][$i];
            $tmpName = $_FILES['arquivos']['tmp_name'][$i];
            $erro = $_FILES['arquivos']['error'][$i];
            $tamanho = $_FILES['arquivos']['size'][$i];
            $tipo = $_FILES['arquivos']['type'][$i];

            if ($erro !== UPLOAD_ERR_OK) {
                $erros[] = "Erro ao enviar o arquivo $nomeOriginal. Código: $erro.";
                continue;
            }

            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            if ($extensao !== 'pdf' && $tipo !== 'application/pdf') {
                $erros[] = "O arquivo $nomeOriginal não é um PDF válido.";
                continue;
            }

            if ($tamanho > 5 * 1024 * 1024) {
                $erros[] = "O arquivo $nomeOriginal excede 5MB.";
                continue;
            }

            $nomeSanitizado = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nomeOriginal);
            $caminhoCompleto = $pasta_destino . $nomeSanitizado;

            if (file_exists($caminhoCompleto)) {
                $erros[] = "O arquivo $nomeOriginal já existe no servidor e foi pulado.";
                continue;
            }

            if (move_uploaded_file($tmpName, $caminhoCompleto)) {
                $arquivosSalvos++;
            }
            else {
                $erros[] = "Não foi possível mover o arquivo $nomeOriginal para o diretório final.";
            }
        }

        if ($arquivosSalvos > 0) {
            $response['sucesso'] = true;
            $msgBase = "$arquivosSalvos arquivo(s) salvos em '" . ($tipo_upload === 'kit' ? 'upload_kits' : 'upload_normal') . "'.";
            if (count($erros) > 0) {
                $msgBase .= " Contudo, houve erros: " . implode(" ", $erros);
            }
            $response['mensagem'] = $msgBase;
        }
        else {
            $response['mensagem'] = "Nenhum arquivo foi salvo. Motivo(s): " . implode(" ", $erros);
        }

    }
    else {
        $response['mensagem'] = 'Nenhum arquivo recebido ou formato incorreto.';
    }
}
else {
    $response['mensagem'] = 'Método de requisição inválido.';
}

echo json_encode($response);
?>
