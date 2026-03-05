window.onload = () => {
    // Se tiver tema no LocalStorage eu pego ele se não coloca "claro" mesmo
    let tema = localStorage.getItem('fafcchini_pcp_tema') || 'claro';

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
