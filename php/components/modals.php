<!-- Modal Gerenciar Tarefas -->
<div class="modal-fundo" id="gerTarefas" style="display: none;">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <h2>GERENCIAR TAREFAS</h2>
            <button type="button" onclick="closeModal('gerTarefas')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-subheader">
                <p>Arquivos enviados para o servidor. Use os filtros para navegar entre os tipos.</p>
            </div>

            <!-- Botões de Filtro -->
            <div class="filtros-arquivo" id="filtrosArquivo">
                <button class="btn-filtro active-filtro" data-filtro="todos" onclick="filtrarArquivos('todos')">
                    <i class="fas fa-layer-group"></i> Todos
                </button>
                <button class="btn-filtro" data-filtro="upload_normal" onclick="filtrarArquivos('upload_normal')">
                    <i class="fas fa-file-pdf"></i> Upload Normal
                </button>
                <button class="btn-filtro" data-filtro="upload_kit" onclick="filtrarArquivos('upload_kit')">
                    <i class="fas fa-boxes"></i> Upload Kit
                </button>
                <button class="btn-filtro btn-refresh" onclick="carregarArquivos()" title="Atualizar lista">
                    <i class="fas fa-sync-alt" id="iconRefresh"></i>
                </button>
            </div>

            <!-- Tabela de Arquivos -->
            <div class="tarefas">
                <table id="tabelaArquivos">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nome do Arquivo</th>
                            <th>Tipo</th>
                            <th>Data de Envio</th>
                            <th>Tamanho</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabelaArquivos">
                        <!-- Populado via JS -->
                        <tr id="linhaLoading">
                            <td colspan="5" class="tabela-estado">
                                <i class="fas fa-spinner fa-spin"></i> Carregando arquivos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <p id="contadorArquivos">0 arquivo(s) encontrado(s).</p>
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