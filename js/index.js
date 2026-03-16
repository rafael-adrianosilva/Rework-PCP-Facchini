window.onload = () => {
    // Se tiver tema no LocalStorage eu pego ele se não coloca "claro" mesmo
    let tema = localStorage.getItem('facchini_pcp_tema') || 'claro';

    // Definindo atributo "data-tema" com valor da minha variavel tema
    document.documentElement.setAttribute("data-tema", tema);

    // Isso é para mudar o icone dos btns, mas tive que colocar uma condicional para verificar se eles existem ou não
    let btnTema = document.getElementById("tema");
    if (tema == 'escuro') {
        if (btnTema != undefined) {
            document.getElementById("tema").innerHTML = '<i class="fas fa-moon"></i>';
        }
    } else {
        if (btnTema != undefined) {
            document.getElementById("tema").innerHTML = '<i class="fas fa-sun"></i>';
        }
    }
}

function changeTheme() {
    let tema_atual = document.documentElement.getAttribute("data-tema")

    if (tema_atual == "escuro") {
        localStorage.removeItem('facchini_pcp_tema');
        document.documentElement.setAttribute("data-tema", "claro");
        document.getElementById("tema").innerHTML = '<i class="fas fa-sun"></i>';
        localStorage.setItem('facchini_pcp_tema', 'claro');
    } else {
        localStorage.removeItem('facchini_pcp_tema');
        document.documentElement.setAttribute("data-tema", "escuro");
        document.getElementById("tema").innerHTML = '<i class="fas fa-moon"></i>';
        localStorage.setItem('facchini_pcp_tema', 'escuro');
    }
}

//Função para abrir Modals
function showModal(qualModal) {
    const modal = document.getElementById(qualModal);
    if (modal) {
        modal.style.display = "flex";
    }
}

//Função para fechar Modals
function closeModal(qualModal) {
    const modal = document.getElementById(qualModal);
    if (modal) {
        modal.style.display = "none";
    }
}

// Funções para exibir mensagens de sucesso e erro
function exibirSucesso(mensagem) {
    const msgElement = document.getElementById('sucesso-msg');
    if (msgElement) {
        msgElement.innerText = mensagem;
        showModal('sucesso');
    }
}

function exibirErro(mensagem) {
    const msgElement = document.getElementById('error-msg');
    if (msgElement) {
        msgElement.innerText = mensagem;
        showModal('error');
    }
}

//Função para mudar o tipo de upload (tem que finalizar @Gideão)
function trocarUploadModal(tipo) {
    const btnUpNormal = document.getElementById('btnUpNormal');
    const btnUpKit = document.getElementById('btnUpKit');

    if (tipo === 'normal') {
        btnUpNormal.className = "btn-modelo active-tab";
        btnUpKit.className = "btn-modelo inactive-tab";
    } else {
        btnUpKit.className = "btn-modelo active-tab";
        btnUpNormal.className = "btn-modelo inactive-tab";
    }
}

function trocarPagina(){
    var pageAtual = window.location.href

    if(pageAtual.includes('uploads.php')){
        window.location.href = 'index.php'
    } else {
        window.location.href = 'uploads.php'
    }
}

// Lógica para a barra de pesquisa de PDFs e Pastas
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.listagem-pesquisa input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const termo = this.value.toLowerCase().trim();
            
            // Seleciona todas as colunas de upload
            const colunas = document.querySelectorAll('.upload-coluna-body');
            
            colunas.forEach(coluna => {
                const filhos = coluna.children;
                
                for (let i = 0; i < filhos.length; i++) {
                    const elem = filhos[i];
                    
                    // Se for um arquivo solto
                    if (elem.classList.contains('arquivo-card') && !elem.classList.contains('pasta-card')) {
                        const nomeElem = elem.querySelector('.arquivo-card-nome');
                        if (nomeElem) {
                            const nome = nomeElem.innerText.toLowerCase();
                            if (nome.includes(termo)) {
                                elem.style.display = 'flex';
                            } else {
                                elem.style.display = 'none';
                            }
                        }
                    }
                    
                    // Se for uma pasta
                    if (elem.classList.contains('pasta-card')) {
                        const pastaNomeElem = elem.querySelector('.arquivo-card-nome');
                        const pastaNome = pastaNomeElem ? pastaNomeElem.innerText.toLowerCase() : '';
                        const conteudo = elem.nextElementSibling; // div.pasta-conteudo
                        let temFilhoCorrespondente = false;
                        
                        if (conteudo && conteudo.classList.contains('pasta-conteudo')) {
                            const arquivosAninhados = conteudo.querySelectorAll('.arquivo-card');
                            arquivosAninhados.forEach(arq => {
                                const arqNomeElem = arq.querySelector('.arquivo-card-nome');
                                if (arqNomeElem) {
                                    const arqNome = arqNomeElem.innerText.toLowerCase();
                                    if (arqNome.includes(termo)) {
                                        arq.style.display = 'flex';
                                        temFilhoCorrespondente = true;
                                    } else {
                                        arq.style.display = 'none';
                                    }
                                }
                            });
                        }
                        
                        // Mostra a pasta se o nome da pasta bater ou algum filho bater
                        if (pastaNome.includes(termo) || temFilhoCorrespondente) {
                            elem.style.display = 'flex';
                            
                            // Se estiver pesquisando, abre a pasta para ver o filho que encontrou
                            if (termo !== '' && temFilhoCorrespondente) {
                                conteudo.style.display = 'flex';
                                const chevron = elem.querySelector('.pasta-chevron');
                                if (chevron) chevron.classList.add('aberto');
                            } else if (termo === '') {
                                // Se limpou a pesquisa, fecha as pastas
                                conteudo.style.display = 'none';
                                const chevron = elem.querySelector('.pasta-chevron');
                                if (chevron) chevron.classList.remove('aberto');
                            }
                        } else {
                            elem.style.display = 'none';
                            if (conteudo) conteudo.style.display = 'none';
                        }
                    }
                }
            });
        });
    }
});