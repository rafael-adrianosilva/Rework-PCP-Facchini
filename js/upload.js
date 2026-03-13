document.addEventListener('DOMContentLoaded', () => {
    const inputFile = document.getElementById('arquivo');
    const inputKitPdf = document.getElementById('arquivo_kit_pdf');
    const previewContainer = document.getElementById('preview-arquivos-curso');
    const btnLimparTudo = document.querySelector('.enviados-titulo button');
    const uploadLabel = document.getElementById('uploadLabelArea');
    const btnEnviarArquivos = document.getElementById('btnEnviarArquivos');
    const inputRegiaoHidden = document.getElementById('regiao_selecionada');
    const btnRegionDropdown = document.getElementById('btn-region');
    const regionDropdown = document.getElementById('region-dropdown');
    const selectedRegionName = document.getElementById('selected-region-name');
    const btnSelecionarPrincipal = document.getElementById('btnSelecionarPrincipal');
    const btnSelecionarKitPdf = document.getElementById('btnSelecionarKitPdf');

    let arquivosNormal = [];
    let arquivosKit = [];

    let tipoUploadAtual = 'normal';

    const urlParams = new URLSearchParams(window.location.search);
    const regiaoUrl = urlParams.get('regiao');
    const regiaoLocal = localStorage.getItem('facchini_pcp_regiao');

    if (regiaoUrl) {
        localStorage.setItem('facchini_pcp_regiao', regiaoUrl);
        if (selectedRegionName) {
            selectedRegionName.textContent = regiaoUrl;
        }
        if (inputRegiaoHidden) {
            inputRegiaoHidden.value = regiaoUrl;
        }
    } else if (regiaoLocal) {
        const url = new URL(window.location.href);
        url.searchParams.set('regiao', regiaoLocal);
        window.location.href = url.toString();
        return;
    } else {
        if (selectedRegionName) {
            selectedRegionName.textContent = "Sistema PCP";
        }
    }

    atualizarLista();

    inputFile.addEventListener('change', (e) => {
        adicionarArquivos(Array.from(e.target.files));
        inputFile.value = '';
    });

    // Listener para o input de PDFs avulsos no modo Kit
    if (inputKitPdf) {
        inputKitPdf.addEventListener('change', (e) => {
            adicionarArquivos(Array.from(e.target.files));
            inputKitPdf.value = '';
        });
    }

    // Gerenciador de cliques para os botões e área de upload
    if (btnSelecionarPrincipal) {
        btnSelecionarPrincipal.addEventListener('click', (e) => {
            e.stopPropagation();
            if (inputFile) inputFile.click();
        });
    }

    if (btnSelecionarKitPdf) {
        btnSelecionarKitPdf.addEventListener('click', (e) => {
            e.stopPropagation();
            if (inputKitPdf) inputKitPdf.click();
        });
    }

    if (uploadLabel) {
        uploadLabel.addEventListener('click', (e) => {
            // Se clicar na área do card (fora dos botões), aciona o seletor principal
            if (inputFile) inputFile.click();
        });
    }


    uploadLabel.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = '#007bff';
        uploadLabel.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
    });

    uploadLabel.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = '';
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

    btnEnviarArquivos.addEventListener('click', () => {
        const listaAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;

        if (listaAtual.length === 0) {
            exibirErro(`Nenhum arquivo na aba ${tipoUploadAtual === 'normal' ? 'Upload Normal' : 'Upload de Kits'} para enviar.`);
            return;
        }

        const formData = new FormData();
        formData.append('tipo_upload', tipoUploadAtual);
        formData.append('regiao', inputRegiaoHidden.value);

        if (!inputRegiaoHidden.value) {
            exibirErro('Por favor, selecione uma região na barra de navegação antes de enviar.');
            return;
        }

        listaAtual.forEach((file) => {
            formData.append('arquivos[]', file);
            // Enviar caminho relativo para preservar estrutura de pastas em kits
            if (tipoUploadAtual === 'kit' && file.webkitRelativePath) {
                formData.append('caminhos[]', file.webkitRelativePath);
            } else {
                formData.append('caminhos[]', file.name);
            }
        });

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
                    exibirSucesso(data.mensagem);

                    if (tipoUploadAtual === 'normal') {
                        arquivosNormal = [];
                    } else {
                        arquivosKit = [];
                    }
                    atualizarLista();
                    // Recarregar página para atualizar a listagem de arquivos
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    exibirErro('Erro: ' + data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro no envio:', error);
                exibirErro('Ocorreu um erro ao enviar os arquivos.');
            })
            .finally(() => {

                btnEnviarArquivos.innerHTML = textoOriginal;
                btnEnviarArquivos.disabled = false;
            });
    });

    function adicionarArquivos(novosArquivos) {
        let adicionouAlgo = false;
        let arrayAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;

        for (let file of novosArquivos) {
            if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                if (file.size <= 5 * 1024 * 1024) {
                    // Se já existe na lista, substitui
                    const indexExistente = arrayAtual.findIndex(f => f.name === file.name);
                    if (indexExistente !== -1) {
                        arrayAtual[indexExistente] = file;
                    } else {
                        arrayAtual.push(file);
                    }
                    adicionouAlgo = true;
                } else {
                    exibirErro(`O arquivo "${file.name}" excede o tamanho máximo de 5MB.`);
                }
            } else {
                exibirErro(`O arquivo "${file.name}" não é um PDF válido.`);
            }
        }

        if (adicionouAlgo) {
            atualizarLista();
        }
    }

    function atualizarLista() {
        previewContainer.innerHTML = '';
        const listaAtual = tipoUploadAtual === 'normal' ? arquivosNormal : arquivosKit;

        if (listaAtual.length === 0) {
            previewContainer.innerHTML = '<p style="text-align: center; color: var(--corTxt3); padding: 20px; width: 100%;">Nenhum PDF selecionado nesta aba.</p>';
            previewContainer.style.overflowY = 'visible';
            previewContainer.style.maxHeight = 'auto';
            return;
        }

        previewContainer.style.maxHeight = '280px';
        previewContainer.style.overflowY = 'auto';
        previewContainer.style.overflowX = 'hidden';

        listaAtual.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'arquivo-item';

            item.style.display = 'flex';
            item.style.justifyContent = 'space-between';
            item.style.alignItems = 'center';
            item.style.padding = '12px';
            item.style.marginBottom = '8px';
            item.style.border = '1px solid var(--corBordas)';
            item.style.borderRadius = '5px';
            item.style.backgroundColor = 'var(--corFundo)';

            const fileSizeInfo = (file.size / 1024 / 1024).toFixed(2);
            const sizeText = fileSizeInfo < 1 ? (file.size / 1024).toFixed(2) + ' KB' : fileSizeInfo + ' MB';

            item.innerHTML = `
                <div class="arquivo-info" style="display: flex; align-items: center; gap: 12px; max-width: 85%;">
                    <div class="icon-bg" style="color: var(--corBase); font-size: 24px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(var(--corTxt1), 0.1); border-radius: 5px;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="arquivo-textos" style="display: flex; flex-direction: column; overflow: hidden;">
                        <strong title="${file.name}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; font-size: 14px; color: var(--corTxt3);">${file.name}</strong>
                        <span style="font-size: 12px; color: var(--corTxt3); margin-top: 2px;">${sizeText}</span>
                    </div>
                </div>
                <div class="arquivo-actions">
                    <button type="button" class="remover-arquivo" data-index="${index}" style="background: none; border: none; color: var(--corTxt3); cursor: pointer; font-size: 16px; transition: 0.2s;" title="Remover Arquivo">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>
            `;

            previewContainer.appendChild(item);
        });

        const botoesRemover = previewContainer.querySelectorAll('.remover-arquivo');
        botoesRemover.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.currentTarget.getAttribute('data-index');
                removerArquivo(index);
            });
            btn.addEventListener('mouseenter', (e) => e.target.style.color = '#ff0000');
            btn.addEventListener('mouseleave', (e) => e.target.style.color = '#d93025');
        });
    }

    function removerArquivo(index) {
        if (tipoUploadAtual === 'normal') {
            arquivosNormal.splice(index, 1);
        } else {
            arquivosKit.splice(index, 1);
        }
        atualizarLista();
    }

    window.trocarUploadModal = function (tipo) {
        tipoUploadAtual = tipo;

        const btnNormal = document.getElementById('btnUpNormal');
        const btnKit = document.getElementById('btnUpKit');
        const inputFile = document.getElementById('arquivo');

        if (tipo === 'normal') {
            if (btnNormal) {
                btnNormal.classList.add('active-tab');
                btnNormal.classList.remove('inactive-tab');
                document.getElementById("tipo_up").innerText = " Normal";
            }
            if (btnKit) {
                btnKit.classList.add('inactive-tab');
                btnKit.classList.remove('active-tab');
            }
            // Para upload normal, desativa seleção de pasta
            if (inputFile) {
                inputFile.removeAttribute('webkitdirectory');
                inputFile.removeAttribute('directory');
                inputFile.setAttribute('multiple', '');
            }
            // Mostrar apenas botão principal com texto padrão
            if (btnSelecionarPrincipal) {
                btnSelecionarPrincipal.textContent = 'Selecionar Arquivos';
                btnSelecionarPrincipal.style.display = '';
            }
            if (btnSelecionarKitPdf) {
                btnSelecionarKitPdf.style.display = 'none';
            }
        } else {
            if (btnKit) {
                btnKit.classList.add('active-tab');
                btnKit.classList.remove('inactive-tab');
                document.getElementById("tipo_up").innerText = " de Kits";
            }
            if (btnNormal) {
                btnNormal.classList.add('inactive-tab');
                btnNormal.classList.remove('active-tab');
            }
            // Para upload de kits, ativa seleção de pasta no input principal
            if (inputFile) {
                inputFile.setAttribute('webkitdirectory', '');
                inputFile.setAttribute('directory', '');
                inputFile.setAttribute('multiple', '');
            }
            // Mostrar dois botões: pasta e PDF avulso
            if (btnSelecionarPrincipal) {
                btnSelecionarPrincipal.textContent = 'Selecionar Pasta';
                btnSelecionarPrincipal.style.display = '';
            }
            if (btnSelecionarKitPdf) {
                btnSelecionarKitPdf.style.display = '';
            }
        }
        atualizarLista();
    };

    if (btnRegionDropdown) {
        btnRegionDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            regionDropdown.classList.toggle('active');
        });
    }

    document.addEventListener('click', () => {
        if (regionDropdown) regionDropdown.classList.remove('active');
    });

    const regionOptions = document.querySelectorAll('.region-option');
    regionOptions.forEach(option => {
        option.addEventListener('click', () => {
            const regiao = option.getAttribute('data-region');

            const url = new URL(window.location.href);
            url.searchParams.set('regiao', regiao);
            window.location.href = url.toString();
        });
    });
});

