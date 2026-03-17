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
    <link rel="stylesheet" href="css/modal.css"> <!-- Modal -->
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
                <p><?php echo isset($_GET['regiao']) ? 'PCP ' . htmlspecialchars($_GET['regiao']) : 'Região não Definida'; ?></p>
            </div>
            <div class="textos">
                <div class="titulo">
                    <h2>Planejamento e Controle da Produção</h2>
                </div>
                <div class="descricao">
                    <p>Gerenciamento e controle sobre seu PCP, em uma única plataforma.</p>
                </div>
            </div>
            <div class="banner-btns" style="display: none;">
                <button type="button" onclick="showModal('upTarefas')"><i class="fas fa-upload"></i>Upload</button>
                <button type="button" onclick="showModal('gerTarefas')"><i class="fas fa-list"></i>Gerenciar Tarefas</button>
            </div>
        </div>
    </section>

    <?php
    // Lista de regiões válidas — única fonte de verdade
    $regioes_validas = ['Votuporanga', 'Rio Preto 1', 'Rio Preto 2', 'Roseira', 'Mirassol', 'Aparecida Taboado'];
    $regiao = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';
    // Validar a região recebida contra a lista
    if (!in_array($regiao, $regioes_validas)) {
        $regiao = '';
    }
    ?>

    <section class='listagem'>
        <div class="listagem-header">
            <h3><?php echo !empty($regiao) ? 'Arquivos Disponíveis — ' . htmlspecialchars($regiao) : 'Região Indefinida'; ?></h3>
            <div class="">
                <p class="extra"><?php date_default_timezone_set('America/Sao_Paulo'); echo date("d/m/Y") ?></p>
            </div>
        </div>
        <?php if (!empty($regiao)): ?>
        <div class="listagem-pesquisa">
            <input type="text" placeholder="Pesquise os PDFs e Pastas"><button><i class="fas fa-search"></i></button>
        </div>
        <?php endif; ?>
        <div class="listagem-split">
            <?php
            $tipos = ['normal', 'kit'];

            if (!empty($regiao)) {
                // == MODO: Visualização de uma região específica ==
                $base_pcp = __DIR__ . "/documentos/pdfs/{$regiao}/";

                foreach ($tipos as $tipo) {
                    $titulo = ($tipo === 'normal') ? 'PDFS' : 'Kits de PDFS';
                    $icone = ($tipo === 'normal') ? 'fa-file-alt' : 'fa-boxes';
                    $pasta = ($tipo === 'normal') ? 'upload_normal' : 'upload_kits';
                    
                    $caminho = $base_pcp . $pasta;
                    // Fallback para singular
                    if ($tipo === 'kit' && !is_dir($caminho)) {
                        $caminho = $base_pcp . 'upload_kit';
                    }
                    
                    echo '<div class="upload-coluna">';
                    echo '    <div class="upload-coluna-header">';
                    echo '        <div class="coluna-icon">';
                    echo '            <i class="fas ' . $icone . '"></i>';
                    echo '        </div>';
                    echo '        <div class="coluna-info">';
                    echo '            <h4>' . $titulo . '</h4>';
                    echo '            <span class="coluna-badge">';
                                      $count = 0;
                                      if (is_dir($caminho)) {
                                          $items = scandir($caminho);
                                          foreach ($items as $it) {
                                              if ($it !== '.' && $it !== '..') $count++;
                                          }
                                      }
                                      echo $count . ' ' . ($count === 1 ? 'item' : 'itens');
                    echo '            </span>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '    <div class="upload-coluna-body">';
                    
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
                                
                                echo '        <div class="arquivo-card pasta-card" onclick="document.getElementById(\'' . $pasta_id . '\').style.display = document.getElementById(\'' . $pasta_id . '\').style.display === \'none\' ? \'flex\' : \'none\'; document.getElementById(\'chevron_' . $pasta_id . '\').classList.toggle(\'aberto\');">';
                                echo '            <div class="arquivo-card-left">';
                                echo '                <div class="arquivo-card-icon pasta-icon">';
                                echo '                    <i class="fas fa-folder"></i>';
                                echo '                </div>';
                                echo '                <div class="arquivo-card-info">';
                                echo '                    <p class="arquivo-card-nome" title="' . htmlspecialchars($arquivo) . '">' . htmlspecialchars($arquivo) . '</p>';
                                echo '                    <span class="arquivo-card-meta">' . $sub_count . ' arquivo(s)</span>';
                                echo '                </div>';
                                echo '            </div>';
                                echo '            <div class="arquivo-card-right">';
                                echo '                <i class="fas fa-chevron-down pasta-chevron" id="chevron_' . $pasta_id . '"></i>';
                                echo '            </div>';
                                echo '        </div>';
                                echo '        <div class="pasta-conteudo" id="' . $pasta_id . '" style="display: none;">';
                                
                                foreach ($sub_items as $sub_arq) {
                                    if ($sub_arq === '.' || $sub_arq === '..') continue;
                                    $sub_path = $item_path . '/' . $sub_arq;
                                    $sub_date = date("d/m/Y H:i", filemtime($sub_path));
                                    $sub_caminho = "documentos/pdfs/{$regiao}/{$pasta_tipo}/{$arquivo}/{$sub_arq}";
                                    
                                    echo '            <div class="arquivo-card">';
                                    echo '                <div class="arquivo-card-left">';
                                    echo '                    <div class="arquivo-card-icon">';
                                    echo '                        <i class="fas fa-file-pdf"></i>';
                                    echo '                    </div>';
                                    echo '                    <div class="arquivo-card-info">';
                                    echo '                        <a href="' . $sub_caminho . '" class="arquivo-card-nome" title="' . htmlspecialchars($sub_arq) . '" download="' . htmlspecialchars($sub_arq) . '">';
                                    echo '                            ' . htmlspecialchars($sub_arq);
                                    echo '                        </a>';
                                    echo '                        <span class="arquivo-card-meta">' . $sub_date . '</span>';
                                    echo '                    </div>';
                                    echo '                </div>';
                                    echo '                <div class="arquivo-card-right">';
                                    echo '                    <a href="' . $sub_caminho . '" download="' . htmlspecialchars($sub_arq) . '" class="btn-download" title="Baixar">';
                                    echo '                        <i class="fas fa-download"></i>';
                                    echo '                    </a>';
                                    echo '                </div>';
                                    echo '            </div>';
                                }
                                echo '        </div>';
                            } else {
                                // Arquivo normal
                                $file_date = date("d/m/Y H:i", filemtime($item_path));
                                
                                echo '        <div class="arquivo-card">';
                                echo '            <div class="arquivo-card-left">';
                                echo '                <div class="arquivo-card-icon">';
                                echo '                    <i class="fas fa-file-pdf"></i>';
                                echo '                </div>';
                                echo '                <div class="arquivo-card-info">';
                                echo '                    <a href="' . $caminho_relativo . '" class="arquivo-card-nome" title="' . htmlspecialchars($arquivo) . '" download="' . htmlspecialchars($arquivo) . '">';
                                echo '                        ' . htmlspecialchars($arquivo);
                                echo '                    </a>';
                                echo '                    <span class="arquivo-card-meta">' . $file_date . '</span>';
                                echo '                </div>';
                                echo '            </div>';
                                echo '            <div class="arquivo-card-right">';
                                echo '                <a href="' . $caminho_relativo . '" download="' . htmlspecialchars($arquivo) . '" class="btn-download" title="Baixar">';
                                echo '                    <i class="fas fa-download"></i>';
                                echo '                </a>';
                                echo '            </div>';
                                echo '        </div>';
                            }
                        }
                        if (!$tem_item) {
                            echo '<div class="listagem-vazio"><i class="fas fa-inbox"></i><p>Nenhum arquivo encontrado</p></div>';
                        }
                    } else {
                        echo '<div class="listagem-vazio"><i class="fas fa-inbox"></i><p>Nenhum arquivo encontrado</p></div>';
                    }
                    
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                // == MODO: Dashboard Global — Visualiza todas as regiões ==
                echo '<div class="listagem-vazio-full" style="flex-direction: column; gap: 20px;">';
                echo '<i class="fas fa-exclamation-triangle" style="font-size: var(--txt-2xl    ); color: var(--corBase);"></i>';
                echo '<h3 style="color: var(--corTxt3);">Região não definida ou inválida</h3>';
                echo '</div>';
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