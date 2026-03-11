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
    switch (qualModal) {
        case 'gerTarefas':
            document.getElementById(qualModal).style.display = "flex";
            carregarArquivos(); // Carrega os arquivos ao abrir a modal
            break;
        case 'upTarefas':
            document.getElementById(qualModal).style.display = "flex";
            break;
        default:
            break;
    }
}

//Função para fechar Modals
function closeModal(qualModal) {
    switch (qualModal) {
        case 'gerTarefas':
            document.getElementById(qualModal).style.display = "none";
            break;
        case 'upTarefas':
            document.getElementById(qualModal).style.display = "none";
            break;
        default:
            break;
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

// -----------------------------------------------------------
// Gerenciar Tarefas — Listagem e Filtro de Arquivos
// -----------------------------------------------------------

// Cache dos arquivos carregados
let _todosArquivos = [];
let _filtroAtivo = 'todos';

/**
 * Busca os arquivos no servidor e renderiza a tabela.
 */
function carregarArquivos() {
    const corpo = document.getElementById('corpoTabelaArquivos');
    const contador = document.getElementById('contadorArquivos');
    const iconRef = document.getElementById('iconRefresh');

    // Animação de loading
    if (iconRef) iconRef.classList.add('fa-spin');

    corpo.innerHTML = `
        <tr>
            <td colspan="5" class="tabela-estado">
                <i class="fas fa-spinner fa-spin"></i> Carregando arquivos...
            </td>
        </tr>`;

    fetch('php/listar_arquivos.php')
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                _todosArquivos = data.arquivos || [];
                renderizarTabela(_filtroAtivo);
            } else {
                corpo.innerHTML = `<tr><td colspan="5" class="tabela-estado tabela-vazia">
                    <i class="fas fa-exclamation-circle"></i> Erro ao carregar arquivos.
                </td></tr>`;
            }
        })
        .catch(() => {
            corpo.innerHTML = `<tr><td colspan="5" class="tabela-estado tabela-vazia">
                <i class="fas fa-wifi-slash"></i> Não foi possível conectar ao servidor.
            </td></tr>`;
        })
        .finally(() => {
            if (iconRef) iconRef.classList.remove('fa-spin');
        });
}

/**
 * Filtra e exibe os arquivos conforme o tipo selecionado.
 * @param {string} tipo - 'todos', 'upload_normal' ou 'upload_kit'
 */
function filtrarArquivos(tipo) {
    _filtroAtivo = tipo;

    // Atualiza classes dos botões de filtro
    document.querySelectorAll('.btn-filtro[data-filtro]').forEach(btn => {
        btn.classList.toggle('active-filtro', btn.dataset.filtro === tipo);
    });

    renderizarTabela(tipo);
}

/**
 * Renderiza as linhas da tabela com base no filtro.
 * @param {string} tipo
 */
function renderizarTabela(tipo) {
    const corpo = document.getElementById('corpoTabelaArquivos');
    const contador = document.getElementById('contadorArquivos');

    const lista = tipo === 'todos'
        ? _todosArquivos
        : _todosArquivos.filter(a => a.tipo === tipo);

    contador.textContent = `${lista.length} arquivo(s) encontrado(s).`;

    if (lista.length === 0) {
        corpo.innerHTML = `
            <tr>
                <td colspan="5" class="tabela-estado tabela-vazia">
                    <i class="fas fa-folder-open"></i>
                    <span>Nenhum arquivo encontrado${tipo !== 'todos' ? ' nesta categoria' : ''}.</span>
                </td>
            </tr>`;
        return;
    }

    corpo.innerHTML = lista.map((arq, idx) => {
        const badge = arq.tipo === 'upload_kit'
            ? '<span class="badge-tipo badge-kit"><i class="fas fa-boxes"></i> Kit</span>'
            : '<span class="badge-tipo badge-normal"><i class="fas fa-file-pdf"></i> Normal</span>';

        return `
            <tr>
                <td>${idx + 1}</td>
                <td class="nome-arquivo" title="${arq.nome}">
                    <i class="fas fa-file-pdf" style="color:#d93025; margin-right:6px;"></i>${arq.nome}
                </td>
                <td>${badge}</td>
                <td>${arq.data}</td>
                <td>${formatarTamanho(arq.tamanho)}</td>
            </tr>`;
    }).join('');
}

/**
 * Formata bytes em KB ou MB legível.
 * @param {number} bytes
 * @returns {string}
 */
function formatarTamanho(bytes) {
    if (bytes >= 1024 * 1024) {
        return (bytes / 1024 / 1024).toFixed(2) + ' MB';
    }
    return (bytes / 1024).toFixed(1) + ' KB';
}
// Função para atualizar o contador de tarefas selecionadas
function atualizarContagemTarefas() {
    const container = document.querySelector("#gerTarefas");
    if (container) {
        const selecionadas = container.querySelectorAll('.tarefas tbody input[type="checkbox"]:checked').length;
        const pQuantidade = container.querySelector(".modal-footer p");
        if (pQuantidade) {
            pQuantidade.textContent = selecionadas + (selecionadas === 1 ? " Tarefa selecionada." : " Tarefas selecionadas.");
        }
    }
}

// Event listener para atualizar a contagem toda vez que um checkbox for clicado na tabela
document.addEventListener("change", function(e) {
    if (e.target.matches('#gerTarefas .tarefas tbody input[type="checkbox"]')) {
        atualizarContagemTarefas();
    }
});

// Função chamada ao clicar em MARCAR COMO FEITO
function checkTarefa() {
    const container = document.querySelector("#gerTarefas");
    const checkboxes = container.querySelectorAll('.tarefas tbody input[type="checkbox"]');
    let alteradas = 0;

    checkboxes.forEach(chk => {
        if (chk.checked) {
            const linha = chk.closest("tr");
            const statusTd = linha.querySelectorAll("td")[2]; // 3ª coluna
            
            // Alterando o status visualmente
            statusTd.textContent = "Concluída";
            statusTd.className = "concluida";
            statusTd.style.color = "#28a745"; // Cor verde
            statusTd.style.fontWeight = "bold";
            
            // Desmarca o checkbox
            chk.checked = false;
            alteradas++;
        }
    });

    if (alteradas > 0) {
        atualizarContagemTarefas();
    } else {
        alert("Selecione pelo menos uma tarefa para marcar como feita.");
    }
}
