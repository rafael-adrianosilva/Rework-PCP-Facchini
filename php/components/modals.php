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
            <div class="tarefas">
                <table>
                    <thead>
                        <th>Tarefa N°</th>
                        <th>Tarefa</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div>
                                    <input type="checkbox" name="" id="">#1
                                </div>
                            </td>
                            <td>Text Aqui</td>
                            <td class="aguardando">Status Aqui</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <p>0 Tarefas selecionadas.</p>
            <button onclick="checkTarefa()">MARCAR COMO FEITO</button>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal-fundo" id="upTarefas" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Upload</h2>
            <button type="button" onclick="closeModal('upTarefas')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- Campo oculto para armazenar a região selecionada via Navbar -->
            <input type="hidden" name="regiao" id="regiao_selecionada" value="">

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
                    <p class="btn-selecionar">Selecionar Arquivos</p>
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