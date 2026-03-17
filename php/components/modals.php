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

$tipos_diretorios = ['Normal', 'Kit'];

if (!empty($regiao)) {
    $base_pcp = dirname(__DIR__, 2);

    foreach ($tipos_diretorios as $tipo) {
        $titulo = ($tipo === 'Normal') ? 'UPLOAD NORMAL' : 'UPLOAD KIT';
        $pasta = ($tipo === 'Normal') ? 'upload_normal' : 'upload_kits';
        
        $caminho = $base_pcp . "/documentos/pdfs/{$regiao}/" . $pasta;

        // Fallback para 'upload_kit' no singular
        if ($tipo === 'Kit' && !is_dir($caminho)) {
            $caminho = $base_pcp . "/documentos/pdfs/{$regiao}/upload_kit";
        }

        echo '<div class="tarefas-coluna">';
        echo '<h3>' . $titulo . '</h3>';
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

                    echo '                    <div class="tarefa-card">';
                    echo '                        <div class="tarefa-info">';
                    echo '                            <div class="tarefa-checkbox">';
                    echo '                                <input type="checkbox" name="tarefas[]" value="' . htmlspecialchars($arquivo) . '" onchange="updateCounter()">';
                    echo '                            </div>';
                    echo '                            <div class="tarefa-icon">';
                    echo '                                <i class="fas ' . ($is_dir ? 'fa-folder' : 'fa-file-pdf') . '"></i>';
                    echo '                            </div>';
                    echo '                            <div class="tarefa-detalhes">';
                    echo '                                <a href="' . $caminho_relativo . '" class="tarefa-nome" ' . ($is_dir ? '' : 'download="' . htmlspecialchars($arquivo) . '"') . '>';
                    echo '                                    ' . htmlspecialchars($arquivo);
                    echo '                                </a>';
                    echo '                                <div class="tarefa-meta">';
                    echo '                                    <span>#' . $tarefas_na_coluna . '</span>';
                    echo '                                </div>';
                    echo '                            </div>';
                    echo '                        </div>';
                    echo '                        <div class="tarefa-acoes">';
                    echo '                            <span class="status-badge pendente">PENDENTE</span>';
                    echo '                        </div>';
                    echo '                    </div>';
                }
            }
        }

        if ($tarefas_na_coluna === 0) {
            echo "<div class='vazio'>Nenhuma tarefa encontrada</div>";
        }
        echo '</div></div>';
    }
}
else {
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
                <button id="btnUpNormal" class="btn-modelo active-tab" onclick="trocarUploadModal('normal')">Upload Normal</button>
                <button id="btnUpKit" class="btn-modelo inactive-tab" onclick="trocarUploadModal('kit')">Upload de Kits</button>
            </div>

            <!-- Seleção de Kit de Destino (só mostra no upload de kit) -->
            <div id="areaSelecaoKit" class="area-selecao-kit">
                <label for="selectPastaKit">Selecione o Kit de Destino:</label>
                <div class="agrupamento-select-kit">
                    <select id="selectPastaKit" class="select-kit">
                        <option value="">Selecione ou Crie um Kit</option>
                        <?php
$reg = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';
if (!empty($reg)) {
    $b_pcp = dirname(__DIR__, 2);
    $cKits = $b_pcp . "/documentos/pdfs/{$reg}/upload_kits";
    if (is_dir($cKits)) {
        $pKits = scandir($cKits);
        foreach ($pKits as $pkt) {
            if ($pkt !== '.' && $pkt !== '..' && is_dir($cKits . '/' . $pkt)) {
                echo '<option value="' . htmlspecialchars($pkt) . '">' . htmlspecialchars($pkt) . '</option>';
            }
        }
    }
}
?>
                    </select>
                    <button type="button" title="Criar Novo Kit" onclick="showModal('criarKitModal')" class="btn-criar-kit"><i class="fas fa-folder-plus"></i></button>
                    <button type="button" title="Excluir Kit Selecionado" id="btnExcluirKit" class="btn-excluir-kit"><i class="fas fa-trash-alt"></i></button>
                </div>
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

<!-- Modal Criar Kit -->
<div class="modal-fundo" id="criarKitModal" style="display: none; z-index: 1000;">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Novo Kit de PDF</h2>
            <button type="button" onclick="closeModal('criarKitModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label for="nomeNovaPastaKit" style="color: var(--corTxt3); font-weight: bold; font-size: 14px;">Nome do Kit (Pasta):</label>
                <input type="text" id="nomeNovaPastaKit" placeholder="Ex: Kit Motor" style="padding: 10px; border-radius: 5px; border: 1px solid var(--corBordas); width: 100%; background: var(--corFundo); color: var(--corTxt3); outline: none;">
            </div>
            <div class="div-btn" style="margin-top: 20px;">
                <button type="button" id="btnSalvarNovoKit" class="btn-criar-kit" style="width: 100%; padding: 10px; font-weight: bold; font-size: 15px; gap: 8px;">
                     Criar Kit <i class="fas fa-folder-plus"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sucesso -->
<div class="modal-fundo" id="sucesso" style="display: none;">
    <div class="modal-box" id="sucesso-box">
        <div class="modal-header">
            <h5 id="sucesso-txt">SUCESSO</h5>
            <button type="button" onclick="closeModal('sucesso')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p id="sucesso-msg" style="font-size: var(--text-sm);">
                Operação realizada com sucesso.
            </p>
        </div>
    </div>
</div>

<!-- Modal Erro -->
<div class="modal-fundo" id="error" style="display: none;">
    <div class="modal-box" id="error-box">
        <div class="modal-header">
            <h5 id="error-txt">ERRO</h5>
            <button type="button" onclick="closeModal('error')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p id="error-msg" style="font-size: var(--text-sm);">
                Ocorreu um erro ao processar a solicitação.
            </p>
        </div>
    </div>
</div>

<!-- Modal Confirmação -->
<div class="modal-fundo" id="confirmacao" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2>CONFIRMAR AÇÃO</h2>
            <button type="button" onclick="closeModal('confirmacao')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p id="confirmacao-msg" style="padding: 10px 0; color: var(--corTxt3);">
                Você tem certeza que deseja realizar esta ação?
            </p>
        </div>
        <div class="modal-footer" style="margin-top: 15px; gap: 10px; display: flex; justify-content: space-between;">
            <button type="button" class="btn-modelo inactive-tab" onclick="closeModal('confirmacao')" style="flex: 1; padding: 12px; border-radius: 8px;">CANCELAR</button>
            <button type="button" id="confirmar-btn-sim" class="btn-modelo active-tab" style="flex: 1; padding: 12px; border-radius: 8px;">SIM, CONTINUAR</button>
        </div>
    </div>
</div>
