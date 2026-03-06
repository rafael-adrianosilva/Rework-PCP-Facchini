<?php require_once "php/components/modals.php"; ?>
<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facchini PCP</title>

    <!-- Css -->
    <link rel="stylesheet" href="css/global.css"> <!-- Global -->
    <link rel="stylesheet" href="css/nav.css"> <!-- Nav -->
    <link rel="stylesheet" href="css/footer.css"> <!-- Footer -->
    <link rel="stylesheet" href="css/modal.css"> <!-- Footer -->
    <link rel="stylesheet" href="css/index.css"> <!-- Index -->
    <link rel="stylesheet" href="assets/vendor/fontawesome-free/css/all.min.css"> <!-- FontAwesome - Icons -->

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
</head>

<body>
    <!-- NavBar -->
    <?php require_once 'php/components/nav.php'; ?>
    <!-- Banner -->
    <section class="banner">
        <div class="banner-txt">
            <div class="extra">
                <div class="ponto"></div>
                <p>Sistema PCP</p>
            </div>
            <div class="textos">
                <div class="titulo">
                    <h1>FACCHINI</h1>
                    <h2>Planejamento e Controle da Produção</h2>
                </div>
                <div class="descricao">
                    <p>Gerenciamento e controle sobre seu PCP, em uma única plataforma.</p>
                </div>
            </div>
            <div class="banner-btns">
                <button type="button" onclick="showModal('upTarefas')"><i class="fas fa-upload"></i>Upload PDF</button>
                <button type="button" onclick="showModal('gerTarefas')"><i class="fas fa-list"></i>Gerenciar Tarefas</button>
            </div>

        </div>
    </section>
    <section class="message">
        <div>
            <h4>Arquivos Enviados Recentemente</h4>
            <p>Acompanhe as ultimas tarefas e documentos envados.</p>
        </div>
    </section>

    <hr>

    <!-- Container que receberá os arquivos recém-enviados -->
    <div id="container-recentes" style="max-width: 1200px; margin: 20px auto; padding: 0 10px; display: flex; flex-direction: column; gap: 9px;">
        <?php
        $pastas = [
            'kit' => 'documentos/upload_kit/',
            'normal' => 'documentos/upload_normal/'
        ];
        $arquivos_recentes = [];

        foreach ($pastas as $tipo => $caminho) {
            if (is_dir($caminho)) {
                $files = scandir($caminho);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $caminho_completo = $caminho . $file;
                        if (is_file($caminho_completo)) {
                            $arquivos_recentes[] = [
                                'name' => $file,
                                'path' => $caminho_completo,
                                'time' => filemtime($caminho_completo),
                                'size' => filesize($caminho_completo),
                                'tipo' => $tipo
                            ];
                        }
                    }
                }
            }
        }

        usort($arquivos_recentes, function($a, $b) {
            return $b['time'] - $a['time']; // Ordena do mais recente pro mais antigo
        });

        if (count($arquivos_recentes) > 0) {
            foreach ($arquivos_recentes as $arquivo) {
                // Configuração de data
                $dataAtual = new DateTime('@' . $arquivo['time']);
                $dataAtual->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                $dataFormatada = $dataAtual->format('d/m/Y \à\s H:i');
                
                // Formatação tamanho
                $fileSizeInfo = $arquivo['size'] / 1024 / 1024;
                $sizeText = $fileSizeInfo < 1 ? number_format($arquivo['size'] / 1024, 2) . ' KB' : number_format($fileSizeInfo, 2) . ' MB';
                
                // Badge
                $badgeCustom = $arquivo['tipo'] === 'kit' 
                    ? '<span style="background-color: #ff9800; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: 12px; letter-spacing: 0.5px;">KIT</span>' 
                    : '<span style="background-color: #007bff; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: 12px; letter-spacing: 0.5px;">NORMAL</span>';
                ?>
                
                <div class="recente-item animated-entry" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; background-color: #ffffff; border: 1px solid #e1e1e1; border-radius: 8px; box-shadow: 0 3px 8px rgba(0,0,0,0.04); transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.08)';" onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 8px rgba(0,0,0,0.04)';">
                    <div style="display: flex; align-items: center; gap: 18px; max-width: 75%;">
                        <div style="color: #d93025; font-size: 28px; background: rgba(217,48,37,0.1); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div style="display: flex; flex-direction: column; overflow: hidden;">
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <strong style="color: #333; font-size: 15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($arquivo['name']); ?></strong>
                                <?php echo $badgeCustom; ?>
                            </div>
                            <div style="color: #777; font-size: 13px; display: flex; gap: 8px;">
                                <span><i class="fas fa-weight-hanging" style="font-size: 11px; margin-right: 4px;"></i><?php echo $sizeText; ?></span> • 
                                <span><i class="far fa-clock" style="font-size: 11px; margin-right: 4px;"></i>Enviado em <?php echo $dataFormatada; ?></span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span style="background-color: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 6px;"></i> Concluído
                        </span>
                        <button onclick="alert('Funcionalidade de detalhar futuramente!')" style="background: none; border: 1px solid #ddd; padding: 6px 12px; border-radius: 5px; cursor: pointer; color: #555; font-weight: 600; font-size: 13px; transition: 0.2s;">
                            Detalhes
                        </button>
                    </div>
                </div>

                <?php
            }
        } else {
            echo '<p style="text-align: center; color: #777; margin-top: 20px;">Nenhum arquivo enviado ainda.</p>';
        }
        ?>
    </div>

    <!-- Footer -->
    <?php require_once 'php/components/footer.php'; ?>
    <!-- JS -->
    <script src="js/index.js" defer></script>
    <script src="js/upload.js" defer></script>
</body>

</html>