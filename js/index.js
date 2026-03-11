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
