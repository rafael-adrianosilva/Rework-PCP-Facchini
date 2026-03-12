<!-- Modal Gerenciar Tarefas -->
<div class="modal-fundo" id="gerTarefas" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2>GERENCIAR TAREFAS</h2>
            <button type="button" onclick="closeModal('gerTarefas')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-subheader">
                <p>Selecione as ordens para processamento em lote ou alteração de status.</p>
            </div>
            <div class="modal-body-split">
                <?php
                $regiao = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';
                $total_tarefas = 0;
                $tarefas_selecionadas = 0;

                $config_diretorios = [
                    'Normal' => [
                        'titulo' => 'UPLOAD NORMAL',
                        'pasta' => 'upload_normal',
                        'cor' => 'var(--corBase)'
                    ],
                    'Kit' => [
                        'titulo' => 'UPLOAD KIT',
                        'pasta' => 'upload_kits',
                        'cor' => 'var(--corBase)'
                    ]
                ];

                if (!empty($regiao)) {
                    $base_pcp = dirname(__DIR__, 2);

                    foreach ($config_diretorios as $tipo => $config) {
                        $caminho = $base_pcp . "/documentos/pdfs/{$regiao}/" . $config['pasta'];
                        
                        // Fallback para 'upload_kit' no singular
                        if ($tipo === 'Kit' && !is_dir($caminho)) {
                            $caminho = $base_pcp . "/documentos/pdfs/{$regiao}/upload_kit";
                        }

                        echo '<div class="tarefas-coluna">';
                        echo '<h3>' . $config['titulo'] . '</h3>';
                        echo '<div class="tarefas-grid">';

                        $tarefas_na_coluna = 0;
                        if (is_dir($caminho)) {
                            $arquivos = scandir($caminho);
                            foreach ($arquivos as $arquivo) {
                                if ($arquivo !== '.' && $arquivo !== '..') {
                                    $total_tarefas++;
                                    $tarefas_na_coluna++;
                                    $caminho_item = $caminho . '/' . $arquivo;
                                    $is_dir = is_dir($caminho_item);
                                    $caminho_relativo = "documentos/pdfs/{$regiao}/" . ($tipo === 'Kit' ? (strpos($caminho, 'upload_kits') !== false ? 'upload_kits' : 'upload_kit') : 'upload_normal') . "/{$arquivo}";
                                    ?>
                                    <div class="tarefa-card">
                                        <div class="tarefa-info">
                                            <div class="tarefa-checkbox">
                                                <input type="checkbox" name="tarefas[]" value="<?php echo htmlspecialchars($arquivo); ?>" onchange="updateCounter()">
                                            </div>
                                            <div class="tarefa-icon">
                                                <i class="fas <?php echo $is_dir ? 'fa-folder' : 'fa-file-pdf'; ?>"></i>
                                            </div>
                                            <div class="tarefa-detalhes">
                                                <a href="<?php echo $caminho_relativo; ?>" class="tarefa-nome" <?php echo $is_dir ? '' : 'download="' . htmlspecialchars($arquivo) . '"'; ?>>
                                                    <?php echo htmlspecialchars($arquivo); ?>
                                                </a>
                                                <div class="tarefa-meta">
                                                    <span>#<?php echo $tarefas_na_coluna; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tarefa-acoes">
                                            <span class="status-badge pendente">PENDENTE</span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }

                        if ($tarefas_na_coluna === 0) {
                            echo "<div class='vazio'>Nenhuma tarefa encontrada</div>";
                        }
                        echo '</div></div>';
                    }
                } else {
                    echo "<p class='vazio'>Selecione uma região para gerenciar tarefas.</p>";
                }
                ?>
            </div>
        </div>
        <div class="modal-footer-custom">
            <p id="counter-text"><span id="selected-count">0</span> tarefas selecionadas para ação em lote.</p>
            <button class="btn-concluir-lote" onclick="checkTarefa()">
                <i class="fas fa-check-circle"></i> CONCLUIR SELECIONADOS
            </button>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal-fundo" id="upTarefas" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Upload<span id='tipo_up'> Normal</span> <?php echo $_GET['regiao']; ?></h2>
            <button type="button" onclick="closeModal('upTarefas')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Campo oculto para armazenar a região selecionada via Navbar -->
            <input type="hidden" name="regiao" id="regiao_selecionada" value="<?php echo isset($_GET['regiao']) ? htmlspecialchars($_GET['regiao']) : ''; ?>">

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button id="btnUpNormal" class="btn-modelo active-tab" onclick="trocarUploadModal('normal')">Upload
                    Normal</button>
                <button id="btnUpKit" class="btn-modelo inactive-tab" onclick="trocarUploadModal('kit')">Upload de
                    Kits</button>
            </div>

            <!-- Card de envio de Arquivos -->
            <input type="file" name="arquivo" id="arquivo" accept=".pdf" multiple style="display: none;">
            <input type="file" name="arquivo_kit_pdf" id="arquivo_kit_pdf" accept=".pdf" multiple style="display: none;">
            
            <div class="upload-label" id="uploadLabelArea" style="cursor: pointer;">
                <div class="upload-div">
                    <i class="fas fa-file-upload"></i>
                    <h2>Arraste e solte ou precione para escolher o arquivo</h2>
                    <p>Tamanho máximo por arquivo: 5MB</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                        <span class="btn-selecionar" id="btnSelecionarPrincipal">Selecionar Arquivos</span>
                        <span class="btn-selecionar" id="btnSelecionarKitPdf" style="display: none;">Selecionar Arquivos PDF</span>
                    </div>
                </div>
            </div>

            <div class="enviados-section">
                <div class="enviados-titulo">
                    <h3>Enviados</h3>
                    <button type="button">Limpar Tudo</button>
                </div>
                <div class="preview-lote" id="preview-arquivos-curso">
                    <!-- Arquivos inseridos via JS -->
                </div>
            </div>

            <!-- Botão de Envio -->
            <div class="div-btn">
                <button type="button" id="btnEnviarArquivos">
                     Enviar Documentos<i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>