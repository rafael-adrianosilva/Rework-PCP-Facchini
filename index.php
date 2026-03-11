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
                <p><?php echo isset($_GET['regiao']) ? 'PCP ' . htmlspecialchars($_GET['regiao']) : 'Sistema PCP'; ?></p>
            </div>
            <div class="textos">
                <div class="titulo">
                    <h2>Planejamento e Controle da Produção</h2>
                </div>
                <div class="descricao">
                    <p>Gerenciamento e controle sobre seu PCP, em uma única plataforma.</p>
                </div>
            </div>
            <div class="banner-btns">
                <button type="button" onclick="showModal('upTarefas')"><i class="fas fa-upload"></i>Upload PDF</button>
            <div class="banner-btns" style="display: none;">
                <button type="button" onclick="showModal('upTarefas')"><i class="fas fa-upload"></i>Upload</button>
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

    <!-- Container que receberá os arquivos recém-enviados ...-->
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
    <section class='listagem'>
        <div class="listagem-header">
            <h3>Lista de Arquivos Disponíveis:</h3>
            <div class="">
                <p class="extra"><?php date_default_timezone_set('America/Sao_Paulo');
                                    echo date("d/m/Y") ?></p>
            </div>
        </div>
        <div class="listagem-split">
            <?php
            $regiao = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';

            $colunas = [
                'normal' => [
                    'titulo' => 'Upload Normal',
                    'icone'  => 'fa-file-alt',
                    'pasta'  => 'upload_normal',
                ],
                'kit' => [
                    'titulo' => 'Upload de Kits',
                    'icone'  => 'fa-boxes',
                    'pasta'  => 'upload_kits',
                ]
            ];

            if (!empty($regiao)) {
                $base_pcp = dirname(__DIR__) . "/pcp/documentos/pdfs/{$regiao}/";

                foreach ($colunas as $tipo => $cfg) {
                    $caminho = $base_pcp . $cfg['pasta'];
                    // Fallback para singular
                    if ($tipo === 'kit' && !is_dir($caminho)) {
                        $caminho = $base_pcp . 'upload_kit';
                    }
                    ?>
                    <div class="upload-coluna">
                        <div class="upload-coluna-header">
                            <div class="coluna-icon">
                                <i class="fas <?php echo $cfg['icone']; ?>"></i>
                            </div>
                            <div class="coluna-info">
                                <h4><?php echo $cfg['titulo']; ?></h4>
                                <span class="coluna-badge"><?php
                                    $count = 0;
                                    if (is_dir($caminho)) {
                                        $items = scandir($caminho);
                                        foreach ($items as $it) {
                                            if ($it !== '.' && $it !== '..') $count++;
                                        }
                                    }
                                    echo $count . ' ' . ($count === 1 ? 'item' : 'itens');
                                ?></span>
                            </div>
                        </div>
                        <div class="upload-coluna-body">
                            <?php
                            if (is_dir($caminho)) {
                                $arquivos = scandir($caminho);
                                $tem_item = false;
                                foreach ($arquivos as $arquivo) {
                                    if ($arquivo === '.' || $arquivo === '..') continue;
                                    $tem_item = true;
                                    $item_path = $caminho . '/' . $arquivo;
                                    $is_dir_item = is_dir($item_path);
                                    $pasta_tipo = ($tipo === 'kit') ? (strpos($caminho, 'upload_kits') !== false ? 'upload_kits' : 'upload_kit') : 'upload_normal';
                                    $caminho_relativo = "documentos/pdfs/{$regiao}/{$pasta_tipo}/{$arquivo}";

                                    if ($is_dir_item && $tipo === 'kit') {
                                        // Contar arquivos dentro da pasta
                                        $sub_count = 0;
                                        $sub_items = scandir($item_path);
                                        foreach ($sub_items as $si) {
                                            if ($si !== '.' && $si !== '..') $sub_count++;
                                        }
                                        $pasta_id = 'pasta_' . md5($arquivo);
                                        ?>
                                        <div class="arquivo-card pasta-card" onclick="document.getElementById('<?php echo $pasta_id; ?>').style.display = document.getElementById('<?php echo $pasta_id; ?>').style.display === 'none' ? 'flex' : 'none'; document.getElementById('chevron_<?php echo $pasta_id; ?>').classList.toggle('aberto');">
                                            <div class="arquivo-card-left">
                                                <div class="arquivo-card-icon pasta-icon">
                                                    <i class="fas fa-folder"></i>
                                                </div>
                                                <div class="arquivo-card-info">
                                                    <p class="arquivo-card-nome" title="<?php echo htmlspecialchars($arquivo); ?>"><?php echo htmlspecialchars($arquivo); ?></p>
                                                    <span class="arquivo-card-meta"><?php echo $sub_count; ?> arquivo(s)</span>
                                                </div>
                                            </div>
                                            <div class="arquivo-card-right">
                                                <i class="fas fa-chevron-down pasta-chevron" id="chevron_<?php echo $pasta_id; ?>"></i>
                                            </div>
                                        </div>
                                        <div class="pasta-conteudo" id="<?php echo $pasta_id; ?>" style="display: none;">
                                            <?php
                                            foreach ($sub_items as $sub_arq) {
                                                if ($sub_arq === '.' || $sub_arq === '..') continue;
                                                $sub_path = $item_path . '/' . $sub_arq;
                                                $sub_size = filesize($sub_path);
                                                $sub_size_text = $sub_size < 1048576 ? round($sub_size / 1024, 1) . ' KB' : round($sub_size / 1048576, 2) . ' MB';
                                                $sub_date = date("d/m/Y H:i", filemtime($sub_path));
                                                $sub_caminho = "documentos/pdfs/{$regiao}/{$pasta_tipo}/{$arquivo}/{$sub_arq}";
                                                ?>
                                                <div class="arquivo-card">
                                                    <div class="arquivo-card-left">
                                                        <div class="arquivo-card-icon">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </div>
                                                        <div class="arquivo-card-info">
                                                            <a href="<?php echo $sub_caminho; ?>" class="arquivo-card-nome" title="<?php echo htmlspecialchars($sub_arq); ?>" download="<?php echo htmlspecialchars($sub_arq); ?>">
                                                                <?php echo htmlspecialchars($sub_arq); ?>
                                                            </a>
                                                            <span class="arquivo-card-meta"><?php echo $sub_size_text; ?> • <?php echo $sub_date; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="arquivo-card-right">
                                                        <a href="<?php echo $sub_caminho; ?>" download="<?php echo htmlspecialchars($sub_arq); ?>" class="btn-download" title="Baixar">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php
                                    } else {
                                        // Arquivo normal
                                        $file_size = filesize($item_path);
                                        $size_text = $file_size < 1048576 ? round($file_size / 1024, 1) . ' KB' : round($file_size / 1048576, 2) . ' MB';
                                        $file_date = date("d/m/Y H:i", filemtime($item_path));
                                        ?>
                                        <div class="arquivo-card">
                                            <div class="arquivo-card-left">
                                                <div class="arquivo-card-icon">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <div class="arquivo-card-info">
                                                    <a href="<?php echo $caminho_relativo; ?>" class="arquivo-card-nome" title="<?php echo htmlspecialchars($arquivo); ?>" download="<?php echo htmlspecialchars($arquivo); ?>">
                                                        <?php echo htmlspecialchars($arquivo); ?>
                                                    </a>
                                                    <span class="arquivo-card-meta"><?php echo $size_text; ?> • <?php echo $file_date; ?></span>
                                                </div>
                                            </div>
                                            <div class="arquivo-card-right">
                                                <a href="<?php echo $caminho_relativo; ?>" download="<?php echo htmlspecialchars($arquivo); ?>" class="btn-download" title="Baixar">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                if (!$tem_item) {
                                    echo '<div class="listagem-vazio"><i class="fas fa-inbox"></i><p>Nenhum arquivo encontrado</p></div>';
                                }
                            } else {
                                echo '<div class="listagem-vazio"><i class="fas fa-inbox"></i><p>Nenhum arquivo encontrado</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="listagem-vazio-full"><i class="fas fa-map-marker-alt"></i><p>Selecione uma região para visualizar os arquivos.</p></div>';
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once 'php/components/footer.php'; ?>
    <!-- JS -->
    <script src="js/index.js" defer></script>
    <script src="js/upload.js" defer></script>
</body>

</html>