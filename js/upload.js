document.addEventListener('DOMContentLoaded', () => {
    // Referências dos elementos principais referenciados no HTML
    const inputFile = document.getElementById('arquivo');
    const previewContainer = document.getElementById('preview-arquivos-curso');
    const btnLimparTudo = document.querySelector('.enviados-titulo button');
    const uploadLabel = document.querySelector('.upload-label');
    const btnEnviarArquivos = document.getElementById('btnEnviarArquivos');
    const inputRegiaoHidden = document.getElementById('regiao_selecionada');
    const btnRegionDropdown = document.getElementById('btn-region');
    const regionDropdown = document.getElementById('region-dropdown');
    const selectedRegionName = document.getElementById('selected-region-name');

    // Array que armazenarão os arquivos PDF separados por tipo
    let arquivosNormal = [];
    let arquivosKit = [];

    // Controle de estado atual (começa no normal)
    let tipoUploadAtual = 'normal';

    // Persistência de Região
    const urlParams = new URLSearchParams(window.location.search);
    const regiaoUrl = urlParams.get('regiao');
    const regiaoLocal = localStorage.getItem('facchini_pcp_regiao');

    if (regiaoUrl) {
        // Se tem na URL, salva/atualiza no localStorage
        localStorage.setItem('facchini_pcp_regiao', regiaoUrl);
        if (selectedRegionName) {
            selectedRegionName.textContent = regiaoUrl;
        }
        if (inputRegiaoHidden) {
            inputRegiaoHidden.value = regiaoUrl;
        }
    } else if (regiaoLocal) {
        // Se não tem na URL, mas tem no localStorage, redireciona para a URL com a região
        const url = new URL(window.location.href);
        url.searchParams.set('regiao', regiaoLocal);
        window.location.href = url.toString();
        return; // Interrompe a execução enquanto redireciona
    } else {
        if (selectedRegionName) {
            selectedRegionName.textContent = "Sistema PCP";
        }
    }

    // Inicializa a interface
    atualizarLista();

    // Evento de seleção de arquivos utilizando o botão/modal padrão do navegador
    inputFile.addEventListener('change', (e) => {
        adicionarArquivos(Array.from(e.target.files));
        // Resetar o input para permitir selecionar o mesmo arquivo novamente se deletado
        inputFile.value = '';
    });


    // -------------------------------------------------------------
    // Drag & Drop: Eventos para soltar arquivos diretamente na tela
    // -------------------------------------------------------------
    uploadLabel.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = '#007bff'; // Estilo de destaque
        uploadLabel.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
    });

    uploadLabel.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = ''; // Remove o destaque
        uploadLabel.style.backgroundColor = '';
    });

    uploadLabel.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = '';
        uploadLabel.style.backgroundColor = '';

        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            adicionarArquivos(Array.from(e.dataTransfer.files));
        }
    });

    // Evento para esvaziar a lista atual
    btnLimparTudo.addEventListener('click', () => {
        const listaAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;
        if (listaAtual.length > 0) {
            const confirmacao = confirm(`Tem certeza que deseja limpar todos os arquivos da aba ${tipoUploadAtual === 'normal' ? 'Upload Normal' : 'Upload de Kits'}?`);
            if (confirmacao) {
                if (tipoUploadAtual === 'normal') {
                    arquivosNormal = [];
                } else {
                    arquivosKit = [];
                }
                atualizarLista();
            }
        }
    });

    // Envio dos arquivos para o backend
    btnEnviarArquivos.addEventListener('click', () => {
        const listaAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;

        if (listaAtual.length === 0) {
            alert(`Nenhum arquivo na aba ${tipoUploadAtual === 'normal' ? 'Upload Normal' : 'Upload de Kits'} para enviar.`);
            return;
        }

        const formData = new FormData();
        formData.append('tipo_upload', tipoUploadAtual);
        formData.append('regiao', inputRegiaoHidden.value);

        if (!inputRegiaoHidden.value) {
            alert('Por favor, selecione uma região na barra de navegação antes de enviar.');
            return;
        }

        listaAtual.forEach((file) => {
            formData.append('arquivos[]', file);
        });

        // Modificando texto do botão para indicar carregamento
        const textoOriginal = btnEnviarArquivos.innerHTML;
        btnEnviarArquivos.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Enviando...';
        btnEnviarArquivos.disabled = true;

        fetch('php/upload_files.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert(data.mensagem);
                    // Limpa a lista atual após envio
                    if (tipoUploadAtual === 'normal') {
                        arquivosNormal = [];
                    } else {
                        arquivosKit = [];
                    }
                    atualizarLista();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro no envio:', error);
                alert('Ocorreu um erro ao enviar os arquivos.');
            })
            .finally(() => {
                // Restaura o botão
                btnEnviarArquivos.innerHTML = textoOriginal;
                btnEnviarArquivos.disabled = false;
            });
    });

    // Função que avalia e adiciona arquivos novos ao array atual (evita duplicatas e arquivos inválidos)
    async function adicionarArquivos(novosArquivos) {
        let adicionouAlgo = false;
        let arrayAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;

        for (let file of novosArquivos) {
            // Regra: ser PDF e possuir no máximo 5MB
            if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                if (file.size <= 5 * 1024 * 1024) {
                    const jaExisteNaLista = arrayAtual.some(f => f.name === file.name);
                    if (!jaExisteNaLista) {
                        try {
                            const formData = new FormData();
                            formData.append('filename', file.name);
                            formData.append('tipo_upload', tipoUploadAtual);
                            formData.append('regiao', inputRegiaoHidden.value);

                            const response = await fetch('php/check_file.php', {
                                method: 'POST',
                                body: formData
                            });

                            const data = await response.json();

                            if (data.existe) {
                                alert(`O arquivo "${file.name}" já foi enviado para o servidor na aba ${tipoUploadAtual === 'normal' ? 'Upload Normal' : 'Upload de Kits'} e não pode ser reenviado.`);
                            } else {
                                arrayAtual.push(file);
                                adicionouAlgo = true;
                            }
                        } catch (error) {
                            console.error('Erro na checagem do arquivo:', error);
                            alert(`Falha ao checar o arquivo "${file.name}". Tente novamente.`);
                        }
                    } else {
                        alert(`O arquivo "${file.name}" já está na lista atual.`);
                    }
                } else {
                    alert(`O arquivo "${file.name}" excede o tamanho máximo de 5MB.`);
                }
            } else {
                alert(`O arquivo "${file.name}" não é um PDF válido.`);
            }
        }

        if (adicionouAlgo) {
            atualizarLista();
        }
    }

    // Função responsável por renderizar o HTML da lista de arquivos com base na aba selecionada
    function atualizarLista() {
        previewContainer.innerHTML = '';
        const listaAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;

        if (listaAtual.length === 0) {
            previewContainer.innerHTML = '<p style="text-align: center; color: #777; padding: 20px; width: 100%;">Nenhum PDF selecionado nesta aba.</p>';
            previewContainer.style.overflowY = 'visible';
            previewContainer.style.maxHeight = 'auto';
            return;
        }

        // Aplicação do Scroll para muitos arquivos
        previewContainer.style.maxHeight = '280px';
        previewContainer.style.overflowY = 'auto';
        previewContainer.style.overflowX = 'hidden';

        listaAtual.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'arquivo-item';

            // Adicionado formatação flex no arquivo-item garantindo o visual
            item.style.display = 'flex';
            item.style.justifyContent = 'space-between';
            item.style.alignItems = 'center';
            item.style.padding = '12px';
            item.style.marginBottom = '8px';
            item.style.border = '1px solid #e1e1e1';
            item.style.borderRadius = '5px';
            item.style.backgroundColor = '#fafafa';

            // Formatação do tamanho
            const fileSizeInfo = (file.size / 1024 / 1024).toFixed(2);
            const sizeText = fileSizeInfo < 1 ? (file.size / 1024).toFixed(2) + ' KB' : fileSizeInfo + ' MB';

            item.innerHTML = `
                <div class="arquivo-info" style="display: flex; align-items: center; gap: 12px; max-width: 85%;">
                    <div class="icon-bg" style="color: #d93025; font-size: 24px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(217, 48, 37, 0.1); border-radius: 5px;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="arquivo-textos" style="display: flex; flex-direction: column; overflow: hidden;">
                        <strong title="${file.name}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; font-size: 14px; color: #333;">${file.name}</strong>
                        <span style="font-size: 12px; color: #777; margin-top: 2px;">${sizeText}</span>
                    </div>
                </div>
                <div class="arquivo-actions">
                    <button type="button" class="remover-arquivo" data-index="${index}" style="background: none; border: none; color: #d93025; cursor: pointer; font-size: 16px; transition: 0.2s;" title="Remover Arquivo">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>
            `;

            previewContainer.appendChild(item);
        });

        // Habilita as lixeiras de exclusão individual
        const botoesRemover = previewContainer.querySelectorAll('.remover-arquivo');
        botoesRemover.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.currentTarget.getAttribute('data-index');
                removerArquivo(index);
            });
            // Efeito de hover via JS apenas para dar um toque extra
            btn.addEventListener('mouseenter', (e) => e.target.style.color = '#ff0000');
            btn.addEventListener('mouseleave', (e) => e.target.style.color = '#d93025');
        });
    }

    // Função para deletar um único registro do array atual
    function removerArquivo(index) {
        if (tipoUploadAtual === 'normal') {
            arquivosNormal.splice(index, 1);
        } else {
            arquivosKit.splice(index, 1);
        }
        atualizarLista();
    }

    // Controle de abas "Upload Normal" e "Upload de Kits" internalizado
    window.trocarUploadModal = function (tipo) {
        tipoUploadAtual = tipo; // Atualiza a variável de estado

        const btnNormal = document.getElementById('btnUpNormal');
        const btnKit = document.getElementById('btnUpKit');

        if (tipo === 'normal') {
            if (btnNormal) {
                btnNormal.classList.add('active-tab');
                btnNormal.classList.remove('inactive-tab');
            }
            if (btnKit) {
                btnKit.classList.add('inactive-tab');
                btnKit.classList.remove('active-tab');
            }
        } else {
            if (btnKit) {
                btnKit.classList.add('active-tab');
                btnKit.classList.remove('inactive-tab');
            }
            if (btnNormal) {
                btnNormal.classList.add('inactive-tab');
                btnNormal.classList.remove('active-tab');
            }
        }
        // Atualiza a visualização da lista conforme a nova aba
        atualizarLista();
    };

    // --- Lógica do Dropdown de Regiões ---
    if (btnRegionDropdown) {
        btnRegionDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            regionDropdown.classList.toggle('active');
        });
    }

    // Fechar dropdown ao clicar fora
    document.addEventListener('click', () => {
        if (regionDropdown) regionDropdown.classList.remove('active');
    });

    // Seleção de região
    const regionOptions = document.querySelectorAll('.region-option');
    regionOptions.forEach(option => {
        option.addEventListener('click', () => {
            const regiao = option.getAttribute('data-region');
            
            // Redireciona atualizando URL com GET
            const url = new URL(window.location.href);
            url.searchParams.set('regiao', regiao);
            window.location.href = url.toString();
        });
    });
});

// Fecha Modais
window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
};
