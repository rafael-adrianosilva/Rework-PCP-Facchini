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
            <div class="banner-btns" style="display: flex;">
                <button type="button" onclick="showModal('upTarefas')"><i class="fas fa-upload"></i>Upload</button>
                <button type="button" style="display: none;" onclick="showModal('gerTarefas')"><i class="fas fa-list"></i>Gerenciar Tarefas</button>
            </div>
        </div>
    </section>

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
                    'cor'    => '#e74c3c'
                ],
                'kit' => [
                    'titulo' => 'Upload de Kits',
                    'icone'  => 'fa-boxes',
                    'pasta'  => 'upload_kits',
                    'cor'    => '#e67e22'
                ]
            ];

            if (!empty($regiao)) {
                $base_pcp = dirname(__DIR__) . "/pcp/documentos/pdfs/{$regiao}/";

                foreach ($colunas as $tipo => $cfg) {
                    $caminho = $base_pcp . $cfg['pasta'];
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