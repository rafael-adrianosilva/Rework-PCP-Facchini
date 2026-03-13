<nav class="navbar">
    <div class="navbar-img">
        <img onclick="trocarPagina()" src="assets/imgs/logo_facchini.png" alt="Logo Facchini">
    </div>
    <div class="navbar-items">
        <div class="region-selector">
            <button id="btn-region" class="nav-btn" title="Selecionar Região">
                <i class="fas fa-globe"></i>
                <span id="selected-region-name"><?php echo isset($_GET['regiao']) ? htmlspecialchars($_GET['regiao']) : 'Sistema PCP'; ?></span>
            </button>
            <div class="region-dropdown" id="region-dropdown">
                <div class="region-option" data-region="Votuporanga">Votuporanga</div>
                <div class="region-option" data-region="Rio Preto 1">Rio Preto 1</div>
                <div class="region-option" data-region="Rio Preto 2">Rio Preto 2</div>
                <div class="region-option" data-region="Roseira">Roseira</div>
                <div class="region-option" data-region="Mirassol">Mirassol</div>
                <div class="region-option" data-region="Aparecida Taboado">Aparecida Taboado</div>
            </div>
        </div>
        <button id="tema" onclick="changeTheme()"><i class="fas fa-moon"></i></button>
    </div>
</nav>