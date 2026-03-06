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
                        echo '<div class="tarefas-lista">';
                        echo '<table>';
                        echo '<thead><tr><th>TAREFA Nº</th><th>TAREFA</th><th>STATUS</th></tr></thead>';
                        echo '<tbody>';

                        $tarefas_na_coluna = 0;
                        if (is_dir($caminho)) {
                            $arquivos = scandir($caminho);
                            foreach ($arquivos as $arquivo) {
                                if ($arquivo !== '.' && $arquivo !== '..') {
                                    $total_tarefas++;
                                    $tarefas_na_coluna++;
                                    $caminho_relativo = "documentos/pdfs/{$regiao}/" . ($tipo === 'Kit' ? (strpos($caminho, 'upload_kits') !== false ? 'upload_kits' : 'upload_kit') : 'upload_normal') . "/{$arquivo}";
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="tarefa-checkbox">
                                                <input type="checkbox" name="tarefas[]" value="<?php echo htmlspecialchars($arquivo); ?>" onchange="updateCounter()">
                                                <span>#<?php echo $tarefas_na_coluna; ?></span>
                                            </div>
                                        </td>
                                        <td class="tarefa-nome">
                                            <a href="<?php echo $caminho_relativo; ?>" download="<?php echo htmlspecialchars($arquivo); ?>" title="Baixar PDF">
                                                <?php echo htmlspecialchars($arquivo); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="status-badge pendente">PENDENTE</span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }

                        if ($tarefas_na_coluna === 0) {
                            echo "<tr><td colspan='3' class='vazio'>Nenhuma tarefa</td></tr>";
                        }
                        echo '</tbody></table></div></div>';
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
            <h2>Upload<span id='tipo_up'></span></h2>
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
            <label class="upload-label" for="arquivo">
                <div class="upload-div">
                    <i class="fas fa-file-upload"></i>
                    <h2>Arraste e solte ou precione para escolher o arquivo</h2>
                    <p>Tamanho máximo por arquivo: 5MB</p>
                    <input type="file" name="arquivo" id="arquivo" accept=".pdf" multiple>
                    <span class="btn-selecionar">Selecionar Arquivos</span>
                </div>
            </label>

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
            <div style="margin-top: 20px; text-align: right;">
                <button type="button" id="btnEnviarArquivos" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: background-color 0.3s;">
                    <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Enviar Documentos
                </button>
            </div>
        </div>
    </div>
</div>